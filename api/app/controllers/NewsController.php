<?php

namespace Notnull\DailyNews\Controllers;

use Notnull\DailyNews\Models\NewsSource;
use Notnull\DailyNews\Models\NewsSourceFactory;
use Notnull\DailyNews\Models\User;

class NewsController extends BaseController
{
    /**
     * @param $username
     * @return bool
     */
    public function listAction($username)
    {
        // Get User
        /** @var User $user */
        $user = User::findFirst([['username' => $username]]);
        if (!$user) {
            return $this->fail(['message' => "User doesn't exist."]);
        }
        $news = $user->getNewsEntries();
        return $this->succeed($user->getNewsEntries());
    }
}