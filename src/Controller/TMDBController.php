<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TMDBController extends AbstractController
{
    private $client;
    private $TMDB_TOKEN;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params)
    {
        $this->client = $client;
        $this->TMDB_TOKEN = $params->get('TMDB_TOKEN');    
    }

    public function __invoke(Request $request): JsonResponse
    {
        $movieName = $request->query->get('name');

        if (!$movieName) {
            return new JsonResponse(['error' => 'Movie name is required'], 400);
        }

        $url = sprintf('https://api.themoviedb.org/3/search/movie?query=%s&include_adult=false&language=en-US&page=1', urlencode($movieName));

        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->TMDB_TOKEN,
                'accept' => 'application/ld+json',
            ],
        ]);

        $data = $response->toArray();
        $poster_full_url = ($data['total_results'] > 0)?"https://image.tmdb.org/t/p/original".$data['results'][0]['poster_path']:null; 
        $poster_path = $data['results'][0]['poster_path'] ?? null;
        return new JsonResponse([
            'poster_path' => $poster_path,
            'poster_full_url' => $poster_full_url
        ]);
    }

}
