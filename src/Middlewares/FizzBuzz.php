<?php

namespace Api\Middlewares;

use Api\Models\Stat;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * FizzBuzz middleware to increment hit after call fizzbuzz endpoint
 */
class FizzBuzz implements MiddlewareInterface
{
    /**
     * @param Micro $application
     * @return void
     */
    public function call(Micro $application): void
    {
        $router = $application->getRouter();

        // only on fizzbuzz route
        if ($router->getMatchedRoute()->getName() == 'fizzbuzz') {
            $stat = new Stat();
            $stat->hydrateQueryParams($router->getParams());
            $stat->increaseHit();
        }
    }
}