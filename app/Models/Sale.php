<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Sale",
 *     type="object",
 *     title="Sale",
 *     description="Response schema for a sale",
 *     @OA\Property(
 *         property="sale_id",
 *         type="integer",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         example="Pending"
 *     ),
 *     @OA\Property(
 *         property="currency",
 *         type="string",
 *         example="USD"
 *     ),
 *     @OA\Property(
 *         property="total_price",
 *         type="number",
 *         format="float",
 *         example=1800
 *     ),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         @OA\Items(type="object",
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(property="category", type="string", example="Cellphones"),
 *             @OA\Property(property="name", type="string", example="Celular 1"),
 *             @OA\Property(property="price", type="number", format="float", example=1800),
 *             @OA\Property(property="amount", type="integer", example=1)
 *         )
 *     )
 * )
 */


class Sale extends Model
{
    use HasFactory;

    protected $table = "sales";
    protected $fillable = ['list_sale_status_id', 'total_price'];

    public function saleProducts()
    {
        return $this->hasMany(SaleProduct::class);
    }

    public function listSaleStatus()
    {
        return $this->belongsTo(ListSaleStatus::class, 'list_sale_status_id');
    }
}
