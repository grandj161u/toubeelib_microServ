<?php

namespace api_praticien\infrastructure\adaptaters;

use api_praticien\core\services\auth\ServiceAuthInterface;
use api_praticien\core\dto\AuthDTO;
use GuzzleHttp\Client;

class AuthServiceAdapter implements ServiceAuthInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function decodeToken(string $token): AuthDTO
    {
        $response = $this->client->post('/tokens/validate', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return new AuthDTO(
            $data['payload']['sub'],
            $data['payload']['data']['user'],
            $data['payload']['data']['role'],
            $token,
            ''
        );
    }
}
