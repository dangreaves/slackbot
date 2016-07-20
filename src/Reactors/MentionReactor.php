<?php

namespace DanGreaves\SlackBot\Reactors;

use Carbon\Carbon;
use DanGreaves\SlackBot\Entities\Message;

/**
 * Reacts with a message whenever the bot is mentioned.
 *
 * @author Dan Greaves <dan@dangreaves.com>
 */
class MentionReactor extends BaseReactor implements ReactorInterface
{
    /**
     * Timestamp for when we last reacted.
     *
     * @var Carbon\Carbon
     */
    protected $lastMessageSentAt;

    /**
     * Respond to all messages that contain the bot name as long as we haven't
     * already reacted in the last 30 minutes.
     *
     * @param  DanGreaves\SlackBot\Entities\Message $message
     * @return void
     */
    public function shouldReact(Message $message)
    {
        return (
            $message->mentionsMe()
            && (
                ! $this->lastMessageSentAt
                || $this->lastMessageSentAt->lt(Carbon::now()->subMinutes(30))
            )
        );
    }

    /**
     * Respond to mentions with a message.
     *
     * @param  DanGreaves\SlackBot\Entities\Message $message
     * @return void
     */
    public function react(Message $message)
    {
        // Fetch reaction from config
        $reaction = $this->config('message', 'Bleep. Bloop. Hey :username, I\'m alive but I\'m a pretty dull fellow at the mo. You can contribute to my commands at http://github.com/dangreaves/slackbot ⚡');

        // Add username to message
        $reaction = str_replace(
            ':username',
            $message->getUser()->getFirstName() ?: $message->getUser()->getUsername(),
            $reaction
        );

        // Reply with the message
        $message->reply($reaction);

        // Update the last sent timestamp
        $this->lastMessageSentAt = Carbon::now();
    }
}
