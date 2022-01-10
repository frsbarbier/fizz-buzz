<?php

namespace Tests\Unit;

use Api\Models\Stat;

class StatTest extends AbstractUnitTest
{
    /**
     * @var Stat
     */
    protected Stat $stat;

    /**
     * @test
     * @return void
     */
    public function getMemberTestCase(): void
    {
        $queryParams = [
            'int1' => 1,
            'int2' => 2,
            'limit' => 100,
            'str1' => 'fizz',
            'str2' => 'buzz'
        ];

        $this->stat->hydrateQueryParams($queryParams);

        $expected = '1-2-100-fizz-buzz';
        $actual = $this->stat->prepareMember();

        $this->assertSame($expected, $actual, 'Test will pass');
    }

    /**
     * @test
     * @return void
     */
    public function convertMemberTestCase(): void
    {
        $member = '1-2-100-fizz-buzz';

        $expected = [1, 2, 100, 'fizz', 'buzz'];
        $actual = $this->stat->prepareQueryParamsFromMember($member);

        $this->assertSame($expected, $actual, 'Test will pass');
    }

    /**
     * @test
     * @return void
     */
    public function topRequestTestCase(): void
    {
        $hit = 5;
        $member = '1-2-100-fizz-buzz';

        $expected = [
            'hit' => 5,
            'int1' => 1,
            'int2' => 2,
            'limit' => 100,
            'str1' => 'fizz',
            'str2' => 'buzz'
        ];
        $actual = $this->stat->createTopRequest($hit, $member);

        $this->assertSame($expected, $actual, 'Test will pass');
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->stat = new Stat();
    }
}