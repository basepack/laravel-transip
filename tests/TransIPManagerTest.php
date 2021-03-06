<?php

/*
 * This file is part of Laravel TransIP.
 *
 * (c) Hidde Beydals <hello@hidde.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TransIP\Tests\Laravel;

use GrahamCampbell\TestBench\AbstractTestCase as AbstractTestBenchTestCase;
use TransIP\Laravel\TransIPFactory;
use TransIP\Laravel\TransIPManager;
use Illuminate\Contracts\Config\Repository;
use Mockery;
use TransIP\Client;

/**
 * @author Hidde Beydals <hello@hidde.co>
 */
class TransIPManagerTest extends AbstractTestBenchTestCase
{
    public function testCreateConnection()
    {
        $config = ['path' => __DIR__];

        $manager = $this->getManager($config);

        $manager->getConfig()->shouldReceive('get')->once()
            ->with('transip.default')->andReturn('transip');

        $this->assertSame([], $manager->getConnections());

        $return = $manager->connection();

        $this->assertInstanceOf(Client::class, $return);

        $this->assertArrayHasKey('transip', $manager->getConnections());
    }

    protected function getManager(array $config)
    {
        $repository = Mockery::mock(Repository::class);
        $factory = Mockery::mock(TransIPFactory::class);

        $manager = new TransIPManager($repository, $factory);

        $manager->getConfig()->shouldReceive('get')->once()
            ->with('transip.connections')->andReturn(['transip' => $config]);

        $config['name'] = 'transip';

        $manager->getFactory()->shouldReceive('create')->once()
            ->with($config)->andReturn(Mockery::mock(Client::class));

        return $manager;
    }
}
