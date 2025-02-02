<?php

return  [

    'displayErrorDetails' => true,
    'logs.dir' => __DIR__ . '/../var/logs',

    'praditicen.db.config' => 'praticien.db.ini',
    'rdv.db.config' => 'rdv.db.ini',
    'patient.db.config' => 'patient.db.ini',
    'auth.db.config' => 'auth.db.ini',
    // 'api.praticiens' => 'http://localhost:2080',
    'praticien.api' => 'http://api.praticien/',

    'rdv.event.exchange' => 'rdv.exchange',
    'rdv.type.exchange' => 'direct',
    'rdv.queue' => 'rdv.queue',
    'rdv.routing.key' => 'rdv.key',

    'rabbitmq.host' => 'rabbitmq',
    'rabbitmq.port' => 5672,
    'rabbitmq.user' => 'admin',
    'rabbitmq.password' => 'root',
];
