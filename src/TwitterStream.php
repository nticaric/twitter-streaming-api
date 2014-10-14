<?php namespace Nticaric\Twitter;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class TwitterStream {

	private $endpoint = "https://stream.twitter.com/1.1";
	
	public function __construct($config) {

		$this->client = new Client([
			'base_url' => $this->endpoint,
			'defaults' => ['auth' => 'oauth']
		]);
		$oauth = new Oauth1($config);

		$this->client->getEmitter()->attach($oauth);
	}

	public function getUsers()
	{
		return $this->client->get('user.json');
	}
}