<?php

namespace Notnull\DailyNews\Models;

use Phalcon\Mvc\Collection;
use SimplePie;
use SimplePie_Category;
use SimplePie_Item;

class NewsSource extends MyMongo
{
    public $title;
    public $feed;
    public $link;
    public $imageUrl;

    public $lastUpdated = null;

    public $stats = [
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
            array('feed' => 1),
            array('unique' => true, 'dropDups' => true)
        );
    }

    public function updateEntries()
    {
        try {
            // Create a new instance of the SimplePie object
            $feed = new SimplePie();
            $feed->set_feed_url($this->feed);
            $feed->set_cache_location(__dir__ . '/../../cache');

            // Initialize the whole SimplePie object.  Read the feed, process it, parse it, cache it, and
            // all that other good stuff.  The feed's information will not be available to SimplePie before
            // this is called.
            $success = $feed->init();

            // We'll make sure that the right content type and character encoding gets set automatically.
            // This function will grab the proper character encoding, as well as set the content type to text/html.
            $feed->handle_content_type();
        } catch (\Exception $e) {
            return false;
        }

        if ($feed->error() || !$success) {
            return false;
        }

        foreach($feed->get_items() as $item) {
            try {
                /** @var SimplePie_Item $item $newsEntry */
                $exists = NewsEntry::count([
                    ['sourceId' => $this->_id, 'entryId' => $item->get_id()]
                ]);

                if ($exists > 0) {
                    continue;
                }

                $newsEntry = new NewsEntry();
                $newsEntry->sourceId = $this->_id;
                $newsEntry->entryId = $item->get_id();
                $newsEntry->title = $item->get_title();
                $newsEntry->description = $item->get_description();
                $newsEntry->link = $item->get_link();
                $newsEntry->publishedAt = $item->get_date('U');
                $newsEntry->updatedAt = $item->get_updated_date('U');
                $newsEntry->author = $item->get_author() ? $item->get_author()->get_name() : '';

                $newsEntry->categories = [];
                /** @var SimplePie_Category $category */
                if ($item->get_categories()) {
                    foreach ($item->get_categories() as $category) {
                        if ($category->get_term()) {
                            $newsEntry->categories[] = $category->get_term();
                        }
                    }
                }

                $client = new \GuzzleHttp\Client();
                $response = $client->get($item->get_link());
                $newsEntry->link = $response->getEffectiveUrl();

                if ($newsEntry->save() && $newsEntry->updateStats()) {
                    $newsEntry->save();
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return true;
    }

    public function updateRelevance()
    {
        // Reset
        $this->stats = [
            'fb' => [
                'likes' => [],
                'shares' => [],
                'comments' => [],
                'click' => [],
            ],
            'twitter' => [
                'count' => []
            ]
        ];

        /** @var NewsEntry[] $entries */
        $entries = NewsEntry::find([
            'conditions' =>  [
                'sourceId' => $this->_id,
//                'publishedAt' => ['$gt' => time() - 60*60*24]
            ]
        ]);

        // Update Mean
        $count = count($entries);
        foreach ($entries as $entry) {
            $this->stats['fb']['likes'] []= $entry->stats['fb']['likes'];
            $this->stats['fb']['shares'] []= $entry->stats['fb']['shares'];
            $this->stats['fb']['comments'] []= $entry->stats['fb']['comments'];
            $this->stats['fb']['click'] []= $entry->stats['fb']['click'];
            $this->stats['twitter']['count'] []= $entry->stats['twitter']['count'];
        }

        $this->stats['fb']['likes'] = Math::specialMean($this->stats['fb']['likes']);
        $this->stats['fb']['shares'] = Math::specialMean($this->stats['fb']['shares']);
        $this->stats['fb']['comments'] = Math::specialMean($this->stats['fb']['comments']);
        $this->stats['fb']['click'] = Math::specialMean($this->stats['fb']['click']);
        $this->stats['twitter']['count'] = Math::specialMean($this->stats['twitter']['count']);

        // Update Entry Relative Stats
        foreach ($entries as $entry) {
            $updateRelative = function ($social, $type) use (&$entry) {
                if ($this->stats[$social][$type] == 0) {
                    $entry->relativeStats[$social][$type] = 1;
                } else {
                    $entry->relativeStats[$social][$type] =
                        $entry->stats[$social][$type] / $this->stats[$social][$type];
                }
            };

            $updateRelative('fb', 'likes');
            $updateRelative('fb', 'shares');
            $updateRelative('fb', 'comments');
            $updateRelative('fb', 'click');
            $updateRelative('twitter', 'count');

            $entry->updateRelevance();

            $entry->save();
        }

        $this->save();
    }
}