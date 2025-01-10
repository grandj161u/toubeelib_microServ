<?php

// namespace gateway_tblb\application\actions;

// use Psr\Http\Message\ResponseInterface;
// use Psr\Http\Message\ServerRequestInterface;
// use GuzzleHttp\Exception\ConnectException;
// use GuzzleHttp\Exception\ClientException;
// use GuzzleHttp\Exception\ServerException;

// class GatewayPraticienByIdAction extends AbstractGatewayAction
// {

//     public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
//     {
//         if (isset($args['idPraticien'])) {
//             $idPraticien = $args['idPraticien'];
//         } else {
//             $idPraticien = null;
//         }

//         try {
//             $response = $this->remote->request('GET', 'praticiens/' . $idPraticien);
//         } catch (ConnectException | ServerException $e) {
//             throw new InternalServiceErrorException(" … ");
//         } catch (ClientException $e) {
//             match ($e->getCode()) {
//                 404 => throw new NotFoundServiceException(" ... "),
//                 400 => throw new BadRequestException(" "
//             };
//         }

//         return $response;
//     }
// }
