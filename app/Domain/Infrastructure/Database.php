<?php

namespace Domain\Infrastructure;

use Silex\Provider\DoctrineServiceProvider;

class Database
{
    const DRIVER = 'pdo_pgsql';
    const HOST = '127.0.0.1';
    const PORT = 5432;
    const USERNAME = 'postgres';
    const PASSWORD = 'postgres';
    const DATABASE = 'postgres';

    private $conn;

    private $production = false;

    private static $instance;

    private function __construct($app)
    {
        $app->register(
            new DoctrineServiceProvider(),
            ['db.options' => $this->dbSettings()]
        );

        $this->conn = $app['db'];
    }

    private function dbSettings()
    {
        $this->production = !!getenv('VCAP_SERVICES');

        if ($this->production) {
            $data = json_decode(getenv('VCAP_SERVICES'));
            $services = current($data->elephantsql);
            $url = $services->credentials->uri;
        } else {
            $url = sprintf(
                'postgresql://%s:%s@%s:%d/%s',
                self::USERNAME,
                self::PASSWORD,
                self::HOST,
                self::PORT,
                self::DATABASE
            );
        }

        return ['url' => $url];
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
