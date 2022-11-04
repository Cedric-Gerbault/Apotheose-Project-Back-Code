<?php

namespace App\Controller\Api\V1;

use App\Service\ApiService;
use App\Entity\Game;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class ChecklistController extends AbstractController 

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
     * @Route("/api/checklist/toggle", name="app_checklist_toggle", methods={"POST"})
     */
    public function toggleChecklist(Request $request, GameRepository $gameRepository, UserRepository $userRepository, ManagerRegistry $doctrine):JsonResponse
    {
        $entityManager = $doctrine->getManager();
        // créer un nouveau objet jeu
        $game = new Game();
        // récupérer le form du jeu dans la requête
        $dataGame = json_decode($request->getContent(), true);
        
        $gameRequest = $dataGame["game"];

        $game->setApiId($gameRequest['apiId']);
        $game->setName($gameRequest['name']);

        // on récupère le nom de notre nouvel objet jeu
        $gameName = $game->getName();
        
        // on récupère tous nos jeux en bdd
        $gameToCheck = $gameRepository->findByName($gameName);

        if (is_null($gameToCheck)){
                // on envoi notre jeu en bdd si il n'existe pas déja
                $entityManager->persist($game);
                $entityManager->flush();
            }

        // on récupère l'id de l'utilisateur dans la requête
        $userId = $dataGame["id"];
        // on récupère l'utilisateur correspondant à l'id en bdd
        $user = $userRepository->findById($userId);
        
        
        // on récupère la collection checklist que l'on convertit en array
        $userChecklist = $user->getChecklist()->toArray();
        // on récupère le jeu à comparer en bdd en fonction de son id
        $gameToCheck = $gameRepository->findByApiId($gameRequest['apiId']);
        
        
        // on cherche à savoir si le jeu est dans la checklist de l'utilisateur ou non
        if (in_array($gameToCheck, $userChecklist)){
            // on retire notre jeu de la checklist s'il y est déja
            $user->removeChecklist($gameToCheck);
            
            $entityManager->flush();
            
            return $this->json(['le jeu a été retiré de la liste'], Response::HTTP_OK,[],[]);
        }
        else{
            // on envoi notre jeu dans la checklist s'il n'y est pas
            $user->addChecklist($gameToCheck);
            
            $entityManager->flush();
            
            return $this->json(['le jeu a été ajouté dans la liste'], Response::HTTP_OK,[],[]);
        }

    } 


    /**
     * @Route("/api/checklist", name="app_checklist", methods={"GET"})
     */
    public function showChecklist(UserInterface $user, ApiService $apiService, UserRepository $userRepository):JsonResponse

    {   
        // on récupère l'utilisateur
        $user = $this->getUser();
        $userEntity = $userRepository->findOneBy(["email" => $user->getUserIdentifier()]);

        // on récupère la collection checklist que l'on convertit en array
        $userChecklist = $userEntity->getChecklist()->toArray();

        $stringRequest = 'fields id,name,genres.name,themes.name, age_ratings, slug, platforms.name, first_release_date, aggregated_rating, cover.image_id, screenshots.image_id,websites.url, involved_companies.company.name, summary; where id = (';


        foreach ($userChecklist as $index => $game) {

            if ($index == array_key_last($userChecklist)) {
                $stringRequest = $stringRequest.$game->GetApiId();
            }
            else{
                $stringRequest = $stringRequest.$game->GetApiId()." ,";
            }
        }

        
        $stringRequest=$stringRequest.");";
        
        $response = $this->client->request(
            'POST',
            'https://api.igdb.com/v4/games',
            [
            'headers' => [
                'Client-ID' => 'xg8ndhd4c1gk9bi9a88lw06kxcizi6',
                'Authorization' => 'Bearer ' . $apiService->getAccessTokenIgdb()
            ],
            'body' => $stringRequest
            ]
        );

        $content = $response->toArray();

        return $this->json(
            ['results' => $content],
            Response::HTTP_OK,
            [],
            []
        );
}
}
