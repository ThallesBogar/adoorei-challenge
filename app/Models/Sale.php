<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
