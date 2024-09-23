<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
   
    public function collection()
    {
        return Product::with('categories')->get()->map(function ($product) {
          
            $categoryNames = $product->categories->pluck('name')->join(', ');

            return [
                'ID' => $product->id,
                'Name' => $product->name,
                'Description' => $product->description,
                'Price' => $product->price,
                'Image' => $product->image,
                'Created At' => $product->created_at,
                'Updated At' => $product->updated_at,
                'Discount' => $product->discount,
                'Product Condition ID' => $product->product_condition_id,
                'Categories' => $categoryNames, 
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Price',
            'Image',
            'Created At',
            'Updated At',
            'Discount',
            'Product Condition ID',
            'Categories' 
        ];
    }
}
