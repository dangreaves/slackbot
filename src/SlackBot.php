<?php

namespace DanGreaves\SlackBot;

use Slack\RealTimeClient;
use Illuminate\Support\Arr;
use DanGreaves\SlackBot\Entities\Message;
use DanGreaves\SlackBot\Reactors\ReactorInterface;

/**
 * Base manager that stores and executes a collection of commands.
 *
 */
class SlackBot
{
    /**
     * Slack API token.
     *
     * @var string
     */
    protected $token;

    /**
     * Optional command instance for debug output.
     *
     * @var Illuminate\Console\Command
     */
    protected $command;

    /**
     * Optional config array (accessible from reactors).
     *
     * @var array
     */
    protected $config = [];

    /**
     * Array of reactors available for execution.
     *
     * @var array
     */
    protected $reactors = [];

    /**
     * Instantiate the class.
     *
     * @param  string $token
     * @return void
     */
    public function __construct($token)
    {
        $this->setToken($token);
    }

    /**
     * Set the API token for this bot.
     *
     * @param  string $token
     * @return self
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Set optional command instance for this bot.
     *
     * @param  Illuminate\Console\Command $command
     * @return self
     */
    public function setCommand(\Illuminate\Console\Command $command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * Set config array.
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * Retrieve key from config array.
     *
     * @param  string $key
     * @param  string $default
     * @return mixed
     */
    public function config($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Add a new reactor to the execution list.
     *
     * @param  DanGreaves\SlackBot\Reactors\ReactorInterface $reactor
     * @return void
     */
    public function addReactor(ReactorInterface $reactor)
    {
        $reactor->setBot($this);
        array_push($this->reactors, $reactor);
    }

    /**
     * Run the bot process.
     *
     * @return void
     */
    public function run()
    {
        $loop = \React\EventLoop\Factory::create();

        $client = new RealTimeClient($loop);
        $client->setToken($this->token);

        // Respond to RTM messages
        $client->on('message', function ($payload) use ($client) {

            // Output debug
            $this->debug('Message received', $payload->getData());

            // Create a message entity from payload
            $message = new Message($client, $payload);

            // Certain message types are blocked
            if (! $message->isValidForReaction()) return;

            // Execute relevant command
            $this->react($message);

        });

        $this->debug('Connecting to RTM socket');

        $client->connect()->then(function () {
            $this->debug('Connected to RTM socket');
        });

        $loop->run();
    }

    /**
     * Locate and execute a relevant reaction.
     *
     * @param  Larachat\Larabot\Entities\Message $message
     * @return void
     */
    protected function react(Message $message)
    {
        // Loop registered reactors until one hits
        foreach ($this->reactors as $reactor) {

            // This reactor is not relevant, continue looping
            if (! $reactor->shouldReact($message)) continue;

            // Output debug
            $this->debug('Executing reactor '.get_class($reactor));

            // Execute reactor
            $reactor->react($message);

            // Only execute one
            return;

        }

        // Output debug
        $this->debug('No relevant reactor found');
    }

    /**
     * Output a debug message if we have a connected command.
     *
     * @param  string $message
     * @param  array  $data
     * @return void
     */
    protected function debug($message, $data = null)
    {
        // No attached command interface
        if (! $this->command) {
            return;
        }

        // Append debug data if provided
        if ($data) {
            $message .= ' '.json_encode($data);
        }

        // Send message to command interface
        $this->command->info($message);
    }
}
