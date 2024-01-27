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

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
