<?php namespace Nticaric\Twitter;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class TwitterRestApi
{
    protected $defaults    = [];
    private $endpoint      = "https://api.twitter.com/1.1/";
    private $mediaEndpoint = "https://upload.twitter.com/1.1/";

    /**
     * If $postAsUser is set to true, the app will post as the user, else it will post as the platform
     */
    public function __construct($credentials, $defaults = [])
    {
        $stack = HandlerStack::create();
        $oauth = new Oauth1($credentials);
        $stack->push($oauth);
        $this->defaults = array_merge($defaults, [
            'base_uri' => $this->endpoint,
            'handler'  => $stack,
            'auth'     => 'oauth'
        ]);

        $this->client = new Client($this->defaults);
    }

    public function getAccountSettings($query = [])
    {
        $response = $this->client->get('account/settings.json', ['query' => $query])->getBody();
        return json_decode($response, true);
    }

    public function getFriendsIds($query = [])
    {
        $response = $this->client->get('friends/ids.json', ['query' => $query])->getBody();
        return json_decode($response, true);
    }

    public function getStatusesUserTimeline($query = [])
    {
        $response = $this->client->get('statuses/user_timeline.json', ['query' => $query])->getBody();
        return json_decode($response, true);
    }

    public function getFollowersIds($query = [])
    {
        $response = $this->client->get('followers/ids.json', ['query' => $query])->getBody();
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
            'form_params' => [
                'id'               => $postID,
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
            'form_params' => [
                'screen_name' => $screenName
            ]
        ])->getBody();

        return json_decode($response, true);
    }

    public function getAccountVerifyCredentials()
    {
        $response = $this->client->get('account/verify_credentials.json')->getBody();
        return json_decode($response, true);
    }

    public function getUsersLookup($query)
    {
        $response = $this->client->get('users/lookup.json', ['query' => $query])->getBody();
        return json_decode($response, true);
    }

    public function getUsersShow($query)
    {
        $response = $this->client->get('users/show.json', ['query' => $query])->getBody();
        return json_decode($response, true);
    }

    public function postUsersLookup($query)
    {
        $response = $this->client->post('users/lookup.json', [
            'form_params' => $query
        ])->getBody();
        return json_decode($response, true);
    }

    public function getUsersSearch($query)
    {
        $response = $this->client->get('users/search.json', ['query' => $query])->getBody();
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

    public function getStatusesShow($query = [])
    {
        $response = $this->client->get('statuses/show.json', ['query' => $query])->getBody();
        return json_decode($response, true);
    }

    public function postDirectMessagesNew($query)
    {
        $response = $this->client->post('direct_messages/events/new.json', [
            'json' => $query
        ])->getBody();

        return json_decode($response, true);
    }

    public function postStatusesUpdate($query)
    {
        $response = $this->client->post('statuses/update.json', [
            'form_params' => $query
        ])->getBody();

        return json_decode($response, true);
    }

    public function getFriendshipsShow($query)
    {
        $response = $this->client->get('friendships/show.json', [
            'query' => $query
        ])->getBody();

        return json_decode($response, true);
    }

    public function postFriendshipsDestroy($screen_name)
    {
        $response = $this->client->post('friendships/destroy.json', [
            'form_params' => [
                'screen_name' => $screen_name
            ]
        ])->getBody();

        return json_decode($response, true);
    }

    public function postFavoritesDestroy($id)
    {
        $response = $this->client->post('favorites/destroy.json', [
            'form_params' => [
                'id' => $id
            ]
        ])->getBody();

        return json_decode($response, true);
    }

    public function postStatusesDestroy($tweetID)
    {
        $response = $this->client->post("statuses/destroy/$tweetID.json")->getBody();
        return json_decode($response, true);
    }

    public function postMediaUpload($filename)
    {
        $imageInfo                  = $this->getImageInfo($filename);
        $this->defaults['base_uri'] = $this->mediaEndpoint;
        $client                     = new Client($this->defaults);
        $response                   = $client->post("media/upload.json", [
            'form_params' => [
                'command'     => "INIT",
                'total_bytes' => $imageInfo['total_bytes'],
                'media_type'  => $imageInfo['media_type']
            ]
        ])->getBody();

        $response = json_decode($response, true);

        $media_id = $response['media_id_string'];

        $postFile = $client->post("media/upload.json", [
            'multipart' => [
                [
                    'name'     => 'command',
                    'contents' => 'APPEND'
                ],
                [
                    'name'     => 'media_id',
                    'contents' => $media_id
                ],
                [
                    'name'     => 'segment_index',
                    'contents' => 0
                ],
                [
                    'name'     => 'media_data',
                    'contents' => base64_encode(file_get_contents($filename))
                ]
            ]
        ])->getBody();

        $response = $client->post("media/upload.json", [
            'form_params' => [
                'command'  => "FINALIZE",
                'media_id' => $media_id
            ]
        ])->getBody();

        return json_decode($response, true);
    }

    public function getImageInfo($filename)
    {
        $media_type  = mime_content_type($filename);
        $total_bytes = filesize($filename);
        $name        = basename($filename);
        return [
            'media_type'  => $media_type,
            'total_bytes' => $total_bytes,
            'name'        => $name
        ];

    }
}
