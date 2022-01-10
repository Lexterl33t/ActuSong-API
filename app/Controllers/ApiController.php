<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiController
{
    private $app_settings = [
        'client_id' => "7cfd30d260924a1a8bbd79fd3b644d69",
        'client_secret' => 'c04e5b7e46e24cbf81b72224aab9a4fd'
    ];

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get_access_token($force = false)
    {
        if (!file_exists(dirname(__FILE__) . '/../../token_access.json'))
            return false;

        if (!$force) {
            $get_content = json_decode(file_get_contents(dirname(__FILE__) . '/../../token_access.json'));

            if (!empty($get_content->access_token)) {
                return $get_content->access_token;
            }
        }


        $server_output = RequestController::post("https://accounts.spotify.com/api/token", [
            'Authorization:  Basic ' . base64_encode($this->app_settings['client_id'] . ':' . $this->app_settings['client_secret'])
        ], "grant_type=client_credentials");


        file_put_contents(dirname(__FILE__) . '/../../token_access.json', "{\"access_token\": \"" . json_decode($server_output)->access_token . "\"}");
        return json_decode($server_output)->access_token;
    }

    public function actuality_album(RequestInterface $request, ResponseInterface $response, $args)
    {
        $get_all_artists_in_array = json_decode(json_encode(json_decode(file_get_contents(dirname(__FILE__) . '/../../artists.json'))), true);

        $access_token = $this->get_access_token();

        $recent_album = [];

        if ((!isset($args['date_interval_start']) && empty($args['date_interval_start'])) && (!isset($args['date_interval_end']) && empty($args['date_interval_end'])) && (!isset($args['default_days']) && empty($args['default_days']))) {
            $date_start = strtotime("now");
            $date_end = strtotime("-30 days");
            $cacheName = "recent_artists_album";
        } elseif (isset($args['default_days']) && !empty($args['default_days'])) {
            $date_start = strtotime("now");
            $date_end = strtotime("-" . $args['default_days'] . " days");
            $cacheName = "recent_artists_album_" . $args['default_days'];
        } else {
            $date_start = strtotime($args['date_interval_start']);
            $date_end = strtotime($args['date_interval_end']);
            $cacheName = "recent_artist_albums_" . $date_end . '_' . $date_start;
        }

        if (!$this->container->get('cache')->read($cacheName)) {
            foreach ($get_all_artists_in_array['artists'] as $artist) {

                $req = RequestController::get('https://api.spotify.com/v1/artists/' . $artist['id'] . '/albums', [
                    'Authorization: Bearer ' . $access_token,
                    'Accept: application/json',
                    'Content-Type: application/json'
                ], [
                    'limit' => "1"
                ]);

                $req_array = json_decode($req, true);
                if (isset($req_array['error']) && $req_array['error']['message'] === "The access token expired") {
                    $this->get_access_token(true);
                    $response->getBody()->write("Refresh page");
                    return $response;
                }
                $released_date = strtotime($req_array['items'][0]['release_date']);
                if ($date_start < $date_end) {
                    if ($released_date >= $date_start && $released_date <= $date_end) {
                        $data = [
                            'name' => $req_array['items'][0]['artists'][0]['name'],
                            'recent_album' => $req_array['items'][0]['name'],
                            'release_date' => $req_array['items'][0]['release_date']
                        ];
                        array_push($recent_album, $data);
                    }
                } else {
                    if ($released_date <= $date_start && $released_date >= $date_end) {
                        $data = [
                            'name' => $req_array['items'][0]['artists'][0]['name'],
                            'recent_album' => $req_array['items'][0]['name'],
                            'release_date' => $req_array['items'][0]['release_date']
                        ];

                        array_push($recent_album, $data);
                    }
                }

            }

            $this->container->get('cache')->write($cacheName, json_encode($recent_album));
        } else {
            $recent_album = $this->container->get('cache')->read($cacheName);
        }


        $response->getBody()->write(json_encode($recent_album));
        return $response;
    }

    public function set_artist_by_name(RequestInterface $request, ResponseInterface $response, $args)
    {

    }

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        var_dump($this->get_access_token());
        $response->getBody()->write("ok");
        return $response;
    }
}