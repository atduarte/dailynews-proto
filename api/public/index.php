<?php

date_default_timezone_set('Europe/Lisbon');

if (isset($_SERVER["APP_ENV"]) && $_SERVER["APP_ENV"] == 'dev') {
    ini_set("display_errors", 1);
    error_reporting(E_ALL);
}

try {

    //Register an autoloader
    $loader = new \Phalcon\Loader();

    // Twig
    require_once __DIR__ . '/../vendor/autoload.php';

    $loader->registerNamespaces([
        'Notnull\DailyNews\Controllers' => __DIR__ . '/../app/controllers/',
        'Notnull\DailyNews\Models' => __DIR__ . '/../app/models/',
        'Notnull\DailyNews\Tasks' => __DIR__ . '/../app/tasks/',
    ]);

    $loader->register();

    //Create a DI
    if (PHP_SAPI != 'cli') {
        $di = new \Phalcon\DI\FactoryDefault();
    } else {
        $di = new \Phalcon\DI\FactoryDefault\CLI();
    }

    //Setting MongoDB

    $di->set('mongo', function () {
        $mongo = new MongoClient();
        return $mongo->selectDb("ppro");
    }, true);


    //Registering the collectionManager service
    $di->set('collectionManager', function () {
        return new Phalcon\Mvc\Collection\Manager();
    }, true);

    //Setting Router
    if (PHP_SAPI == 'cli') {
        $di->set('dispatcher', function () {
            $dispatcher = new Phalcon\CLI\Dispatcher();
            $dispatcher->setDefaultNamespace('Notnull\DailyNews\Tasks');
            return $dispatcher;
        });
    }

    //Setting Router
    if (PHP_SAPI != 'cli') {
        $di->set('router', function () {
            return require __DIR__ . '/../app/config/routes.php';
        });
    }

    //Setting URL Helper
    $di->set('url', function () {
        $url = new Phalcon\Mvc\Url();
        $base = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '');
        $base .= "://";
        $base .= isset($_SERVER['HTTP_HOST'])  ? $_SERVER['HTTP_HOST'] : 'revis.pt';
        $base .= '/';
        $url->setBaseUri($base);
        return $url;
    });

    //Setting up the view component
    $di->set('view', function () {
        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir(__DIR__ . '/../app/views/');
        $view->registerEngines(array(
            ".volt" => function ($view, $di) {
                $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
                $volt->setOptions([
                    'compileAlways' => (isset($_SERVER["APP_ENV"]) && $_SERVER["APP_ENV"] == 'dev'),
                    'compiledPath' => function ($templatePath) {
                        $templatePath = str_replace(__DIR__ . '/../app/views', __DIR__ . '/../app/views/cache', $templatePath);
                        $dirName = dirname($templatePath);
                        if (!is_dir($dirName)) {
                            mkdir($dirName, 0755, true);
                        }
                        return $templatePath;
                    }
                ]);
                return $volt;
            }
        ));
        return $view;
    });

    if (PHP_SAPI != 'cli') {
        // URL Creation
        $url = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);

        //Handle the request
        $application = new \Phalcon\Mvc\Application();
        $application->setDI($di);
        echo $application->handle($url)->getContent();
    } else {
        $cli = new Phalcon\CLI\Console();
        $cli->setDI($di);
        $cli->handle(array(
            'task' => (isset($argv[1]) ? $argv[1] : null),
            'action' => (isset($argv[2]) ? $argv[2] : 'index'),
            'params' => count($argv) > 3 ? array_slice($argv, 3) : []
        ));
    }

} catch (\Phalcon\Exception $e) {
    echo "PhalconException: ", $e->getMessage();
}
