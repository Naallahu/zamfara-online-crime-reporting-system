<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class NotificationServer implements MessageComponentInterface {
    protected $clients;
    protected $subscriptions;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $conn->send(json_encode([
            'type' => 'connection',
            'message' => 'Connected successfully'
        ]));
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg);
        foreach ($this->clients as $client) {
            if ($client !== $from) {
                $client->send(json_encode([
                    'type' => 'update',
                    'data' => $data
                ]));
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}

// Create WebSocket server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new NotificationServer()
        )
    ),
    8080
);

echo "WebSocket server started on port 8080\n";
$server->run();