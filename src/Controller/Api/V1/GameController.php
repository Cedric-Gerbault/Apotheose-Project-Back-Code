<?php

namespace App\Controller\Api\V1;

use App\Service\ApiService;
use App\Service\QuizLogic;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GameController extends AbstractController
{
  private $client;


  // construct at any call of the controller to get the IGDB token for future requests
  public function __construct(HttpClientInterface $client, ApiService $apiService)
  {
    $this->client = $client;

    $response = $this->client->request(
        'POST',
        'https://id.twitch.tv/oauth2/token?client_id=xg8ndhd4c1gk9bi9a88lw06kxcizi6&client_secret=wgeksfty9kw4m74o2jznmgny5aof27&grant_type=client_credentials'
    );
    $apiService->setAccessTokenIgdb($response->toArray()["access_token"]);
  }

  /**
   * @Route("/api/game/result", name="app_result")
   */
  public function fetchResult(QuizLogic $quizLogic, ApiService $apiService): JsonResponse
  {

    $request = $quizLogic->createRequest();


    
    $response = $this->client->request(
        'POST',
        'https://api.igdb.com/v4/games',
        [
          'headers' => [
            'Client-ID' => 'xg8ndhd4c1gk9bi9a88lw06kxcizi6',
            'Authorization' => 'Bearer ' . $apiService->getAccessTokenIgdb()
          ],
          'body' => $request
        ]
    );

    $content = $response->toArray();


    $quizLogic->sessionReset();
    return $this->json(
        ['results' => $content],
        Response::HTTP_OK,
        [],
        []
    );
  }

  /**
   * @Route("/api/game/{id}", name="app_game")
   */
  public function show($id, ApiService $apiService): JsonResponse
  {

    $response = $this->client->request(
      'POST',
      'https://api.igdb.com/v4/games',
      [
        'headers' => [
          'Client-ID' => 'xg8ndhd4c1gk9bi9a88lw06kxcizi6',
          'Authorization' => 'Bearer ' . $apiService->getAccessTokenIgdb()
        ],
        'body' => 'fields *; where id =' . $id . ';'
      ]
    );

    $content = $response->toArray();




    return $this->json(
        ['game' => $content],
        Response::HTTP_OK,
        [],
        []
    );
  }
}
