<?php

function testBcround()
{
    echo '-> testing bcround() ...<br>';

    $bcroundTests = [
        // input, decimal count, expected output
        ['-99.4444449', 0, '-100'],
        ['-99.', 0, '-99'],
        ['-100', 0, '-100'],
        ['100', 0, '100'],
        ['99.35', 2, '99,35'],
        ['99.355', 2, '99,36'],
        ['99.999', 2, '100,00'],
        ['99.9991', 2, '100,00'],
        ['13.999', 2, '14,00'],
        ['13.199', 2, '13,20'],
        ['-13.199', 2, '-13,20'],
        ['-13.199', 2, '-13,20'],
        ['13.199', 3, '13,199'],
        ['13.199', -1, '13'],
        ['13.199', 10, '13,199'],
        ['12', 10, '12'],
        ['12.11', 1, '12,1'],
        ['12.19', 1, '12,2'],
    ];

    foreach ($bcroundTests as $test) {
        $result = bcround($test[0], $test[1]);
        if ($result !== $test[2]) {
            var_dump_pre($test);
            var_dump_pre($result);
            throw new Exception('test failed');
        }
    }
}

echo 'Running tests ...<br>';

testBcround();

echo 'Done running tests.<br>';