<?php
/**
 * Created by solly [07.03.18 3:04]
 */

require __DIR__ . '/vendor/autoload.php';

use Throwable;
use Amp\Loop;
use Amp\Socket\ServerSocket;
use function Amp\asyncCoroutine;

Loop::run(function () {
    $dataHandler = asyncCoroutine(function ($resource) {
        $data = stream_get_contents($resource);
        try {
            print_r(json_decode($data, true));
        } catch (Throwable $e) {
            sprintf('Fail decode data %s%s%s', PHP_EOLprint_r($data, true), PHP_EOL);
        }
    });

    $clientHandler = asyncCoroutine(function (ServerSocket $socket) use ($dataHandler) {
        list($ip, $port) = explode(':', $socket->getRemoteAddress());
        echo sprintf('Accepted connection from %s:%d.%s', $ip, $port, PHP_EOL);
        $resource = $socket->getResource();
        if ($resource) {
            $data = stream_get_contents($resource);
            print_r($data);
        }
        $dataHandler($socket->getResource());
        yield $socket->end('some end message');
    });
    
    $server = Amp\Socket\listen('127.0.0.1:5000');
    echo sprintf('Listening for new connections on %s ...%s', $server->getAddress(), PHP_EOL);
    while ($socket = yield $server->accept()) {
        $clientHandler($socket);
    }
});
