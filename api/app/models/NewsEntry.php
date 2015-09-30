<?php

namespace Notnull\DailyNews\Models;

class NewsEntry extends MyMongo
{
    public $sourceId;
    public $entryId;

    public $title;
    public $description;
    public $link;
    public $publishedAt;
    public $updatedAt;
    public $author;
    public $categories;

    public $relevance;

    public $stats = [
        'lastUpdated' => 0,
        'fb' => [
            'likes' => 0,
            'shares' => 0,
            'comments' => 0,
            'click' => 0,
        ],
        'twitter' => [
            'count' => 0
        ],
        'linkedin' => [
            'count' => 0
        ]
    ];

    public $relativeStats = [
        'fb' => [
            'likes' => 0,
            'shares' => 0,
            'comments' => 0,
            'click' => 0,
        ],
        'twitter' => [
            'count' => 0
        ],
        'linkedin' => [
            'count' => 0
        ]
    ];

    public function initialize()
    {
        // A unique index
        $this->ensureIndex(
            array('entryId' => 1, 'sourceId' => 1),
            array('unique' => true, 'dropDups' => true)
        );
    }

    public function updateRelevance()
    {
        $FBLIKES = 2;
        $FBSHARES = 3.5;
        $FBCOMMENTS = 0;
        $FBCLICKS = 0;
        $TWEETS = 3;

        $this->relevance =
            $this->relativeStats['fb']['likes'] * $FBLIKES +
            $this->relativeStats['fb']['shares'] * $FBSHARES +
            $this->relativeStats['fb']['comments'] * $FBCOMMENTS +
            $this->relativeStats['fb']['click'] * $FBCLICKS +
            $this->relativeStats['twitter']['count'] * $TWEETS;

        $this->relevance = $this->relevance / ($FBLIKES + $FBSHARES + $FBCOMMENTS + $FBCLICKS + $TWEETS);
    }

    public function updateStats()
    {
        $changed = false;

        $this->link = explode('?', $this->link)[0];

        $changed = $this->updateFbStats() ?: $changed;
        $changed = $this->updateTwitterStats() ?: $changed;

        $this->stats['lastUpdated'] = time();
        $this->save();

        return $changed;
    }

    private function updateTwitterStats()
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get('http://urls.api.twitter.com/1/urls/count.json?url=' . $this->link);
        } catch (\Exception $e) {
            return false;
        }

        if ($response->getStatusCode() != 200) {
            return false;
        }

        $response = $response->json();
        if (isset($response['error'])) {
            return false;
        }

        $this->stats['twitter'] = [
            'count' => $response['count']
        ];

        return true;
    }

    private function updateFbStats()
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://api.facebook.com/method/links.getStats?format=json&urls=' . $this->link);
        } catch (\Exception $e) {
            return false;
        }

        if ($response->getStatusCode() != 200) {
            return false;
        }

        $response = $response->json();
        if (isset($response['error_code']) || count($response) != 1) {
            return false;
        }

        $response = $response[0];

        $this->stats['fb'] = [
            'likes' => $response['like_count'],
            'shares' => $response['share_count'],
            'comments' => $response['comment_count'],
            'click' => $response['click_count'],
        ];

        return true;
    }

    public function getSourceEntity()
    {
        return NewsSource::findById($this->sourceId);
    }
}