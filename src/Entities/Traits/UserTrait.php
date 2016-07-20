<?php

namespace DanGreaves\SlackBot\Entities\Traits;

/**
 * Trait providing tools to access the user attached to an entity.
 */
trait UserTrait
{
    /**
     * Resolved user entity.
     *
     * @var Slack\User
     */
    protected $resolvedUser;

    /**
     * Return the user associated with this message.
     *
     * @return Slack\User
     */
    public function getUser()
    {
        if (! $this->resolvedUser) {
            $this->client->getUserById($this->user)->then(function ($user) {
                $this->resolvedUser = $user;
            });
        }

        return $this->resolvedUser;
    }
}
