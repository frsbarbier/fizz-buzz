<?php

return [
    'redis' => function () {
        $redis = new Redis();
        $redis->pconnect('redis');

        return $redis;
    }
];
