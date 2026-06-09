<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductCatalogSeeder extends Seeder
{
    /**
     * Seed a small sample catalog (with images + rich descriptions) so the
     * storefront/admin has realistic data to show.
     */
    public function run(): void
    {
        $catalog = [
            'Electronics' => [
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400&q=80',
                'products' => [
                    [
                        'name' => 'Wireless Earbuds',
                        'price' => 49.99,
                        'compare_at_price' => 74.99,
                        'stock' => 120,
                        'is_featured' => true,
                        'images' => [
                            'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=700&q=80',
                            'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=700&q=80',
                        ],
                    ],
                    [
                        'name' => 'Smart Watch',
                        'price' => 89.00,
                        'stock' => 60,
                        'is_featured' => true,
                        'images' => [
                            'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=700&q=80',
                            'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=700&q=80',
                        ],
                    ],
                ],
            ],
            'Fashion' => [
                'image' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&q=80',
                'products' => [
                    [
                        'name' => 'Cotton T-Shirt',
                        'price' => 14.50,
                        'compare_at_price' => 19.99,
                        'stock' => 200,
                        'images' => [
                            'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=700&q=80',
                        ],
                    ],
                    [
                        'name' => 'Denim Jacket',
                        'price' => 39.90,
                        'stock' => 45,
                        'images' => [
                            'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=700&q=80',
                        ],
                    ],
                ],
            ],
            'Home & Living' => [
                'image' => 'https://images.unsplash.com/photo-1567016432779-094069958ea5?w=400&q=80',
                'products' => [
                    [
                        'name' => 'Ceramic Mug',
                        'price' => 9.99,
                        'stock' => 300,
                        'images' => [
                            'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=700&q=80',
                        ],
                    ],
                    [
                        'name' => 'Table Lamp',
                        'price' => 24.00,
                        'compare_at_price' => 32.00,
                        'stock' => 80,
                        'is_featured' => true,
                        'images' => [
                            'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=700&q=80',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($catalog as $categoryName => $data) {
            $category = Category::create([
                'name' => $categoryName,
                'image' => $data['image'],
                'description' => 'Explore our '.$categoryName.' collection.',
                'is_active' => true,
            ]);

            foreach ($data['products'] as $product) {
                $images = $product['images'] ?? [];

                $created = Product::create([
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'thumbnail' => $images[0] ?? null,
                    'short_description' => $this->shortDescription($product['name']),
                    'description' => $this->description($product['name']),
                    'price' => $product['price'],
                    'compare_at_price' => $product['compare_at_price'] ?? null,
                    'stock' => $product['stock'],
                    'is_featured' => $product['is_featured'] ?? false,
                    'is_active' => true,
                ]);

                foreach ($images as $i => $url) {
                    ProductImage::create([
                        'product_id' => $created->id,
                        'image' => $url,
                        'sort_order' => $i,
                    ]);
                }
            }
        }
    }

    private function shortDescription(string $name): string
    {
        return "Premium quality {$name} crafted for everyday use. Enjoy great value with cash-on-delivery convenience.";
    }

    private function description(string $name): string
    {
        return <<<HTML
<p>The <strong>{$name}</strong> blends quality, durability and modern design to fit seamlessly into your daily life. Each piece is carefully selected and quality-checked before it reaches your door.</p>
<h4>Key Features</h4>
<ul>
    <li>Premium materials built to last</li>
    <li>Thoughtful, modern design</li>
    <li>Backed by our easy returns policy</li>
    <li>Cash on delivery available nationwide</li>
</ul>
<h4>Why you'll love it</h4>
<p>Whether for yourself or as a gift, the {$name} offers dependable performance and a refined finish at an honest price. Order today and pay only when it arrives.</p>
<p><em>Note: actual color may vary slightly depending on your screen.</em></p>
HTML;
    }
}
