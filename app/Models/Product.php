<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = "products";
    protected $fillable = ['store_id', 'list_product_category_id', 'name', 'price', 'description'];

    public function saleProducts()
    {
        return $this->hasMany(SaleProduct::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function listProductCategory()
    {
        return $this->belongsTo(ListProductCategory::class, 'list_product_category_id');
    }
}
