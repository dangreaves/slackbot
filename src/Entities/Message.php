<?php

namespace DanGreaves\SlackBot\Entities;

/**
 * Entity that represents a Slack message.
 */
class Message extends Event
{
    use Traits\UserTrait, Traits\ChannelTrait;

    /**
     * Return the subtype for this message.
     *
     * @return string|null
     */
    public function getSubType()
    {
        return $this->subtype;
    }

    /**
     * Return true if this is an ordinary (no subtype) message.
     *
     * @return boolean
     */
    public function isOrdinary()
    {
        return ! $this->subtype;
    }

    /**
     * Return whether or not the message contains the given needles.
     *
     * @param  array|string $needles
     * @param  string       $operator
     * @return boolean
     */
    public function contains($needles, $operator = 'OR')
    {
        if (! is_array($needles)) {
            return $this->textContains($needles);
        }

        $matchCount = 0;

        foreach ($needles as $needle) {

            if ($this->textContains($needle)) {
                $matchCount++;

                if ('OR' == $operator) {
                    break;
                }
            }

        }

        if ('OR' == $operator && 0 < $matchCount) {
            return true;
        }

        if ('AND' == $operator && $matchCount == count($needles)) {
            return true;
        }

        return false;
    }

    /**
     * Returnt true if the message text contains ALL the given needles.
     *
     * @param  array $needles
     * @return boolean
     */
    public function containsAll($needles)
    {
        return $this->contains($needles, 'AND');
    }

    /**
     * Returnt true if the message text contains ANY of the given needles.
     *
     * @param  array $needles
     * @return boolean
     */
    public function containsAny($needles)
    {
        return $this->contains($needles, 'OR');
    }

    /**
     * Return whether or not the message text contains the given $needle.
     *
     * Case-insensitive.
     *
     * @param  array|string $needle
     * @return boolean
     */
    public function textContains($needle)
    {
        return false !== strpos(
            strtolower($this->text),
            strtolower($needle)
        );
    }

    /**
     * Return whether or not this message is directed at me.
     *
     * @return boolean
     */
    public function isDirectedAtMe()
    {
        return $this->contains('@'.$this->getMe()->getId());
    }

    /**
     * Return whether or not this message mentions me.
     *
     * @return boolean
     */
    public function mentionsMe()
    {
        return $this->isDirectedAtMe() || $this->contains($this->getMe()->getUsername());
    }

    /**
     * Return true if this message didn't come from the bot itself.
     *
     * This is needed as sometimes on reconnect, the client will receive the
     * last message. If the last message was actually the bot, we can end
     * up in a loop of the bot talking to itself.
     *
     * @return boolean
     */
    public function isNotFromSelf()
    {
        return $this->getUser()->getId() !== $this->getMe()->getId();
    }

    /**
     * Reply to this message.
     *
     * @param  string $message
     * @return void
     */
    public function reply($message)
    {
        $this->client->send($message, $this->getChannel());
    }

    /**
     * Return whether or not reaction lookup should be attempted for this
     * message. Some message types are skipped completely.
     *
     * @return boolean
     */
    public function isValidForReaction()
    {
        return $this->isOrdinary() && $this->isNotFromSelf();
    }
}
