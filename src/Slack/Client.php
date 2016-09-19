<?php

namespace Slack;

use GuzzleHttp\Client as GuzzleClient;

class Client extends GuzzleClient
{
    public function __construct($team, $token)
    {
        $url = sprintf("https://%s.slack.com", $team);
        parent::__construct(['base_url' => $url]);
        $this->setDefaultOption('query', array('token' => $token));
    }
}
