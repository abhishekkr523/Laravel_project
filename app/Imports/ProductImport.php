<?php
namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\product_condition;

class ProductImport implements ToModel
{
    public function model(array $row)
    {
        // Find the category by ID from the row
        $category = Category::find($row[9]);
        

        if ($category) {
            // Create or update the product with other details
            $product = Product::firstOrCreate(
                ['name' => $row['1']],
                [
                    'description' => $row['2'],
                    'price' => isset($row['3']) && is_numeric($row['3']) ? (float)$row['3'] : 0.00,
                    'image' => isset($row['4']) ? $row['4'] : null,
                    'discount' => isset($row['7']) && is_numeric($row['7']) ? (int)$row['7'] : 0,
                    'product_condition_id' => $this->getProductConditionId($row[8]),
                ]
            );

            // Sync the category by its ID
            // $product->categories()->sync([$category->id]);
            $product->categories()->attach($category->id);

            // dd($product);
        }

        return $product;
    }

    private function getProductConditionId($conditionName)
    {
        // Find or create the product condition
        $condition = product_condition::firstOrCreate(
            ['product_condition' => $conditionName]
        );
        return $condition->id;
    }
}






