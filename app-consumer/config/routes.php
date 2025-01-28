<?php

declare(strict_types=1);

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \app_consumer\application\actions\HomeAction::class);

    $app->get('/consume', \app_consumer\application\actions\ConsumeMessageAction::class);

    return $app;
};
