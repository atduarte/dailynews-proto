<?php

namespace Notnull\DailyNews\Models;

use Phalcon\Mvc\Collection;

class NewsDigest extends MyMongo
{
    public $user;
    public $date; // timestamp
    public $dailyNews = [];

    public function getNewsEntries()
    {
        return $this->dailyNews;
    }
}