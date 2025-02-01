<?php

use DI\ContainerBuilder;

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/settings.php');
$builder->addDefinitions(__DIR__ . '/dependencies.php');

$c = $builder->build();

return $c;
