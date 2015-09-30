<?php

namespace Notnull\DailyNews\Controllers;

use Notnull\DailyNews\Models\NewsSource;
use Notnull\DailyNews\Models\NewsSourceFactory;
use Notnull\DailyNews\Models\User;

class SourceController extends BaseController
{
    /**
     * @param $username
     * @return bool
     */
    public function addAction($username)
    {
        // Check Source Param
        $feedUrl = $this->request->getPost('source', 'string', null);
        if (empty($feedUrl)) {
            return $this->fail(['message' => 'Source parameter missing.']);
        }

        // Get User
        /** @var User $user */
        $user = User::findFirst([['username' => $username]]);
        if (!$user) {
            return $this->fail(['message' => "User doesn't exist."]);
        }

        // Check if Source already on the DB
        /** @var NewsSource $source */
        $source = NewsSource::findFirst([['feed' => $feedUrl]]);

        // If doesn't exist, create
        if (!$source) {
            list($source, $message) = (new NewsSourceFactory())->create($feedUrl);

            // If creation fails, fail
            if (!$source) {
                return $this->fail(['message' => $message]);
            }
        }

        $user->addNewsSource($source->getId());

        if ($user->save()) {
            return $this->succeed();
        } else {
            return $this->fail(['message' => 'Error saving to Database.']);
        }
    }

    /**
     * @param $username
     * @return bool
     */
    public function deleteAction($username, $id)
    {

        // Get User
        /** @var User $user */
        $user = User::findFirst([['username' => $username]]);
        if (!$user) {
            return $this->fail(['message' => "User doesn't exist."]);
        }


        // Check if Source already on the DB
        /** @var NewsSource $source */
        $source = NewsSource::findById($id);
        if (!$source) {
            return $this->fail(['message' => "Source doesn't exist."]);
        }

        $user->removeNewsSource($source->getId());

        if ($user->save()) {
            return $this->succeed();
        } else {
            return $this->fail(['message' => 'Error saving to Database.']);
        }
    }

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

        return $this->succeed($user->getNewsSourcesInfo());
    }

}