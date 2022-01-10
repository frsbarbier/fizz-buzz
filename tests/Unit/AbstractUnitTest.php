<?php

namespace Tests\Unit;

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Incubator\Test\PhpUnit\UnitTestCase;
use PHPUnit\Framework\IncompleteTestError;

abstract class AbstractUnitTest extends UnitTestCase
{
    /**
     * @var bool
     */
    private bool $loaded = false;

    public function __destruct()
    {
        if (!$this->loaded) {
            throw new IncompleteTestError(
                "Please run parent::setUp()."
            );
        }
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $di = new FactoryDefault();

        Di::reset();
        Di::setDefault($di);

        $this->loaded = true;
    }
}