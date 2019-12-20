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

use Hyperf\Contract\ConfigInterface;
use MongoDB\Client;
use Hyperf\Mongodb\Exception\InvalidMongodbProxyException;

class MongodbFactory
{
    /**
     * @var MongodbProxy[]
     */
    protected $proxies;

    public function __construct(ConfigInterface $config)
    {
        $redisConfig = $config->get('mongodb');

        foreach ($redisConfig as $poolName => $item) {
            $this->proxies[$poolName] = make(MongodbProxy::class, ['pool' => $poolName]);
        }
    }

    /**
     * @param string $poolName
     * @return MongodbProxy|Client
     */
    public function get(string $poolName)
    {
        $proxy = $this->proxies[$poolName] ?? null;
        if (!$proxy instanceof MongodbProxy) {
            throw new InvalidMongodbProxyException('Invalid mongodb proxy.');
        }

        return $proxy;
    }
}
