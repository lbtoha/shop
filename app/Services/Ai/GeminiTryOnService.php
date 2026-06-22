<?php

namespace App\Services\Ai;

use App\Exceptions\CustomWebException;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * AI virtual try-on powered by Google Gemini (Nano Banana image model).
 *
 * Sends the customer's photo plus a product image to Gemini's generateContent
 * endpoint with a try-on prompt, and saves the generated image to the public
 * disk under `tryon/`. The customer photo is never persisted server-side beyond
 * the request; only the generated result is stored (and auto-cleaned later).
 */
class GeminiTryOnService
{
    private const ENDPOINT = 'https://generativelanguage.googleapis.com/v1/models/%s:generateContent';

    /** Where generated results live on the public disk (auto-cleaned by schedule). */
    public const RESULT_DIR = 'tryon';

    /** Admin enabled the feature and a key is configured. */
    public static function isEnabled(): bool
    {
        return (int) getOption('ai_tryon_enabled', 0) === 1 && filled(self::apiKey());
    }

    public static function apiKey(): ?string
    {
        return getOption('ai_tryon_api_key') ?: config('services.gemini.api_key');
    }

    public static function model(): string
    {
        return getOption('ai_tryon_model') ?: config('services.gemini.model', 'gemini-3.1-flash-image');
    }

