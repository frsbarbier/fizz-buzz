<?php

namespace Api\Controllers;

use Api\Models\Stat;
use Phalcon\Http\Message\Exception\InvalidArgumentException;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller;
use Throwable;

/**
 * FizzBuzz Rest API controller
 * @OA\Info(title="FizzBuzz REST API", version="0.1")
 * @OA\Server(
 *     url="http://127.0.0.1:8080/api",
 *     description="Server REST"
 * )
 * @OA\Response(
 *   response=404,
 *   description="Not Found error response",
 *   @OA\JsonContent(ref="#/components/schemas/Error")
 * )
 */
class Api extends Controller
{
    /**
     * @var int
     */
    protected const START = 1;

    /**
     * @var int
     */
    protected const LIMIT = 1000;

    /**
     * @var string
     */
    protected const SEPARATOR = ',';

    /**
     * API FizzBuzz endpoint
     * @OA\Get(
     *   summary="FizzBuzz endpoint",
     *   path="/fizzbuzz/{int1}/{int2}/{limit}/{str1}/{str2}",
     *   @OA\Parameter(
     *     name="int1",
     *     in="path",
     *     required=true,
     *     description="A number whose multiple will be replaced by 'str1' parameter",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="int2",
     *     in="path",
     *     required=true,
     *     description="A number whose multiple will be replaced by 'str2' parameter",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="limit",
     *     in="path",
     *     required=true,
     *     description="A number to limit list of fizz-buzz numbers. Must be between 1 and 1000",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="str1",
     *     in="path",
     *     required=true,
     *     description="String that will replace by 'int1' parameter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="str2",
     *     in="path",
     *     required=true,
     *     description="String that will replace by 'int2' parameter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Fizzbuzz success response",
     *     @OA\JsonContent(
     *       type="string",
     *       description="Fizzbuzz string list separate by comma",
     *       example="1,fizz,3,fizz,buzz,fizz,7,fizz,9,fizzbuzz,11,fizz,13,fizz,buzz")
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Fizzbuzz error response",
     *     @OA\JsonContent(ref="#/components/schemas/Error")
     *   )
     * )
     * @param int $int1
     * @param int $int2
     * @param int $limit
     * @param string $str1
     * @param string $str2
     * @return ResponseInterface
     */
    public function fizzBuzz(int $int1, int $int2, int $limit, string $str1, string $str2): ResponseInterface
    {
        $content = $this->doFizzBuzz($int1, $int2, $limit, $str1, $str2);

        return $this->response
            ->setJsonContent($content)
            ->send();
    }

    /**
     * Return all numbers between 1 and limit separate by separator chararacter (default ',').
     * All multiples of int1 are replaced by str1
     * All multiples of int2 are replaced by str2
     * All multiples of both int1 and int2 are replaced by str1str2
     * @param int $int1
     * @param int $int2
     * @param int $limit
     * @param string $str1
     * @param string $str2
     * @return string
     */
    public function doFizzBuzz(int $int1, int $int2, int $limit, string $str1, string $str2): string
    {
        $result = '';
        $separator = '';

        if ($int1 < self::START || $int2 < self::START) {
            throw new InvalidArgumentException(
                sprintf("Parameter 'int1' or 'int2' must be greater than %d.", self::START),
                400
            );
        }

        if ($limit < self::START || $limit > self::LIMIT) {
            throw new InvalidArgumentException(
                sprintf("Parameter 'limit' must be between %d and %d.", self::START, self::LIMIT),
                400
            );
        }

        for ($i = self::START; $i <= $limit; $i++) {
            if ($i > self::START) {
                $separator = self::SEPARATOR;
            }

            if ($i % $int1 == 0 && $i % $int2 == 0) {
                $result .= $separator . $str1 . $str2;
            } elseif ($i % $int1 == 0) {
                $result .= $separator . $str1;
            } elseif ($i % $int2 == 0) {
                $result .= $separator . $str2;
            } else {
                $result .= $separator . $i;
            }
        }

        return $result;
    }

    /**
     * API Stats endpoint
     * @OA\Get(
     *   path="/stats",
     *   summary="Stats endpoint",
     *   @OA\Response(
     *     response=200,
     *     description="Stat success response",
     *     @OA\JsonContent(
     *       oneOf={
     *          @OA\Schema(ref="#/components/schemas/Stat"),
     *          @OA\Schema(),
     *       }
     *     )
     *   )
     * )
     * @return ResponseInterface
     */
    public function stats(): ResponseInterface
    {
        $stat = new Stat();
        $content = $stat->getTopRequest();

        return $this->response
            ->setJsonContent($content)
            ->send();
    }

    /**
     * Error handler
     * @OA\Schema(
     *   schema="Error",
     *   @OA\Property(
     *     property="code",
     *     type="integer",
     *     description="Error code",
     *     title="code",
     *     example="500"
     *   ),
     *   @OA\Property(
     *     property="status",
     *     type="string",
     *     description="Error status",
     *     title="status",
     *     example="error"
     *   ),
     *   @OA\Property(
     *     property="message",
     *     type="string",
     *     description="Error message",
     *     title="message",
     *     example="An error occured."
     *   )
     * )
     * @param Throwable $exception
     * @return ResponseInterface
     */
    public function error(Throwable $exception): ResponseInterface
    {
        $statusCode = $exception->getCode() != 0 ? $exception->getCode() : 500;

        return $this->response
            ->setStatusCode($statusCode)
            ->setJsonContent([
                                 'code' => $exception->getCode(),
                                 'status' => 'error',
                                 'message' => $exception->getMessage(),
                             ])
            ->send();
    }
}