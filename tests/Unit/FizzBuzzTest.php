<?php

namespace Tests\Unit;

use Api\Controllers\Api;
use Phalcon\Http\Message\Exception\InvalidArgumentException;

class FizzBuzzTest extends AbstractUnitTest
{
    /**
     * @var Api
     */
    protected Api $apiController;

    /**
     * Basic test
     * @test
     * @return void
     */
    public function simpleTestCase(): void
    {
        $expected = '1,fizz,3,fizz,buzz,fizz,7,fizz,9,fizzbuzz,11,fizz,13,fizz,buzz';
        $actual = $this->apiController->doFizzBuzz(2, 5, 15, 'fizz', 'buzz');

        $this->assertSame($expected, $actual, 'Test will pass');
    }

    /**
     * Multiple of 1 in int1 test
     * @test
     * @return void
     */
    public function multipleOf1TestCase(): void
    {
        $expected = 'fizz,fizz,fizz,fizz,fizz';
        $actual = $this->apiController->doFizzBuzz(1, 6, 5, 'fizz', 'buzz');

        $this->assertSame($expected, $actual, 'Test will pass');
    }

    /**
     * Multiple of 2 in int2 test
     * @test
     * @return void
     */
    public function multipleOf2TestCase(): void
    {
        $expected = '1,buzz,3,buzz,5,buzz';
        $actual = $this->apiController->doFizzBuzz(7, 2, 6, 'fizz', 'buzz');

        $this->assertSame($expected, $actual, 'Test will pass');
    }

    /**
     * Multiple of 1 in int1 and 2 in int2 test
     * @test
     * @return void
     */
    public function multipleOf1And2TestCase(): void
    {
        $expected = 'fizz,fizzbuzz,fizz,fizzbuzz,fizz,fizzbuzz';
        $actual = $this->apiController->doFizzBuzz(1, 2, 6, 'fizz', 'buzz');

        $this->assertSame($expected, $actual, 'Test will pass');
    }

    /**
     * Invalid int1
     * @test
     * @return void
     */
    public function invalidInt1TestCase(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->apiController->doFizzBuzz(0, 2, 6, 'fizz', 'buzz');
    }

    /**
     * Invalid int2
     * @test
     * @return void
     */
    public function invalidInt2TestCase(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->apiController->doFizzBuzz(1, 0, 6, 'fizz', 'buzz');
    }

    /**
     * Invalid limit < 1
     * @test
     * @return void
     */
    public function tooSmallLimitTestCase(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->apiController->doFizzBuzz(1, 2, 0, 'fizz', 'buzz');
    }

    /**
     * Invalid limit > 1000
     * @test
     * @return void
     */
    public function tooBigLimitTestCase(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->apiController->doFizzBuzz(1, 2, 10000, 'fizz', 'buzz');
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->apiController = new Api();
    }
}