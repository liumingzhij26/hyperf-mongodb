<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Hyperf\Mongodb;

use Hyperf\Utils\Context;
use MongoDB\Client;
use Hyperf\Mongodb\Exception\InvalidMongodbConnectionException;
use Hyperf\Mongodb\Pool\PoolFactory;

class Mongodb
{

    /**
     * @var PoolFactory
     */
    protected $factory;

    /**
     * @var string
     */
    protected $poolName = 'default';

    /**
     *
     *
     * Mongodb constructor.
     * @param PoolFactory $factory
     * @return Client
     */
    public function __construct(PoolFactory $factory)
    {
        $this->factory = $factory;
    }

    public function __call($name, $arguments)
    {
        // Get a connection from coroutine context or connection pool.
        $hasContextConnection = Context::has($this->getContextKey());
        $connection = $this->getConnection($hasContextConnection);

        try {
            $connection = $connection->getConnection();
            // Execute the command with the arguments.
            $result = $connection->{$name}(...$arguments);
        } finally {
            // Release connection.
            if (!$hasContextConnection) {
                $connection->release();
            }
        }

        return $result;
    }

    /**
     * @param $hasContextConnection
     * @return MongodbConnection
     */
    private function getConnection($hasContextConnection): MongodbConnection
    {
        $connection = null;
        if ($hasContextConnection) {
            $connection = Context::get($this->getContextKey());
        }
        if (!$connection instanceof MongodbConnection) {
            $pool = $this->factory->getPool($this->poolName);
            $connection = $pool->get();

        }
        if (!$connection instanceof MongodbConnection) {
            throw new InvalidMongodbConnectionException('The connection is not a valid RedisConnection.');
        }
        return $connection;
    }

    /**
     * The key to identify the connection object in coroutine context.
     */
    private function getContextKey(): string
    {
        return sprintf('mongodb.connection.%s', $this->poolName);
    }
}
