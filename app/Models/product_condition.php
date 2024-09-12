<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_condition extends Model
{
    use HasFactory;

    protected $fillable = ['product_condition'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
