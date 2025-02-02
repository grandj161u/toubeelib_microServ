<?php

declare(strict_types=1);

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \app_consumer\application\actions\HomeAction::class);

    return $app;
};
