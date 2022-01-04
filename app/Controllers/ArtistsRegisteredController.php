<?php

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ArtistsRegisteredController
{
    public function __construct()
    {
    }

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        $file_content = file_get_contents(dirname(__FILE__).'/../../artists.json');

        $response->getBody()->write($file_content);
        return $response;
    }
}