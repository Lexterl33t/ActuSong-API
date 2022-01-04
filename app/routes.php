<?php

$app->group('/api', function(\Slim\Routing\RouteCollectorProxy $group) {
    $group->get('/', \App\Controllers\ArtistsRegisteredController::class.':index');
    $group->get('/artists_registred', \App\Controllers\ArtistsRegisteredController::class.':index');

    $group->group('/actuality', function (\Slim\Routing\RouteCollectorProxy $actuality_group){
        $actuality_group->group('/albums', function(\Slim\Routing\RouteCollectorProxy $album_group) {
            $album_group->get('', \App\Controllers\ApiController::class.':actuality_album');
            $album_group->get('/', \App\Controllers\ApiController::class.':actuality_album');
            $album_group->get('/{default_days}', \App\Controllers\ApiController::class.':actuality_album');
            $album_group->get('/{date_interval_start}/{date_interval_end}', \App\Controllers\ApiController::class.':actuality_album');
        });

        $actuality_group->group('/single', function (\Slim\Routing\RouteCollectorProxy $single_group) {
            $single_group->get('/', \App\Controllers\ApiController::class.':actuality_single');
        });
    });

});

