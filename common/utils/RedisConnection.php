<?php

namespace app\common\utils;

use yii\base\Component;

class RedisConnection extends Component
{
    public string $host = '127.0.0.1';

    public int $port = 6379;

    public ?string $password = null;

    public ?\Redis $redis = null;

    public function init()
    {
        parent::init();
        $this->redis = new \Redis();
        $this->redis->connect($this->host, $this->port);
    }
}
