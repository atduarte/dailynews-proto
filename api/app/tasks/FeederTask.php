<?php

namespace Notnull\DailyNews\Tasks;


use Notnull\DailyNews\Models\NewsEntry;
use Notnull\DailyNews\Models\NewsSource;

class FeederTask extends BaseTask
{
    public function getNewsAction()
    {
        /** @var NewsSource[] $sources */
        $sources = NewsSource::find();

        $this->log('Will update entries of ' . count($sources) . ' sources.');

        foreach ($sources as $source) {
            $this->log($source->title);
            $source->updateEntries();
        }

        $this->log('Done');
    }

    public function updateStatsAction()
    {
        /** @var NewsEntry[] $entries */
        $entries = NewsEntry::find([[
            'stats.lastUpdated' => ['$lt' => time() - 2*60*60],
            'publishedAt' => ['$gt' => time() - 24*60*60],
        ]]);

        $this->log('Will update relevance of ' . count($entries) . ' entries.');

        foreach ($entries as $entry) {
            $this->log(' -> ' . $entry->title);
            $entry->updateStats();
        }

        $this->log('Done');
    }

    public function updateRelevanceAction()
    {
        /** @var NewsSource[] $sources */
        $sources = NewsSource::find();

        $this->log('Will update relevance of ' . count($sources) . ' sources.');

        foreach ($sources as $source) {
            $this->log($source->title);
            $source->updateRelevance();
        }

        $this->log('Done');
    }
}