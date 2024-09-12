<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'image','discount','condition','product_condition_id'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function getDiscountedPrice(): float
    {
        if ($this->discount) {
            return $this->price - ($this->price * ($this->discount / 100));
        }

        return $this->price; // Return original price if no discount
    }

    public function product_con()
    {
        // return $this->belongsTo(product_condition::class);
        return $this->belongsTo(product_condition::class, 'product_condition_id');
    }
}
