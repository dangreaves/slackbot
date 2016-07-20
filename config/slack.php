<?php

return [

    // Slack API token (https://my.slack.com/services/new/bot)
    'token' => env('SLACK_TOKEN'),

    // Array of enabled reactors with config arrays
    'reactors' => [
        DanGreaves\SlackBot\Reactors\MentionReactor::class => []
    ],

];
