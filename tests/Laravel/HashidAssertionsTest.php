<?php

namespace Astrotomic\PhpunitAssertions\Tests\Laravel;

use Astrotomic\PhpunitAssertions\Laravel\HashidAssertions;
use Hashids\Hashids;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Vinkla\Hashids\HashidsServiceProvider;

final class HashidAssertionsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            HashidsServiceProvider::class,
        ];
    }

    /**
     * @test
     * @dataProvider hundredTimes
     */
    public function it_can_validate_hashid_on_default_connection(): void
    {
        $salt = static::randomString();
        $length = static::randomInt(0, 32);

        $this->app->make(ConfigContract::class)->set('hashids.default', 'main');
        $this->configureHashids('main', $salt, $length);

        $hashid = new Hashids($salt, $length);
        HashidAssertions::assertHashId($hashid->encode(static::randomInt(0)));
    }

    /**
     * @test
     * @dataProvider hundredTimes
     */
    public function it_can_validate_hashid(): void
    {
        $salt = static::randomString();
        $length = static::randomInt(0, 32);
        $name = static::randomString();

        $this->configureHashids($name, $salt, $length);

        $hashid = new Hashids($salt, $length);
        HashidAssertions::assertHashId(
            $hashid->encode(static::randomInt(0)),
            $name
        );
    }

    /**
     * @test
     * @dataProvider hundredTimes
     */
    public function it_can_validate_hashids(): void
    {
        $salt = static::randomString();
        $length = static::randomInt(0, 32);
        $name = static::randomString();

        $this->configureHashids($name, $salt, $length);

        $hashid = new Hashids($salt, $length);
        $ids = array_map(fn () => static::randomInt(0), range(0, static::randomInt(2, 20)));

        HashidAssertions::assertHashIds(
            $hashid->encode($ids),
            count($ids),
            $name
        );
    }

    protected function configureHashids(string $name, string $salt, int $length): void
    {
        $this->app->make(ConfigContract::class)->set('hashids.connections.'.$name, [
            'salt' => $salt,
            'length' => $length,
        ]);
    }
}
