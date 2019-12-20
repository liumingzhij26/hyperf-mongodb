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

use Hyperf\Contract\ConnectionInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Pool\Connection as BaseConnection;
use Hyperf\Pool\Exception\ConnectionException;
use Hyperf\Pool\Pool;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

/**
 * @method bool select(int $db)
 */
class MongodbConnection extends BaseConnection implements ConnectionInterface
{

    /**
     * @var Client
     */
    protected $connection;

    /**
     * @var array
     */
    protected $config;

    /**
     * Current redis database.
     * @var null|int
     */
    protected $database;

    public function __construct(ContainerInterface $container, Pool $pool, array $config)
    {
        parent::__construct($container, $pool);
        $this->config = $config;

        $this->reconnect();
    }

    public function __call($name, $arguments)
    {
        try {
            $result = $this->connection->{$name}(...$arguments);
        } catch (\Throwable $exception) {
            $result = $this->retry($name, $arguments, $exception);
        }

        return $result;
    }

    public function getActiveConnection()
    {
        if ($this->check()) {
            return $this;
        }

        if (!$this->reconnect()) {
            throw new ConnectionException('Connection reconnect failed.');
        }

        return $this;
    }

    public function reconnect(): bool
    {
        $configuration = new MongodbConfiguration($this->config);
        $mongodb = new Client($configuration->getDsn(), $configuration->getOptions());
        if (!$mongodb) {
            throw new ConnectionException('Connection reconnect failed.');
        }

        $this->connection = $mongodb;
        $this->lastUseTime = microtime(true);

        return true;
    }

    public function close(): bool
    {
        unset($this->connection);

        return true;
    }

    public function release(): void
    {
        parent::release();
    }

    public function setDatabase(?int $database): void
    {
        $this->database = $database;
    }

    protected function retry($name, $arguments, \Throwable $exception)
    {
        $logger = $this->container->get(StdoutLoggerInterface::class);
        $logger->warning(sprintf('Mongodb::__call failed, because ' . $exception->getMessage()));

        try {
            $this->reconnect();
            $result = $this->connection->{$name}(...$arguments);
        } catch (\Throwable $exception) {
            $this->lastUseTime = 0.0;
            throw $exception;
        }

        return $result;
    }
}
