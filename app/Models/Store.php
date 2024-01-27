<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = "stores";
    protected $fillable = ['name'];

    public const LOJA_ABC_LTDA = 1;

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
