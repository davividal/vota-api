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
        $app->register(new DoctrineServiceProvider(), $this->dbSettings());

        $this->conn = $app['db'];
    }

    private function dbSettings()
    {
        $this->production = !!getenv('OPENSHIFT_POSTGRESQL_DB_HOST');

        return [
            'db.options' => [
                'driver' => self::DRIVER,
                'host' => getenv('OPENSHIFT_POSTGRESQL_DB_HOST') ?: self::HOST,
                'port' => getenv('$OPENSHIFT_POSTGRESQL_DB_PORT') ?: self::PORT,
                'user' => $this->getUser(),
                'password' => $this->getPassword(),
                'dbname' => $this->getDB()
            ]
        ];
    }

    private function getUser()
    {
        if ($this->production) {
            return 'adminwyyxeve';
        }

        return self::USERNAME;
    }

    private function getPassword()
    {
        if ($this->production) {
            return 'IbAEj7C_Lsrd';
        }

        return self::PASSWORD;
    }

    private function getDB()
    {
        if ($this->production) {
            return 'vota';
        }

        return self::DATABASE;
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
