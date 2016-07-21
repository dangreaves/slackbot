<?php

namespace DanGreaves\SlackBot\Laravel;

use Illuminate\Console\Command;
use DanGreaves\SlackBot\SlackBot;

class SlackBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slackbot:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the Slack bot';

    /**
     * The bot service.
     *
     * @var DanGreaves\SlackBot\SlackBot
     */
    protected $bot;

    /**
     * Create a new command instance.
     *
     * @param  DanGreaves\SlackBot\SlackBot $bot
     * @return void
     */
    public function __construct(SlackBot $bot)
    {
        parent::__construct();

        $bot->setCommand($this);

        $this->bot = $bot;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->bot->run();
    }
}
