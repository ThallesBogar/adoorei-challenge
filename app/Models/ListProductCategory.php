<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListProductCategory extends Model
{
    use HasFactory;

    protected $table = 'list_products_categories';
    protected $fillable = [
        'name',
    ];

    public const CELLPHONE = 1;
    public const SOME_OTHER_CATEGORY = 2;

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
