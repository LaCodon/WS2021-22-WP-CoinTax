<?php

namespace Framework;

final class Context
{
    public function __construct(
        public Database $_database
    )
    {
    }
}