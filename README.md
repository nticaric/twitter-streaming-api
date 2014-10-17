twitter-streaming-api
=====================

A PHP library for consuming Twitter’s Streaming API

##Instalation

The easiest way to install Twitter Streaming API is via [composer](http://getcomposer.org/). Create the following `composer.json` file and run the `php composer.phar install` command to install it.

```json
{
    "require": {
        "nticaric/twitter-streaming-api": "dev-master"
    }
}
```

##Examples

### POST statuses/filter

Returns public statuses that match one or more filter predicates

Usage:
```php

	use Nticaric\Twitter\TwitterStream;

	$stream = new TwitterStream(array(
	    'consumer_key'    => 'my_consumer_key',
	    'consumer_secret' => 'my_consumer_secret',
	    'token'           => 'my_token',
	    'token_secret'    => 'my_token_secret'
	));

	$res = $stream->getStatuses(['track' => 'car'], function($tweet) {
		//prints to the screen statuses as they come along
		print_r($tweet);
	});

```