<?php

namespace Framework\Validation;

final class Input
{
    /**
     * @param int $_method
     * @param string $_name
     * @param string $_readableName
     * @param bool $_required
     * @param int $_filter
     * @param array|int $_options
     */
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