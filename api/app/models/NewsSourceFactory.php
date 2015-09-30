<?php

namespace Notnull\DailyNews\Models;


use SimplePie;

class NewsSourceFactory
{
    public function create($feedUrl)
    {
        $feed = new SimplePie();
        $feed->set_feed_url($feedUrl);
        $feed->set_cache_location(__dir__ . '/../../cache');
        $success = $feed->init();
        $feed->handle_content_type();

        if ($feed->error() || !$success) {
            return [null, $feed->error()];
        }

        $source = new NewsSource();
        $source->title = html_entity_decode($feed->get_title());
        $source->imageUrl = $feed->get_image_url();
        $source->feed = $feedUrl;
        $source->link = $feed->get_link();

        if ($source->save()) {
            return [$source, null];
        } else {
            return [null, 'Error saving to Database.'];
        }
    }
}