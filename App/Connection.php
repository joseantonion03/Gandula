<?php

namespace App;

class Connection
{
    public static $host = "localhost";
    public static $dbname = "gandula";
    public static $user = "root";
    public static $password = "";

    public static function getDb()
    {
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
                <title>Document</title>
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
