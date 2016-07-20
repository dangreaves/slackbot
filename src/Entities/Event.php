<?php

namespace DanGreaves\SlackBot\Entities;

use Slack\Payload;
use Slack\ApiClient;
use Illuminate\Support\Fluent;

/**
 * Base entity that represents a Slack payload.
 */
class Event extends Fluent
{
    use Traits\MeTrait;

    protected $client;

    public function __construct(ApiClient $client, Payload $payload)
    {
        $this->client = $client;

        parent::__construct($payload->getData());
    }
}
