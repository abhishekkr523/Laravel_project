<?php
namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\product_condition;

class ProductImport implements ToModel
{
    public function model(array $row)
{
    $name = $row['1'];
    $description =$row['2'];
    $price = isset($row['3']) && is_numeric($row['3']) 
    ? (float) $row['3'] 
    : 0.00;
     $image = isset($row['4']) ? $row['4'] : null;
    $discount = isset($row['5']) && is_numeric($row['5']) 
            ? (int) $row['5'] 
            : 0;
   
    // $conditionName = $row[6];
    // $condition = product_condition::firstOrCreate(
    //     ['product_condition' => $conditionName],
    // );
    // \Log::info('Importing product:', [
    //     'name' => $name,
    //     'description' => $description,
    //     'price' => $price,
    //     'discount' => $discount
    // ]);

    return new Product([
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'discount' => $discount,
        // 'condition' => $condition,
    ]);
}

}
