<?php namespace Nticaric\Twitter;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Stream\Utils;
use GuzzleHttp\Event\AbstractTransferEvent;
use GuzzleHttp\Subscriber\Retry\RetrySubscriber;

class TwitterStream {

    private $endpoint = "https://stream.twitter.com/1.1/";
    private $retries  = 16;
    
    public function __construct($config) {

        $this->client = new Client([
            'base_url' => $this->endpoint,
            'defaults' => ['auth' => 'oauth', 'stream' => true],
        ]);
        $oauth = new Oauth1($config);

        $retry = new RetrySubscriber([
            'filter' => RetrySubscriber::createStatusFilter([503]),
            'max'    => $this->retries,
        ]);

        $this->client->getEmitter()->attach($retry);
        $this->client->getEmitter()->attach($oauth);
    }

    public function setRetries($num)
    {
        $this->retries = $num;
    }

    public function getStatuses($param, $callback)
    {
        $response = $this->client->post('statuses/filter.json', [
            'body'   => $param
        ]);

        $body = $response->getBody();

        while (!$body->eof()) {
            $line = Utils::readLine($body);
            $data = json_decode($line, true);
            if(is_null($data)) continue;
            call_user_func($callback, $data);
            if( ob_get_level() > 0 ) ob_flush();
            flush();
        }

    }
}