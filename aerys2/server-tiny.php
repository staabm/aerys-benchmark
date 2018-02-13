<?php

use Aerys\Host;
use function Aerys\initServer;
use Psr\Log\NullLogger;

require_once __DIR__ . '/vendor/autoload.php';

$options = [
    // help avoid connection errors during benchmark
    'connectionsPerIP' => 100,
    // to emulate NodeJS behavior
    'connectionTimeout' => 10000,
    'maxRequestsPerConnection' => PHP_INT_MAX,
];

$hosts = [
    (new Host())
        ->name('localhost')
        ->expose('0.0.0.0', 8080)
        ->use(function(Aerys\Request $req, Aerys\Response $resp) {
            if ($req->getUri() === '/') {
                $data = 'Hello world!';
                $status = 200;
            } else {
                $data = 'Not Found';
                $status = 400;
            }
            $resp->addHeader('Content-Type', 'text/plain; charset=utf-8');
            $resp->addHeader('X-Powered-By', 'AerysServer');
            $resp->setStatus($status);
            $resp->end($data);
        }),
];
$server = initServer(new NullLogger(), $hosts, $options);

\Amp\Loop::run(function () use ($server) {
    yield $server->start();
});
