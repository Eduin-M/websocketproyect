<?php
error_reporting(E_ALL & ~E_DEPRECATED);
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class NotificacionesServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "✅ Nueva conexión: ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        foreach ($this->clients as $client) {
            if ($client !== $from) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "❌ Conexión cerrada: ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "⚠️ Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$port = getenv("PORT") ?: 3000;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new NotificacionesServer()
        )
    ),
    $port
);

echo "🚀 Servidor WebSocket escuchando en ws://0.0.0.0:$port\n";
$server->run();
