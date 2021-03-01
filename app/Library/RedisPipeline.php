<?php

namespace App\Library;


class RedisPipeline
{
    /**
     * @var \Redis $client
     */
    protected $client;

    /**
     * @var array
     */
    protected $commandQueue;

    /**
     * @var bool
     */
    protected $isTransfer  = false;

    /**
     * @var bool
     */
    protected static $running = false;

    private static $instance = null;
    /**
     * @var array
     */
    protected $responses = [];

    private function __construct($client, $isTransfer = false)
    {
        $this->client = $client;
        if(!self::$running) {
            if($this->isTransfer) {
                $this->client->multi(\Redis::MULTI);
            }else{
                $this->client->multi(\Redis::PIPELINE);
            }
            self::$running = true;
        }
        $this->commandQueue = [];
        $this->isTransfer;
    }

    /**
     * Queues a command into the pipeline buffer.
     *
     * @param string $method    Command ID.
     * @param array  $arguments Arguments for the command.
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $this->client->$method(...$arguments);
        return $this;
    }

    public function exec()
    {
        $this->responses = $this->client->exec();
        self::$running = false;
    }

    /**
     * Handles the actual execution of the whole pipeline.
     *
     * @param mixed $callable Optional callback for execution.
     *
     * @return mixed
     */
    public function execute($callable = null)
    {
        return is_null($callable)
            ? $this
            : tap($this, $callable)->exec();

    }

    public static function getInstance($client, $isTransfer = false) : RedisPipeline
    {
        if(self::$instance === null) {
            self::$instance = new self($client, $isTransfer);
        }

        return self::$instance;
    }
}