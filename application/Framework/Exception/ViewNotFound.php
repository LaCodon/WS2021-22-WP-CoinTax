<?php

namespace Framework\Exception;

use Exception;
use Throwable;

final class ViewNotFound extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = 'view "' . $message . '" not found';
        parent::__construct($message, $code, $previous);
    }
}