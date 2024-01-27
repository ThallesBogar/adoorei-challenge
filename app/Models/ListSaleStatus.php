<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListSaleStatus extends Model
{
    use HasFactory;

    protected $table = 'list_sales_status';
    protected $fillable = [
        'name',
    ];

    public const PENDING = 1;
    public const PROCESSING = 2;
    public const PAID = 3;
    public const IN_TRANSIT_SHIPPED = 4;
    public const DELIVERED = 5;
    public const CANCELED = 6;
    public const RETURNED = 7;
    public const PARTIALLY_REFUNDED = 8;
    public const PAYMENT_FAILED = 9;
    public const REFUNDED = 10;

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
