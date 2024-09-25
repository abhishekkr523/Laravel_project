<?php

// namespace App\Exports;

// use App\Models\Product;
// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\WithHeadings;

// class ProductsExport implements FromCollection, WithHeadings
// {
//     /**
//     * @return \Illuminate\Support\Collection
//     */
//     public function collection()
//     {
//         return Product::with('categories') ->whereIn("id", [184, 185])->get()->map(function ($product) {
//             // Extract category names
//             $categoryNames = $product->categories->pluck('name')->join(', ');

//             return [
//                 'ID' => $product->id,
//                 'Name' => $product->name,
//                 'Description' => $product->description,
//                 'Price' => $product->price,
//                 'Image' => $product->image,
//                 'Created At' => $product->created_at,
//                 'Updated At' => $product->updated_at,
//                 'Discount' => $product->discount,
//                 'Product Condition ID' => $product->product_condition_id,
//                 'Categories' => $categoryNames, // Category names concatenated
//             ];
//         });
//     }

//     public function headings(): array
//     {
//         return [
//             'ID',
//             'Name',
//             'Description',
//             'Price',
//             'Image',
//             'Created At',
//             'Updated At',
//             'Discount',
//             'Product Condition ID',
//             'Categories' // Header for category names
//         ];
//     }
// }


namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    protected $selectedIds;

    public function __construct(array $selectedIds = [])
    {
        $this->selectedIds = $selectedIds;
    }

    // This function will return the collection for exporting
    public function collection()
    {
        if (empty($this->selectedIds)) {
            // Export all data
            return Product::with('categories')->get()->map(function ($product) {
                return $this->mapProductData($product);
            });
        } else {
            // Export only selected products
            return Product::with('categories')
                ->whereIn('id', $this->selectedIds)
                ->get()
                ->map(function ($product) {
                    return $this->mapProductData($product);
                });
        }
    }

    // This helper method will format the product data for export
    protected function mapProductData($product)
    {
        $categoryNames = $product->categories->pluck('name')->join(', ');

        return [
            'ID' => $product->id,
            'Name' => $product->name,
            'Description' => $product->description,
            'Price' => $product->price,
            'Image' => $product->image,
            'Status' => $product->status ? "Active" : "Inactive",
            'Created At' => $product->created_at,
            'Updated At' => $product->updated_at,
            'Discount' => $product->discount,
            'Product Condition ID' => $product->product_condition_id,
            'Categories' => $categoryNames,
        ];
    }

    // This function will define the column headings for the export
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Price',
            'Image',
            'Status',
            'Created At',
            'Updated At',
            'Discount',
            'Product Condition ID',
            'Categories'
        ];
    }
}
