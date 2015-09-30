<?php

namespace Notnull\DailyNews\Tasks;

use Phalcon\CLI\Task;

class BaseTask extends Task
{
    public function log($message)
    {
        echo $message . PHP_EOL;
    }
}