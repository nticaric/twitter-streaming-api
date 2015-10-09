<?php namespace Nticaric\Twitter;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class TwitterRestApi
{
    private $endpoint = "https://api.twitter.com/1.1/";

    /**
     * If $postAsUser is set to true, the app will post as the user, else it will post as the platform
     */
    public function __construct($credentials)
    {

        $this->client = new Client([
            'base_url' => $this->endpoint,
            'defaults' => ['auth' => 'oauth'],
        ]);

        $oauth = new Oauth1($credentials);

        $this->client->getEmitter()->attach($oauth);
    }

    public function getFriendsIds()
    {
        $response = $this->client->get('friends/ids.json')->getBody();
        return json_decode($response, true);
    }

    public function getApplicationRateLimitStatus()
    {
        $response = $this->client->get('application/rate_limit_status.json')->getBody();
        return json_decode($response, true);
    }

    public function postFavoritesCreate($postID, $includeEntities = false)
    {
        $response = $this->client->post('favorites/create.json', [
            'body' => [
                'id' => $postID,
                'include_entities' => $includeEntities
            ]
        ])->getBody();
        
        return json_decode($response, true);
    }

    public function postStatusesRetweet($tweetID)
    {
        $response = $this->client->post("statuses/retweet/$tweetID.json")->getBody();
        return json_decode($response, true);
    }

    public function postFriendshipCreate($screenName)
    {
        $response = $this->client->post('friendships/create.json', [
            'body' => [
                'screen_name' => $screenName,
            ]
        ])->getBody();
        
        return json_decode($response, true);
    }

    public function getAccountVerifyCredentials()
    {
        $response = $this->client->get('account/verify_credentials.json')->getBody();
        return json_decode($response, true);
    }

    public function getUsersLookup($screen_name)
    {
        $response = $this->client->get('users/lookup.json?screen_name='.$screen_name)->getBody();
        return json_decode($response, true);
    }

    public function getUsersSearch($query)
    {
        $response = $this->client->get('users/search.json?q='.$query)->getBody();
        return json_decode($response, true);
    }

    public function getSearchTweets($query)
    {
        $response = $this->client->get('search/tweets.json', ['query' => $query])->getBody();
        return json_decode($response, true);
    }

    public function getFollowersList($query = [])
    {
        $response = $this->client->get('followers/list.json', ['query' => $query])->getBody();
        return json_decode($response, true);
    }
}
