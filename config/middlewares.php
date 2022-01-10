<?php

use Api\Middlewares\FizzBuzz as FizzBuzzMidlleware;

return [
    [
        'type' => 'after',
        'class' => FizzBuzzMidlleware::class
    ]
];