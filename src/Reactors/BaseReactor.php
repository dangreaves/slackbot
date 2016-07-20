<?php

namespace DanGreaves\SlackBot\Reactors;

use DanGreaves\SlackBot\SlackBot;

abstract class BaseReactor
{
    /**
     * Bot instance that this reactor is attached to.
     *
     * @var DanGreaves\SlackBot\SlackBot
     */
    protected $bot;

    /**
     * Set the bot instance for this reactor.
     *
     * @param  DanGreaves\SlackBot\SlackBot $bot
     * @return static
     */
    public function setBot(SlackBot $bot)
    {
        $this->bot = $bot;
    }

    /**
     * Access a config key specifically for this reactor.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function config($key, $default = null)
    {
        return $this->bot->config('reactors.'.get_class($this).'.'.$key, $default);
    }
}
