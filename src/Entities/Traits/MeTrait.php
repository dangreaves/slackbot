<?php

namespace DanGreaves\SlackBot\Entities\Traits;

/**
 * Trait providing tools to access the user attached to an entity.
 */
trait MeTrait
{
    /**
     * Resolved user entity.
     *
     * @var Slack\User
     */
    protected $resolvedMe;

    /**
     * Return the user associated with this message.
     *
     * @return Slack\User
     */
    public function getMe()
    {
        if (! $this->resolvedMe) {
            $this->client->getUsers()->then(function ($users) {
                $this->resolvedMe = current($users);
            });
        }

        return $this->resolvedMe;
    }
}
