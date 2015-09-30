<?php

namespace Notnull\DailyNews\Controllers;

use Notnull\DailyNews\Models\User;

class UserController extends BaseController
{
    /**
     * @param $username
     * @return bool
     */
    public function addAction($username)
    {
        $user = User::findFirst([['username' => $username]]);

        if ($user) {
            return $this->succeed(['message' => 'User already exists.']);
        }

        $user = new User();
        $user->username = $username;

        if ($user->save()) {
            return $this->succeed();
        } else {
            return $this->fail(['message' => 'Error saving to Database.']);
        }
    }

}