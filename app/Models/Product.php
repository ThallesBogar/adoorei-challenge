<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Product",
 *     description="Product model",
 *     @OA\Xml(name="Product"),
 *     @OA\Property(property="store_id", type="integer", example=1, description="Unique identifier of the store"),
 *     @OA\Property(property="store_name", type="string", example="Loja ABC LTDA", description="Name of the store"),
 *     @OA\Property(property="product_id", type="integer", example=1, description="Unique identifier of the product"),
 *     @OA\Property(property="product_category", type="string", example="Cellphones", description="Category of the product"),
 *     @OA\Property(property="product_name", type="string", example="Celular 1", description="Name of the product"),
 *     @OA\Property(property="product_price", type="number", format="float", example=1800, description="Price of the product"),
 *     @OA\Property(property="product_description", type="string", example="Qui ratione sapiente blanditiis veniam dolores.", description="Description of the product")
 * )
 */

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
