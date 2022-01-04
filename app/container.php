<?php

$container->set('cache', function () {
    $cache = new \App\Controllers\CacheController(1, '../../tmp/');
    return $cache;
});