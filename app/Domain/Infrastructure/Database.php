<?php

namespace Domain\Infrastructure;

use Silex\Provider\DoctrineServiceProvider;

class Database
{
    const DRIVER = 'pdo_pgsql';
    const HOST = '127.0.0.1';
    const USERNAME = 'postgres';
    const PASSWORD = 'postgres';
    const DATABASE = 'postgres';

    private $conn;

    private static $instance;

    private function __construct($app)
    {
        $app->register(
            new DoctrineServiceProvider(),
            [
                'db.options' => [
                    'driver' => self::DRIVER,
                    'host' => self::HOST,
                    'user' => self::USERNAME,
                    'password' => self::PASSWORD,
                    'dbname' => self::DATABASE
                ]
            ]
        );

        $this->conn = $app['db'];
    }

    public static function getInstance($app)
    {
        if (null === static::$instance) {
            static::$instance = new Database($app);
        }

        return static::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
