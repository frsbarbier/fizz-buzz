<?php

use Api\Controllers\{Api, Doc};
use Phalcon\Mvc\Micro\Collection as MicroCollection;

$apiCollection = new MicroCollection();
$apiCollection
    ->setHandler(Api::class, true)
    ->setPrefix('/api')
    ->get(
        '/fizzbuzz/{int1:[0-9]+}/{int2:[0-9]+}/{limit:[0-9]+}/{str1:[a-z]+}/{str2:[a-z]+}',
        'fizzbuzz',
        'fizzbuzz'
    )
    ->get(
        '/stats',
        'stats',
        'stats'
    );

return [
    'collections' => [
        'apiCollection' => $apiCollection,
    ],
    'error' => [new Api(), 'error']
];