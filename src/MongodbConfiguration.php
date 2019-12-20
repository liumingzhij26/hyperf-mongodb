<?php


namespace Tf\HyperfMongodb;


use Tf\HyperfMongodb\Exception\InvalidMongodbConnectionException;

class MongodbConfiguration
{
    /**
     * @return array
     */
    public function getHost(): array
    {
        return $this->host;
    }

    /**
     * @param array $host
     * @return MongodbConfiguration
     */
    public function setHost(array $host): MongodbConfiguration
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return MongodbConfiguration
     */
    public function setPort(int $port): MongodbConfiguration
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return MongodbConfiguration
     */
    public function setUsername(string $username): MongodbConfiguration
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return MongodbConfiguration
     */
    public function setPassword(string $password): MongodbConfiguration
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return MongodbConfiguration
     */
    public function setOptions(array $options): MongodbConfiguration
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @param string $database
     * @return MongodbConfiguration
     */
    public function setDatabase(string $database): MongodbConfiguration
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @return array
     */
    public function getPool(): array
    {
        return $this->pool;
    }

    /**
     * @param array $pool
     * @return MongodbConfiguration
     */
    public function setPool(array $pool): MongodbConfiguration
    {
        $this->pool = $pool;
        return $this;
    }

    /**
     * @var array
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $options;

    /**
     * @var string
     */
    private $database;

    /**
     * @var array
     */
    private $pool;

    /**
     * @var array
     */
    protected $config;

    public function __construct(array $host, int $port, string $username, string $password, string $database, array $options, array $pool)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->options = $options;
        $this->pool = $pool;
    }

    public function getDsn()
    {
        $hosts = [];
        if (!$this->getHost()) {
            throw new InvalidMongodbConnectionException('error mongodb config host');
        }
        foreach ($this->getHost() as $host) {
            $hosts[] = sprintf("%s:%d", $host, $this->getPort());
        }
        return "mongodb://" . implode(',', $hosts);
    }
}

