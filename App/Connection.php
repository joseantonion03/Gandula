<?php

namespace App;

class Connection
{
    public static $host = 'localhost';
    public static $dbname = 'gandula';
    public static $user = 'root';
    public static $password = '';

    public static function getDb()
    {
        /*$envPath = realpath(dirname(__FILE__) . '/../env.ini');
        $env = parse_ini_file($envPath);
        self::$host = $env['host'];
        self::$user = $env['username'];
        self::$password = $env['password'];
        self::$dbname = $env['database'];*/

        try {
            $conn = new \PDO(
                "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8",
                self::$user,
                self::$password
            );
            return $conn;
        } catch (\PDOException $e) {
            die('<!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Erro de conexão</title>
                <link rel="stylesheet" href="/assets/CSS/erro.css">
            </head>
            <body>
                <div class="banco fx-ctr-st">
                    <div class="banco-container fx-center">
                        <h2>' . $e->getMessage() . '</h2>
                    </div>
                </div>
            </body>
            </html>');
        }
    }
}
