<?php

require_once __DIR__ . '/../vendor/autoload.php';

class RdvTest extends PHPUnit\Framework\TestCase {
    
    // test 1 : récupération données d'un rdv par son id
    public function testGetRdvById_valide() {
        $service = new toubeelib\core\services\rdv\ServiceRdv(new \toubeelib\infrastructure\repositories\ArrayRdvRepository());
        $rdv = $service->getRdvById('r2');
        $this->assertEquals('r2', $rdv->ID);
    }

    // test 2 : erreur s'éxecute si numéro de rdv inexistant
    public function testGetRdbById_Exception(): void {
        $service = new toubeelib\core\services\rdv\ServiceRdv(new \toubeelib\infrastructure\repositories\ArrayRdvRepository());
        $this->expectException(toubeelib\core\services\rdv\ServiceRdvNotFoundException::class);
        $service->getRdvById('r20');
    }
}