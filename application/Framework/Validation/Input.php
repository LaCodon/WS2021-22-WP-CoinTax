<?php

namespace Framework\Validation;

/**
 * This class represents user inputs on the server side
 */
final class Input
{
    /**
     * @param int $_method one of INPUT_GET, INPUT_POST, INPUT_SERVER, ...
     * @param string $_name name of the input
     * @param string $_readableName readable name which gets shown to users in error messages etc.
     * @param bool $_required true if the input must be given
     * @param int $_filter any php nativ filter
     * @param array|int $_options any filter options
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