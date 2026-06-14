<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            "Women's Products" => [
                'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=600&q=80',
                'products' => [
                    [
                        'name' => 'Ready-made Cotton Kurti',
                        'price' => 1450.00,
                        'compare_at_price' => 1850.00,
                        'stock' => 250,
                        'is_featured' => true,
                        'sku' => 'wom-ready-01',
                        'images' => [
                            'https://images.unsplash.com/photo-1608748010899-18f300247112?w=700&q=80',
                            'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=700&q=80',
                            'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=700&q=80',
                            'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=700&q=80',
                        ],
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'description' => '<p>Premium quality ready-made 100% cotton Kurti. Ideal for casual outings and daily wear.</p>',
                        'variants' => [
                            ['attrs' => ['Color' => 'Rose Pink', 'Size' => '36'], 'sku' => 'wom-ready-01-36', 'stock' => 50],
                            ['attrs' => ['Color' => 'Rose Pink', 'Size' => '38'], 'sku' => 'wom-ready-01-38', 'stock' => 50],
                            ['attrs' => ['Color' => 'Rose Pink', 'Size' => '40'], 'sku' => 'wom-ready-01-40', 'stock' => 50],
                            ['attrs' => ['Color' => 'Rose Pink', 'Size' => '42'], 'sku' => 'wom-ready-01-42', 'stock' => 50],
                            ['attrs' => ['Color' => 'Rose Pink', 'Size' => '44'], 'sku' => 'wom-ready-01-44', 'stock' => 50],
                        ],
                    ],
                    [
                        'name' => 'Unstitched Premium Georgette 3-Piece',
                        'price' => 2250.00,
                        'compare_at_price' => 2950.00,
                        'stock' => 120,
                        'is_featured' => true,
                        'sku' => 'wom-unstitch-01',
                        'images' => [
                            'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=700&q=80',
                        ],
                        'description' => '<p>Gorgeous unstitched Georgette 3-piece set with heavy embroidery work. Customize the size to your preferences.</p>',
                        'variants' => [], // No variants, unstitched clothing
                    ],
                    [
                        'name' => 'Fuchsia Azure Delight 3-Piece',
                        'price' => 1750.00,
                        'compare_at_price' => 2250.00,
                        'stock' => 100,
                        'is_featured' => true,
                        'sku' => 'lbm-1008',
                        'images' => [
                            'https://images.unsplash.com/photo-1608748010899-18f300247112?w=700&q=80',
                        ],
                        'description' => '<p>Pure cotton unstitched 3-piece designer dress with digital printed dupatta.</p>',
                        'variants' => [], // No variants, unstitched clothing
                    ]
                ],
            ],
            'Baby Products' => [
                'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=600&q=80',
                'products' => [
                    [
                        'name' => 'Baby Romper & Pajama Set',
                        'price' => 850.00,
                        'compare_at_price' => 1200.00,
                        'stock' => 300,
                        'is_featured' => true,
                        'sku' => 'baby-romp-01',
                        'images' => [
                            'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=700&q=80',
                        ],
                        'description' => '<p>Soft organic cotton romper set for infants. Easy snap buttons for quick diaper changes.</p>',
                        'variants' => [
                            ['attrs' => ['Color' => 'Sky Blue', 'Age' => '0-3 months'], 'sku' => 'baby-romp-01-03m', 'stock' => 50],
                            ['attrs' => ['Color' => 'Sky Blue', 'Age' => '3-6 months'], 'sku' => 'baby-romp-01-36m', 'stock' => 50],
                            ['attrs' => ['Color' => 'Sky Blue', 'Age' => '6-12 months'], 'sku' => 'baby-romp-01-612m', 'stock' => 50],
                            ['attrs' => ['Color' => 'Sky Blue', 'Age' => '1 year'], 'sku' => 'baby-romp-01-1y', 'stock' => 50],
                            ['attrs' => ['Color' => 'Sky Blue', 'Age' => '2 years'], 'sku' => 'baby-romp-01-2y', 'stock' => 50],
                            ['attrs' => ['Color' => 'Sky Blue', 'Age' => '3 years'], 'sku' => 'baby-romp-01-3y', 'stock' => 50],
                        ]
                    ]
                ],
            ],
            'Boys Products' => [
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600&q=80',
                'products' => [
                    [
                        'name' => 'Boys Casual Cotton Shirt',
                        'price' => 950.00,
                        'compare_at_price' => 1350.00,
                        'stock' => 250,
                        'is_featured' => true,
                        'sku' => 'boy-shirt-01',
                        'images' => [
                            'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=700&q=80',
                        ],
                        'description' => '<p>Comfortable check cotton shirt for boys. Ideal for smart casual dress-up.</p>',
                        'variants' => [
                            ['attrs' => ['Color' => 'Green Plaid', 'Size' => 'S'], 'sku' => 'boy-shirt-01-s', 'stock' => 50],
                            ['attrs' => ['Color' => 'Green Plaid', 'Size' => 'M'], 'sku' => 'boy-shirt-01-m', 'stock' => 50],
                            ['attrs' => ['Color' => 'Green Plaid', 'Size' => 'L'], 'sku' => 'boy-shirt-01-l', 'stock' => 50],
                            ['attrs' => ['Color' => 'Green Plaid', 'Size' => 'XL'], 'sku' => 'boy-shirt-01-xl', 'stock' => 50],
                            ['attrs' => ['Color' => 'Green Plaid', 'Size' => 'XXL'], 'sku' => 'boy-shirt-01-xxl', 'stock' => 50],
                        ]
                    ]
                ],
            ],
            "Men's Products" => [
                'image' => 'https://images.unsplash.com/photo-1490367532201-b9bc1dc483f6?w=600&q=80',
                'products' => [
                    [
                        'name' => "Men's Formal Slim-Fit Shirt",
                        'price' => 1650.00,
                        'compare_at_price' => 2200.00,
                        'stock' => 250,
                        'is_featured' => true,
                        'sku' => 'men-shirt-01',
                        'images' => [
                            'https://images.unsplash.com/photo-1490367532201-b9bc1dc483f6?w=700&q=80',
                        ],
                        'description' => '<p>Premium quality formal slim-fit cotton shirt. Wrinkle-resistant and breathable.</p>',
                        'variants' => [
                            ['attrs' => ['Color' => 'Classic White', 'Size' => 'S'], 'sku' => 'men-shirt-01-s', 'stock' => 50],
                            ['attrs' => ['Color' => 'Classic White', 'Size' => 'M'], 'sku' => 'men-shirt-01-m', 'stock' => 50],
                            ['attrs' => ['Color' => 'Classic White', 'Size' => 'L'], 'sku' => 'men-shirt-01-l', 'stock' => 50],
                            ['attrs' => ['Color' => 'Classic White', 'Size' => 'XL'], 'sku' => 'men-shirt-01-xl', 'stock' => 50],
                            ['attrs' => ['Color' => 'Classic White', 'Size' => 'XXL'], 'sku' => 'men-shirt-01-xxl', 'stock' => 50],
                        ]
                    ],
                    [
                        'name' => "Men's Stretch Gabardine Pant",
                        'price' => 1850.00,
                        'compare_at_price' => 2500.00,
                        'stock' => 250,
                        'is_featured' => true,
                        'sku' => 'men-pant-01',
                        'images' => [
                            'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=700&q=80',
                        ],
                        'description' => '<p>Sleek stretch gabardine chino pants for men. Durable stitching and premium zipper.</p>',
                        'variants' => [
                            ['attrs' => ['Color' => 'Khaki', 'Waist' => '28'], 'sku' => 'men-pant-01-28', 'stock' => 50],
                            ['attrs' => ['Color' => 'Khaki', 'Waist' => '30'], 'sku' => 'men-pant-01-30', 'stock' => 50],
                            ['attrs' => ['Color' => 'Khaki', 'Waist' => '32'], 'sku' => 'men-pant-01-32', 'stock' => 50],
                            ['attrs' => ['Color' => 'Khaki', 'Waist' => '34'], 'sku' => 'men-pant-01-34', 'stock' => 50],
                            ['attrs' => ['Color' => 'Khaki', 'Waist' => '36'], 'sku' => 'men-pant-01-36', 'stock' => 50],
                        ]
                    ],
                    [
                        'name' => "Men's Premium Polo T-shirt",
                        'price' => 950.00,
                        'compare_at_price' => 1400.00,
                        'stock' => 200,
                        'is_featured' => true,
                        'sku' => 'men-polo-01',
                        'images' => [
                            'https://images.unsplash.com/photo-1581655353564-df123a1eb820?w=700&q=80',
                        ],
                        'description' => '<p>Modern fit pique knit cotton polo t-shirt. Contrast tipping on collar and sleeves.</p>',
                        'variants' => [
                            ['attrs' => ['Color' => 'Navy Blue', 'Size' => 'S'], 'sku' => 'men-polo-01-s', 'stock' => 50],
                            ['attrs' => ['Color' => 'Navy Blue', 'Size' => 'M'], 'sku' => 'men-polo-01-m', 'stock' => 50],
                            ['attrs' => ['Color' => 'Navy Blue', 'Size' => 'L'], 'sku' => 'men-polo-01-l', 'stock' => 50],
                            ['attrs' => ['Color' => 'Navy Blue', 'Size' => 'XL'], 'sku' => 'men-polo-01-xl', 'stock' => 50],
                        ]
                    ],
                    [
                        'name' => "Men's Casual Denim Jacket",
                        'price' => 2450.00,
                        'compare_at_price' => 3200.00,
                        'stock' => 200,
                        'is_featured' => false,
                        'sku' => 'men-jack-01',
                        'images' => [
                            'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=700&q=80',
                        ],
                        'description' => '<p>Heavyweight classic denim jacket. Button closures and chest pockets.</p>',
                        'variants' => [
                            ['attrs' => ['Color' => 'Stone Wash', 'Size' => 'S'], 'sku' => 'men-jack-01-s', 'stock' => 50],
                            ['attrs' => ['Color' => 'Stone Wash', 'Size' => 'M'], 'sku' => 'men-jack-01-m', 'stock' => 50],
                            ['attrs' => ['Color' => 'Stone Wash', 'Size' => 'L'], 'sku' => 'men-jack-01-l', 'stock' => 50],
                            ['attrs' => ['Color' => 'Stone Wash', 'Size' => 'XL'], 'sku' => 'men-jack-01-xl', 'stock' => 50],
                        ]
                    ]
                ],
            ],
            'Traditional Products' => [
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600&q=80',
                'products' => [
                    [
                        'name' => 'Premium Cotton Lungi',
                        'price' => 650.00,
                        'compare_at_price' => 850.00,
                        'stock' => 100,
                        'is_featured' => true,
                        'sku' => 'trad-lungi-01',
                        'images' => [
                            'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=700&q=80',
                        ],
                        'description' => '<p>Traditional premium handloom cotton lungi. Pure soft fabric for daily comfort.</p>',
                        'variants' => [
                            ['attrs' => ['Color' => 'Blue Stripe', 'Length' => '2.25 yards'], 'sku' => 'trad-lungi-01-225', 'stock' => 50],
                            ['attrs' => ['Color' => 'Blue Stripe', 'Length' => '2.5 yards'], 'sku' => 'trad-lungi-01-250', 'stock' => 50],
                        ]
                    ],
                    [
                        'name' => 'Handloom Cotton Gamcha',
                        'price' => 250.00,
                        'compare_at_price' => 350.00,
                        'stock' => 150,
                        'is_featured' => false,
                        'sku' => 'trad-gamcha-01',
                        'images' => [
                            'https://images.unsplash.com/photo-1621184455862-c163dfb30e0f?w=700&q=80',
                        ],
                        'description' => '<p>Pure handloom cotton Gamcha. Excellent water absorption and fast drying.</p>',
                        'variants' => [], // Simple product, no size options
                    ]
                ]
            ]
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
                $variants = $product['variants'] ?? [];

                $productStock = !empty($variants) ? array_sum(array_column($variants, 'stock')) : $product['stock'];

                $created = Product::create([
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'sku' => $product['sku'] ?? null,
                    'thumbnail' => $images[0] ?? null,
                    'video_url' => $product['video_url'] ?? null,
                    'short_description' => "Premium quality {$product['name']} crafted for comfort and elegance.",
                    'description' => $product['description'] ?? "<p>Beautifully designed {$product['name']} made from high grade fabrics. Perfect for daily wear and formal events.</p>",
                    'price' => $product['price'],
                    'compare_at_price' => $product['compare_at_price'] ?? null,
                    'stock' => $productStock,
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

                foreach ($variants as $idx => $v) {
                    $nameParts = [];
                    foreach ($v['attrs'] as $key => $val) {
                        $nameParts[] = $val;
                    }
                    $variantName = implode(' / ', $nameParts);

                    ProductVariant::create([
                        'product_id' => $created->id,
                        'name' => $variantName,
                        'sku' => $v['sku'] ?? null,
                        'attributes' => $v['attrs'],
                        'price_adjustment' => $v['price_adjustment'] ?? 0.00,
                        'stock' => $v['stock'],
                        'sort_order' => $idx,
                    ]);
                }
            }
        }
    }
}
