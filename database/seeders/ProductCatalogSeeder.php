<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductCatalogSeeder extends Seeder
{
    /**
     * Seed a small sample catalog so the storefront/admin has data to show.
     */
    public function run(): void
    {
        $catalog = [
            'Electronics' => [
                ['name' => 'Wireless Earbuds', 'price' => 49.99, 'stock' => 120],
                ['name' => 'Smart Watch', 'price' => 89.00, 'stock' => 60],
            ],
            'Fashion' => [
                ['name' => 'Cotton T-Shirt', 'price' => 14.50, 'stock' => 200],
                ['name' => 'Denim Jacket', 'price' => 39.90, 'stock' => 45],
            ],
            'Home & Living' => [
                ['name' => 'Ceramic Mug', 'price' => 9.99, 'stock' => 300],
                ['name' => 'Table Lamp', 'price' => 24.00, 'stock' => 80],
            ],
        ];

        foreach ($catalog as $categoryName => $products) {
            $category = Category::create([
                'name' => $categoryName,
                'is_active' => true,
            ]);

            foreach ($products as $product) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'short_description' => $product['name'].' — sample product.',
                    'description' => '<p>'.$product['name'].' description.</p>',
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