    /**
     * Ping Gemini with the given key/model to confirm they work, without
     * generating a (billed) image. Sends a tiny text+image request and checks
     * that an image part comes back.
     *
     * @return array{ok: bool, message: string}
     */
    public function testConnection(?string $apiKey = null, ?string $model = null): array
    {
        $apiKey = $apiKey ?: self::apiKey();
        $model = $model ?: self::model();

        if (blank($apiKey)) {
            return ['ok' => false, 'message' => __('No API key configured.')];
        }

        // 1x1 transparent PNG — gives the image model something to edit so the
        // probe exercises the real image pipeline cheaply.
        $pixel = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M8AAAMCAYAAAS1zNAAAAAASUVORK5CYII=');

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post(sprintf(self::ENDPOINT, $model), [
                    'contents' => [[
                        'parts' => [
                            ['text' => 'Return this image unchanged.'],
                            ['inline_data' => ['mime_type' => 'image/png', 'data' => base64_encode($pixel)]],
                        ],
                    ]],
                    'generationConfig' => ['responseModalities' => ['TEXT', 'IMAGE']],
                ]);
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => __('Could not reach Gemini: :error', ['error' => $e->getMessage()])];
        }

        if ($response->failed()) {
            $apiMessage = data_get($response->json(), 'error.message', $response->body());

            return ['ok' => false, 'message' => __('Gemini rejected the request: :error', ['error' => mb_strimwidth((string) $apiMessage, 0, 200, '…')])];
        }

        $hasImage = collect(data_get($response->json(), 'candidates.0.content.parts', []))
            ->contains(fn ($part) => filled($part['inline_data']['data'] ?? $part['inlineData']['data'] ?? null));

        return $hasImage
            ? ['ok' => true, 'message' => __('Connection OK — Gemini returned an image. Try-on is ready.')]
            : ['ok' => false, 'message' => __('Connected, but the model did not return an image. Check that ":model" supports image output.', ['model' => $model])];
    }

    /**
     * Generate a try-on image: the customer wearing/using the product.
     *
     * @return string The public-disk path of the generated image (e.g. tryon/abc.png).
     *
     * @throws CustomWebException on misconfiguration, a missing product image, or a gateway failure.
     */
    public function generate(UploadedFile $customerPhoto, Product $product): string
    {
        if (! self::isEnabled()) {
            throw new CustomWebException(__('Virtual try-on is currently unavailable.'), 422);
        }

        $productImage = $this->fetchProductImage($product);

        $response = Http::timeout(90)
            ->withHeaders([
                'x-goog-api-key' => self::apiKey(),
                'Content-Type' => 'application/json',
            ])
            ->post(sprintf(self::ENDPOINT, self::model()), [
                'contents' => [[
                    'parts' => [
                        ['text' => $this->prompt($product)],
                        ['inline_data' => [
                            'mime_type' => $customerPhoto->getMimeType() ?: 'image/jpeg',
                            'data' => base64_encode(file_get_contents($customerPhoto->getRealPath())),
                        ]],
                        ['inline_data' => [
                            'mime_type' => $productImage['mime'],
                            'data' => base64_encode($productImage['bytes']),
                        ]],
                    ],
                ]],
                'generationConfig' => [
                    'responseModalities' => ['TEXT', 'IMAGE'],
                ],
            ]);

        if ($response->failed()) {
            report(new \RuntimeException('Gemini try-on failed: '.$response->status().' '.$response->body()));

            throw new CustomWebException(__('The try-on could not be generated. Please try again.'), 502);
        }

        return $this->storeGeneratedImage($response->json());
    }

    /**
     * Build the try-on instruction. Category gives Gemini a hint about what the
     * product is (shirt vs shoes vs hijab) so it places it naturally.
     */
    private function prompt(Product $product): string
    {
        $category = $product->category?->name;
        $item = $category ? "{$category} ({$product->name})" : $product->name;

        return 'You are a virtual fashion try-on assistant. The first image is a photo of a person (the customer). '
            ."The second image is a product: {$item}. "
            .'Generate a single photorealistic image of the same person from the first photo wearing/using this exact product. '
            ."Preserve the person's face, body shape, pose, skin tone, and proportions exactly. "
            .'Fit the product naturally with realistic draping, fit, lighting, and shadows. '
            .'Keep the background simple and clean. Output only the resulting image.';
    }

    /**
     * Fetch the product's primary image bytes. The thumbnail is stored as a
     * path/URL string; resolve relative paths against the app URL.
     *
     * @return array{bytes: string, mime: string}
     */
    private function fetchProductImage(Product $product): array
    {
        $src = $product->thumbnail ?: $product->images()->value('image');

        if (! $src) {
            throw new CustomWebException(__('This product has no image to try on.'), 422);
        }

        // Local public-disk path → read from disk; otherwise fetch over HTTP.
        $relative = ltrim(Str::after($src, '/storage/'), '/');

        if (! Str::startsWith($src, ['http://', 'https://']) && Storage::disk('public')->exists($relative)) {
            return [
                'bytes' => Storage::disk('public')->get($relative),
                'mime' => Storage::disk('public')->mimeType($relative) ?: 'image/jpeg',
            ];
        }

        $url = Str::startsWith($src, ['http://', 'https://']) ? $src : url($src);
        $resp = Http::timeout(30)->get($url);

        if ($resp->failed()) {
            throw new CustomWebException(__('Could not load the product image. Please try again.'), 502);
        }

        return [
            'bytes' => $resp->body(),
            'mime' => $resp->header('Content-Type') ?: 'image/jpeg',
        ];
    }

    /**
     * Pull the base64 image out of Gemini's response and write it to the public disk.
     */
    private function storeGeneratedImage(array $payload): string
    {
        $parts = data_get($payload, 'candidates.0.content.parts', []);

        foreach ($parts as $part) {
            // The API returns inline_data; some SDK shapes use inlineData.
            $data = $part['inline_data']['data'] ?? $part['inlineData']['data'] ?? null;
            $mime = $part['inline_data']['mime_type'] ?? $part['inlineData']['mimeType'] ?? 'image/png';

            if ($data) {
                $ext = Str::after($mime, '/') ?: 'png';
                $path = self::RESULT_DIR.'/'.Str::uuid()->toString().'.'.$ext;
                Storage::disk('public')->put($path, base64_decode($data));

                return $path;
            }
        }

        throw new CustomWebException(__('The AI did not return an image. Please try a clearer photo.'), 502);
    }
}
