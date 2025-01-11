<?php

namespace api_rdv\infrastructure\adaptaters;

use api_rdv\core\services\praticien\PraticienServiceInterface;
use api_rdv\core\dto\PraticienDTO;
use GuzzleHttp\Client;
use api_rdv\core\dto\SpecialiteDTO;

class PraticienServiceAdapter implements PraticienServiceInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getPraticienById(string $id): PraticienDTO
    {
        $response = $this->client->get("/praticiens/{$id}");
        $data = json_decode($response->getBody()->getContents(), true);
        return new PraticienDTO($data);
    }

    public function getAllPraticiens(): array
    {
        $response = $this->client->get("/praticiens");
        $data = json_decode($response->getBody()->getContents(), true);
        return array_map(fn($item) => new PraticienDTO($item), $data);
    }

    public function getSpecialiteById(string $id): SpecialiteDTO
    {
        $response = $this->client->get("/specialites/{$id}");
        $data = json_decode($response->getBody()->getContents(), true);
        $specialite = $data['specialite'];
        return new SpecialiteDTO($specialite['ID'], $specialite['label'], $specialite['description']);
    }
}
