<?php

use Nticaric\Twitter\TwitterStream;
use Carbon\Carbon;

class TwitterStreamTest extends \PHPUnit_Framework_TestCase {

	public function testOAuthConnection()
	{
		$stream = new TwitterStream(array(
			'consumer_key'    => 'my_key',
			'consumer_secret' => 'my_secret',
			'token'           => 'my_token',
			'token_secret'    => 'my_token_secret'
		));

		$stream->getStatuses(
            ['track' => 'car'], function($tweet) {
                echo $tweet;
        });
	}
}