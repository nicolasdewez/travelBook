<?php

use Symfony\Component\Config\Resource\ClassExistenceResource;
use Symfony\Component\Console\Application;

$handlers = [
    'main' => [
        'type' => 'rotating_file',
        'path' => '%kernel.logs_dir%/app.%kernel.environment%.log',
        'level' => constant('Monolog\Logger::'.strtoupper(getenv('APP_LOGGING_LEVEL'))),
        'max_files' => 10,
    ],
];

$container->addResource(new ClassExistenceResource(Application::class));
if (class_exists(Application::class)) {
    $handlers['console'] = [
        'type' => 'console',
        'process_psr_3_messages' => false,
        'channels' => ['!event', '!doctrine'],
    ];
}

$container->loadFromExtension('monolog', [
    'handlers' => $handlers,
]);
