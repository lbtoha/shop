<?php

namespace App\Http\Controllers\Shop;

use App\Exceptions\CustomWebException;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Ai\GeminiTryOnService;
use App\Services\Ai\TryOnAbuseGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * AI virtual try-on (Gemini). The customer uploads a photo and previews a
 * product on themselves before buying. The uploaded photo is not stored; only
 * the generated result is saved to the public disk and auto-cleaned by a
 * scheduled task (see PruneTryOnImages).
 */
class TryOnController extends Controller
{
    public function __construct(private GeminiTryOnService $tryOn, private TryOnAbuseGuard $guard) {}

    /**
     * Generate a try-on preview for a product. Returns JSON for the AJAX modal.
     */
    public function generate(Request $request, string $slug)
    {
        if (! GeminiTryOnService::isEnabled()) {
            return response()->json(['message' => __('Virtual try-on is currently unavailable.')], 422);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:8192',
        ]);

        $product = Product::active()->where('slug', $slug)->firstOrFail();

        try {
            // Abuse checks (login gate, bot friction, per-user + global limits)
            // run before the billed Gemini call; the counter is only consumed on
            // a successful generation, so failures don't burn anyone's quota.
            $this->guard->assert($request);

            $path = $this->tryOn->generate($request->file('photo'), $product);

            $this->guard->consume($request);
        } catch (CustomWebException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        }

        // Track generated files in the session so the cleanup task and a future
        // "clear" action know which results belong to this visitor.
        $generated = $request->session()->get('tryon_results', []);
        $generated[] = $path;
        $request->session()->put('tryon_results', array_slice($generated, -20));

        return response()->json([
            'message' => __('Here is your try-on preview!'),
            'image_url' => Storage::disk('public')->url($path),
        ]);
    }
}
