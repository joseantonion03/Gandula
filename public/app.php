<?php

session_start();

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Socket;

require_once(dirname(__DIR__, 1). '\vendor\autoload.php');

$id_user = $_SESSION['USER_ID'];

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Socket($id_user)
        )
    ),
    8080
);

$server->run();
