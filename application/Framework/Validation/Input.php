<?php

namespace Framework\Validation;

final class Input
{
    public function __construct(
        public int       $_method,
        public string    $_name,
        public string    $_readableName,
        public bool      $_required = true,
        public int       $_filter = FILTER_DEFAULT,
        public array|int $_options = 0,
    )
    {
    }
}