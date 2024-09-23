<?php

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryRepositoryImpl implements CategoryRepository
{
    public function getCategories(): Collection
    {
        return Category::all();
    }

    // public function getCategoryById($categoryId): Category
    // {
    //     return Category::find($categoryId);
    // }

    public function getCategoryById(int $Id): Category
    {
        $category = Category::find($Id);
    
        if (!$category) {
            // dd($category);
            throw new \Exception('Category not found');
        }
    
        return $category;
    }
    

}
