<?php

namespace Notnull\DailyNews\Models;

use MongoId;
use Phalcon\Mvc\Collection;
use SimplePie;
use SimplePie_Category;
use SimplePie_Item;

class User extends MyMongo
{
    public $username;
    public $newsSources = [];

    public function initialize()
    {
        // A unique index
        $this->ensureIndex(
            array('username' => 1),
            array('unique' => true, 'dropDups' => true)
        );
    }

    public function getNewsSourcesInfo()
    {
        return NewsSource::find([[
            '_id' => ['$in' => $this->newsSources]
        ]]);
    }

    public function getNewsEntries()
    {
        /** @var NewsDigest $digest */
        $digest = NewsDigest::find([
            ['user' => $this->getId()],
            'sort' => ['date' => -1]
        ]);

        return $digest ?: [];
    }

    public function removeNewsSource(MongoId $feed)
    {
        $index = array_search($feed, $this->newsSources);
        if($index !== false) {
            unset($this->newsSources[$index]);
        }
    }

    public function addNewsSource(MongoId $feed)
    {
        $this->newsSources[] = $feed;
        $this->newsSources = array_unique($this->newsSources);
    }
}