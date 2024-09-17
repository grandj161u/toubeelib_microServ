<?php

require_once __DIR__ . '/../vendor/autoload.php';

$service = new toubeelib\core\services\rdv\ServiceRdv(new \toubeelib\infrastructure\repositories\ArrayRdvRepository());

try {
    $rdv1 = $service->getRdvById('r1');
} catch (\toubeelib\core\services\rdv\ServiceRdvInvalidDataException $e) {
    echo 'exception dans la récupération d\'un rdv :' . PHP_EOL;
    echo $e->getMessage(). PHP_EOL;
}