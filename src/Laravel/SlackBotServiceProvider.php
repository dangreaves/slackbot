<?php

namespace DanGreaves\SlackBot\Laravel;

use DanGreaves\SlackBot\SlackBot;
use Illuminate\Support\ServiceProvider;

class SlackBotServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->configure('slack');

        $this->app->singleton(SlackBot::class, function ($app) {

            // Instiate the bot with a token
            $bot = new SlackBot(config('slack.token'));

            // Set config array
            $bot->setConfig(config('slack'));

            // Add reactors from config file
            foreach (array_keys(config('slack.reactors')) as $reactorClass) {
                $bot->addReactor(new $reactorClass);
            }

            // Return bot
            return $bot;

        });
    }
}
