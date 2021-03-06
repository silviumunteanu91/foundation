<?php

namespace Dyln\Slack;

use Dyln\Sentry\Sentry;
use GuzzleHttp\Client;

class SimpleMessenger
{
    /** @var  Client */
    protected $client;
    protected $hook;

    /**
     * SimpleMessenger constructor.
     * @param $hook
     */
    public function __construct($hook)
    {
        $this->hook = $hook;
    }

    public function send($message, $options = [])
    {
        $options['channel'] = $options['channel'] ?? 'general';
        $options['username'] = $options['username'] ?? '_messenger_';
        $options['icon_emoji'] = $options['icon_emoji'] ?? ':monkey_face:';
        $options['mrkdwn'] = $options['mrkdwn'] ?? true;
        $options['text'] = $message;
        try {
            $response = $this->getClient()->request('POST', $this->hook, ['form_params' => ['payload' => json_encode($options)]]);
        } catch (\Exception $e) {
            Sentry::exception($e);
        }
    }

    private function getClient()
    {
        if (!$this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }
}
