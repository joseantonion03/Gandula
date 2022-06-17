<?php
session_start();

date_default_timezone_set('America/Sao_Paulo'); //Fuso horario de brasilia

/*
if ($_SERVER['REQUEST_SCHEME'] == 'http') {
    $url = "https://". $_SERVER['SERVER_NAME']."/";
    header("Location: $url");
}*/

ini_set('error_reporting', 'E_STRICT');
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);


require_once "../vendor/autoload.php";

$route = new \App\Route;
/*
if (!$_SESSION['pagina']) {
    header('Location: /404');
}
*/