<?php

namespace Framework\Exception;

use Exception;
use Throwable;

final class IdOverrideDisallowed extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = 'id of object is already set, cannot override it ' . $message;
        parent::__construct($message, $code, $previous);
    }
}