<?php

namespace App\Exceptions;

use Exception;

class SaleAlreadyCancelledException extends Exception
{
    protected $message = "Sale already canceled.";
    protected $code = 409;
}
