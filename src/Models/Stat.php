<?php

namespace Api\Models;

use Phalcon\Di\Injectable;
use Redis;

/**
 * Stat model to store hit and request parameters
 * @OA\Schema(
 *     description="Stat model",
 *     title="Stat",
 *     required={"hit", "int1", "int2", "limit", "str1", "str2"}
 * )
 * @property Redis $redis
 */
class Stat extends Injectable
{
    /**
     * Key name to store in redis
     * @var string
     */
    protected const KEY = 'stats';

    /**
     * Separator character to separate request parameters
     * @var string
     */
    protected const SEPARATOR = '-';

    /**
     * @OA\Property(
     *     format="int",
     *     description="Number of hits for request",
     *     title="hit",
     *     example="5"
     * )
     * @var int
     */
    protected int $hit;

    /**
     * @OA\Property(
     *     format="int",
     *     description="Request paramater 'int1'",
     *     title="int1",
     *     example="1"
     * )
     * @var int
     */
    protected int $int1;

    /**
     * @OA\Property(
     *     format="int",
     *     description="Request paramater 'int2'",
     *     title="int2",
     *     example="3"
     * )
     * @var int
     */
    protected int $int2;

    /**
     * @OA\Property(
     *     format="int",
     *     description="Request paramater 'limit'",
     *     title="limit",
     *     example="100"
     * )
     * @var int
     */
    protected int $limit;

    /**
     * @OA\Property(
     *     format="string",
     *     description="Request paramater 'str1'",
     *     title="str1",
     *     example="fizz"
     * )
     * @var string
     */
    protected string $str1;

    /**
     * @OA\Property(
     *     format="string",
     *     description="Request paramater 'str2'",
     *     title="str2",
     *     example="buzz"
     * )
     * @var string
     */
    protected string $str2;

    /**
     * Hydrate object from request parameters
     * @param array $queryParams Request parameters
     * like ['int1' => 1, 'int2' => 2, 'limit' => 100, 'str1' => 'fizz', 'str2' => 'buzz']
     * @return void
     */
    public function hydrateQueryParams(array $queryParams): void
    {
        $this->int1 = intval($queryParams['int1']);
        $this->int2 = intval($queryParams['int2']);
        $this->limit = intval($queryParams['limit']);
        $this->str1 = $queryParams['str1'];
        $this->str2 = $queryParams['str2'];
    }

    /**
     * Increase by 1 hit for member
     * @return void
     */
    public function increaseHit()
    {
        if ($this->redis->isConnected()) {
            $member = $this->prepareMember();

            $this->redis->zIncrBy(Stat::KEY, 1, $member);
        }
    }

    /**
     * Create member from properties, like int1-int2-limit-str1-str2
     * @return string
     */
    public function prepareMember(): string
    {
        return implode(self::SEPARATOR, [
            $this->int1,
            $this->int2,
            $this->limit,
            $this->str1,
            $this->str2
        ]);
    }

    /**
     * Return top request
     * @return array|object
     */
    public function getTopRequest()
    {
        $topRequest = (object)[];

        if ($this->redis->isConnected()) {
            $greatestHit = $this->redis->zRevRange(self::KEY, 0, 0, true);

            if (!empty($greatestHit)) {
                $member = key($greatestHit);
                $hit = intval($greatestHit[$member]);

                $topRequest = $this->createTopRequest($hit, $member);
            }
        }

        return $topRequest;
    }

    /**
     * Create array from hit and member
     * @param int $hit Hit number for request
     * @param string $member Redis member like int1-int2-limit-str1-str2
     * @return array
     */
    public function createTopRequest(int $hit, string $member): array
    {
        $this->hydrateHitAndMember($hit, $member);

        return $this->toArray();
    }

    /**
     * Hydrate object from hit and member
     * @param int $hit Hit number for request
     * @param string $member Redis member like int1-int2-limit-str1-str2
     * @return void
     */
    public function hydrateHitAndMember(int $hit, string $member)
    {
        $this->hit = $hit;

        $this->hydrateMember($member);
    }

    /**
     * Hydrate object from member
     * @param string $member Redis member ex (int1-int2-limit-str1-str2)
     * @return void
     */
    public function hydrateMember(string $member): void
    {
        $queryParams = $this->prepareQueryParamsFromMember($member);

        $this->int1 = $queryParams[0];
        $this->int2 = $queryParams[1];
        $this->limit = $queryParams[2];
        $this->str1 = $queryParams[3];
        $this->str2 = $queryParams[4];
    }

    /**
     * Convert string member in array
     * @param string $member
     * @return array
     */
    public function prepareQueryParamsFromMember(string $member): array
    {
        $queryParams = explode(self::SEPARATOR, $member);
        return $this->convertNumericToIntInArray($queryParams);
    }

    /**
     * Convert numeric value to int in array
     * @param $array
     * @return array
     */
    protected function convertNumericToIntInArray($array): array
    {
        return array_map(function ($value) {
            if (is_numeric($value)) {
                return intval($value);
            }

            return $value;
        }, $array);
    }

    /**
     * Return array from object properties
     * @return array
     */
    public function toArray(): array
    {
        return [
            'hit' => $this->hit,
            'int1' => $this->int1,
            'int2' => $this->int2,
            'limit' => $this->limit,
            'str1' => $this->str1,
            'str2' => $this->str2,
        ];
    }
}