<?php

namespace DanGreaves\SlackBot\Reactors;

use DanGreaves\SlackBot\Entities\Message;

interface ReactorInterface
{
    /**
     * Return whether or not this reactor should react to the message.
     *
     * @param  DanGreaves\SlackBot\Entities\Message $message
     * @return boolean
     */
    public function shouldReact(Message $message);

    /**
     * React to the provided message.
     *
     * @param  DanGreaves\SlackBot\Entities\Message $message
     * @return boolean
     */
    public function react(Message $message);
}
