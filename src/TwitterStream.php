<?php namespace Nticaric\Twitter;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Stream\Utils;

class TwitterStream {

	private $endpoint = "https://stream.twitter.com/1.1/";
	
	public function __construct($config) {

		$this->client = new Client([
			'base_url' => $this->endpoint,
			'defaults' => ['auth' => 'oauth', 'stream' => true],
		]);
		$oauth = new Oauth1($config);

		$this->client->getEmitter()->attach($oauth);
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
            ob_flush();
        	flush();
		}

	}
}