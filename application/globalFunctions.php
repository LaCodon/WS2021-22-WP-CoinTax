<?php

/**
 * Surrounds var_dump with HTML pre tags for better readability
 * @param $obj
 */
function var_dump_pre($obj): void
{
    echo '<pre>';
    var_dump($obj);
    echo '</pre>';
}