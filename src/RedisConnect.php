<?php
class RedisConnect {
    private $redisHost = 'redis'; // Redis hostname aligns with the Docker service name
    private $redisPort = 6379;

    public function connect() {
        try {
            $redis = new Redis();
            $redis->connect($this->redisHost, $this->redisPort);
            return $redis;
        } catch (Exception $e) {
            echo 'Redis Connection Error: ' . $e->getMessage();
        }
    }
}
?>
