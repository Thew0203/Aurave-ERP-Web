<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static array $config;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$config = require dirname(__DIR__) . '/config/database.php';
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::$config['host'],
                self::$config['name'],
                self::$config['charset']
            );
            try {
                self::$instance = new PDO($dsn, self::$config['user'], self::$config['password'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                if ((require dirname(__DIR__) . '/config/app.php')['debug']) {
                    throw $e;
                }
                die('Database connection failed.');
            }
        }
        return self::$instance;
    }

    public static function config(): array
    {
        if (empty(self::$config)) {
            self::$config = require dirname(__DIR__) . '/config/database.php';
        }
        return self::$config;
    }
}
