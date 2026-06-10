<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'Kids Girls' => [
                'image' => 'https://images.unsplash.com/photo-1608748010899-18f300247112?w=600&q=80',
                'products' => [
                    [
                        'name' => 'Kids Cotton Lehenga',
                        'price' => 1800.00,
                        'compare_at_price' => 2500.00,
                        'stock' => 120,
                        'is_featured' => true,
                        'sku' => 'lbm-2001',
                        'images' => [
                            'https://images.unsplash.com/photo-1608748010899-18f300247112?w=700&q=80',
                        ],
                    ],
                    [
                        'name' => 'Kids Party Frock',
                        'price' => 1500.00,
                        'stock' => 80,
                        'is_featured' => true,
                        'sku' => 'lbm-2002',
                        'images' => [
                            'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=700&q=80',
                        ],
                    ],
                ],
            ],
            'Ladies Three Piece' => [
                'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=600&q=80',
                'products' => [
                    [
                        'name' => 'Fuchsia Azure Delight',
                        'price' => 1750.00,
                        'compare_at_price' => 2250.00,
                        'stock' => 100,
                        'is_featured' => true,
                        'sku' => 'lbm-1008',
                        'images' => [
                            'https://images.unsplash.com/photo-1608748010899-18f300247112?w=700&q=80',
                        ],
                        'description' => '
                            <p class="mb-4"><strong>ফেব্রিক:</strong> এই সম্পূর্ণ পোশাকটি তৈরি করা হয়েছে ১০০% প্রিমিয়াম পিওর কটন (Pure Cotton) ফেব্রিক দিয়ে। পিওর কটন হওয়ার কারণে এটি অত্যন্ত আরামদায়ক, ঘাম শোষক এবং ত্বকের জন্য বন্ধুত্বপূর্ণ—যা আমাদের দেশের গরম আবহাওয়ার জন্য সবথেকে সেরা পছন্দ।</p>
                            <p class="mb-4"><strong>কামিজ:</strong> স্নিগ্ধ গোলাপি ও আসমানি রঙের এই কটন কামিজের সামনে এবং গলার প্যানেলে রয়েছে নিখুঁত এমব্রয়ডারি এবং আইলেট (Eyelet) লেসের কাজ। সুতি কাপড়ের ওপর এই সূক্ষ্ম কারুকাজ পোশাকটিকে ক্যাজুয়াল থেকে সেমি-ফরমাল লুকে নিয়ে গেছে। হাতার নকশাটি একে আরও স্মার্ট করে তুলেছে।</p>
                            <p class="mb-4"><strong>ওড়না:</strong> সাথে রয়েছে ডিজিটাল প্রিন্টেড ওড়না, যা কামিজের রঙের সাথে সামঞ্জস্য রেখে মাল্টি-কালার ফ্লোরাল মোটিফে ডিজাইন করা। পিওর কটন ওড়নাটি দীর্ঘক্ষণ মাথায় বা কাঁধে রাখতে সুবিধা দেবে এবং এটি বেশ বড় ও আরামদায়ক।</p>
                            <p class="mb-4"><strong>সালোয়ার:</strong> ম্যাচিং সালোয়ার সালোয়ার প্যান্ট কাট সালোয়ার। এর নিচের অংশে সূক্ষ্ম লেসের ডিটেইলিং করা হয়েছে যা পুরো সেটটিকে একটি কমপ্লিট লুক দেয়।</p>
                            <p class="mb-4"><strong>উপযোগিতা:</strong> পিওর কটন ড্রেস মানেই স্বস্তি। সারাদিন অফিস, ভার্সিটি বা ঘরোয়া অনুষ্ঠানে পরে থাকার জন্য এটি একটি আদর্শ পোশাক। ধোয়ার পরেও এর উজ্জ্বলতা এবং আরামদায়ক ভাব বজায় থাকবে।</p>
                            <p class="mb-4"><strong>বিশেষ দ্রষ্টব্য:</strong> ক্যামেরার লাইটিং, ফটোগ্রাফি এবং আপনার ডিভাইসের (মোবাইল/কম্পিউটার) ডিসপ্লে সেটিং-এর কারণে পণ্যের প্রকৃত রঙ এবং ছবির রঙের মধ্যে সামান্য তারতম্য হতে পারে। তবে আমরা পণ্যের আসল রঙটি ছবিতে ফুটিয়ে তোলার সর্বোচ্চ চেষ্টা করেছি।</p>
                        ',
                    ],
                    [
                        'name' => 'Coffee Gold Floral Embroidery Cotton Three Piece',
                        'price' => 1750.00,
                        'compare_at_price' => 2250.00,
                        'stock' => 150,
                        'is_featured' => true,
                        'sku' => 'lbm-1001',
                        'images' => [
                            'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=700&q=80',
                        ],
                        'description' => '
                            <p class="mb-4"><strong>ফেব্রিক:</strong> ১০০% পিওর আরামদায়ক সুতি কাপড় দিয়ে তৈরি, যা গরমের দিনে সতেজ অনুভূতি দেয়।</p>
                            <p class="mb-4"><strong>কামিজ:</strong> আকর্ষণীয় কফি গোল্ড কালারের ওপরে গর্জিয়াস এমব্রয়ডারি ও সুনিপুণ জড়ি সুতার কাজ করা ডিজাইন কামিজ।</p>
                            <p class="mb-4"><strong>ওড়না:</strong> ম্যাচিং ডিজাইনে করা ৫ হাত লম্বা ওড়না, যা অত্যন্ত আরামদায়ক ও ব্যবহারের উপযোগী।</p>
                            <p class="mb-4"><strong>সালোয়ার:</strong> উন্নতমানের পিওর সলিড সালোয়ার কাপড়।</p>
                        ',
                    ],
                    [
                        'name' => 'Azure & Mint Elegance',
                        'price' => 1750.00,
                        'compare_at_price' => 2250.00,
                        'stock' => 90,
                        'is_featured' => true,
                        'sku' => 'lbm-1002',
                        'images' => [
                            'https://images.unsplash.com/photo-1609357605129-26f69add5d6e?w=700&q=80',
                        ],
                        'description' => '
                            <p class="mb-4"><strong>ফেব্রিক:</strong> আরামদায়ক সফট জর্জেট ও পিওর কটন কম্বিনেশন দিয়ে তৈরি থ্রি-পিস।</p>
                            <p class="mb-4"><strong>কামিজ:</strong> রিফ্রেশিং আজুর ও মিন্ট কালার স্কিমের ওপর আকর্ষণীয় কারুকাজ ও আধুনিক প্যানেল কাট ডিজাইন।</p>
                            <p class="mb-4"><strong>ওড়না:</strong> পিওর ৫ হাত প্রিমিয়াম সফট শিফন ওড়না।</p>
                        ',
                    ],
                    [
                        'name' => 'Zari-Enchanted Mustard & Teal Salwar Kameez Set',
                        'price' => 1750.00,
                        'compare_at_price' => 2250.00,
                        'stock' => 110,
                        'is_featured' => true,
                        'sku' => 'lbm-1003',
                        'images' => [
                            'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=700&q=80',
                        ],
                        'description' => '
                            <p class="mb-4"><strong>ফেব্রিক:</strong> উৎসবের জন্য প্রিমিয়াম আরামদায়ক লিনেন ও জ্যাকার্ড উইভিং ফেব্রিক।</p>
                            <p class="mb-4"><strong>কামিজ:</strong> মাস্টার্ড গোল্ড এবং টিল কালার কম্বিনেশনে তৈরি কামিজ, যাতে রয়েছে গর্জিয়াস জড়ি (Zari) কাজ।</p>
                        ',
                    ],
                    [
                        'name' => 'Sky Blue Floral Vibe',
                        'price' => 4200.00,
                        'compare_at_price' => 5200.00,
                        'stock' => 50,
                        'is_featured' => true,
                        'sku' => 'lbm-1004',
                        'images' => [
                            'https://images.unsplash.com/photo-1621184455862-c163dfb30e0f?w=700&q=80',
                        ],
                        'description' => '
                            <p class="mb-4"><strong>ফেব্রিক:</strong> প্রিমিয়াম লুম কটন ড্রেস, যা নরম এবং ত্বকের জন্য খুবই ভালো।</p>
                        ',
                    ],
                    [
                        'name' => 'Maroon Glow',
                        'price' => 4200.00,
                        'compare_at_price' => 5000.00,
                        'stock' => 70,
                        'is_featured' => true,
                        'sku' => 'lbm-1005',
                        'images' => [
                            'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=700&q=80',
                        ],
                        'description' => '
                            <p class="mb-4"><strong>ফেব্রিক:</strong> প্রিমিয়াম অরগাঞ্জা এবং সিল্ক মিক্স, যা গর্জিয়াস ও জমকালো দেখায়।</p>
                        ',
                    ],
                    [
                        'name' => 'Magenta Floral Elegance',
                        'price' => 1950.00,
                        'compare_at_price' => 2500.00,
                        'stock' => 60,
                        'is_featured' => false,
                        'sku' => 'lbm-1006',
                        'images' => [
                            'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=700&q=80',
                        ],
                        'description' => '
                            <p class="mb-4"><strong>ফেব্রিক:</strong> সফট সিল্কি লিনেন কটন ফেব্রিক দিয়ে ডিজাইন করা হয়েছে।</p>
                        ',
                    ],
                ],
            ],
            'Combo Sets' => [
                'image' => 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?w=600&q=80',
                'products' => [
                    [
                        'name' => 'Mother & Daughter Combo Set',
                        'price' => 5500.00,
                        'compare_at_price' => 7000.00,
                        'stock' => 45,
                        'is_featured' => true,
                        'sku' => 'lbm-3001',
                        'images' => [
                            'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?w=700&q=80',
                        ],
                    ],
                    [
                        'name' => 'Sisters Ethnic Matching Combo',
                        'price' => 4200.00,
                        'stock' => 50,
                        'is_featured' => false,
                        'sku' => 'lbm-3002',
                        'images' => [
                            'https://images.unsplash.com/photo-1608748010899-18f300247112?w=700&q=80',
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
                    'sku' => $product['sku'] ?? null,
                    'thumbnail' => $images[0] ?? null,
                    'short_description' => "Premium quality {$product['name']} crafted for comfort and elegance.",
                    'description' => $product['description'] ?? "<p>Beautifully designed {$product['name']} made from high grade loom fabrics. Perfect for festivals, family events and formal wear.</p>",
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
}
