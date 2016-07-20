<?php

namespace DanGreaves\SlackBot\Entities\Traits;

/**
 * Trait providing tools to access the channel attached to an entity.
 */
trait ChannelTrait
{
    /**
     * Resolved channel entity.
     *
     * @var Slack\Channel
     */
    protected $resolvedChannel;

    /**
     * Return the channel associated with this entity.
     *
     * @return Slack\Channel
     */
    public function getChannel()
    {
        if (! $this->resolvedChannel) {
            $this->client->getChannelById($this->channel)->then(function ($channel) {
                $this->resolvedChannel = $channel;
            });
        }

        return $this->resolvedChannel;
    }
}
