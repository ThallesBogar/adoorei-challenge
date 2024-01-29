<?php

namespace App\Exceptions;

use Exception;

class SaleStatusCannotHaveNewProductsException extends Exception
{
    protected $message = 'Sale status is beyond the point of adding new products.';
    protected $code = 409;
}
