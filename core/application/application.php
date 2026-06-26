<?php

namespace Core\Application;

use Core\Container\Container;

class Application {
    protected static ?Application $instance = null;

    protected Container $container;

    public function __construct() {
        self::$instance = $this;

        $this->container = new Container();
    }

    public static function getInstance(): Application {
        return self::$instance;
    }

    public function container(): Container {
        return $this->container;
    }



//     public function run()
// {
//     require base_path('routes/web.php');

//     var_dump(
//         \Core\Router\Router::dispatch(
//             'GET',
//             '/db-test'
//         )
//     );

//     exit;

//     (new Kernel())->handle();
// }


    public function run() {
        require base_path('routes/web.php');

        (new Kernel())->handle();
    }
}