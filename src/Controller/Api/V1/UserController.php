<?php

namespace App\Controller\Api\V1;

use App\Repository\UserRepository;
use App\Form\UserType;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController

{

    /**
     * @Route("/api/user", name="app_user_page", methods={"GET"})
     */

    public function show(UserInterface $user, UserRepository $userRepository): JSonResponse
    {
        $user = $this->getUser();
        $userEntity = $userRepository->findOneBy(["email" => $user->getUserIdentifier()]);

        return $this->json($userEntity, Response::HTTP_OK,[],['groups' => 'user']);

    }

    /**
     * @Route("/api/user/new", name="app_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        
        $data=json_decode($request->getContent(), true);
        
        $form->submit($data);
        
        $user->setRoles(["ROLE_USER"]);

        /* Hash du mdp */
        $password = $user->getPassword();
                    $hashedPassword = $passwordHasher->hashPassword(
                        $user,
                        $password
                    );
                    $user->setPassword($hashedPassword);

        if (is_null($user->getAvatar())){
            $user->setAvatar('assets/ProfilLogged.png');
        }

        if($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

           return $this->json(['user' => $user], Response::HTTP_OK,[],[]);
        } 
        else {
            return $this->json(['Erreur dans la complétion du formulaire !']);
        }
    }


    /**
     * @Route("/api/user/{id}/edit", name="app_user_edit", methods={"PATCH"})
     */
    public function edit(Request $request, UserRepository $userRepository,User $user, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {   

        
        $data=json_decode($request->getContent(), true);
        $form = $this->createForm(UserType::class, $user);
        $previousPassword = $user->getPassword();

        $form->submit($data, false);

        if($form->isSubmitted() && $form->isValid()) {
            
            // on vérifie si on a soumis un mot de passe
            if(!empty($data['password'])) {
                // si c'est le cas (password != null)
                // hash du mot de passe 
                $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
            } else {
                // le mot de passe ne doit pas être changé, donc on remet le précédent
                $user->setPassword($previousPassword);
            }


            $userRepository->add($user, true);
           return $this->json(['Modifications effectuées'], Response::HTTP_OK,[],[]);
        } 
        else {
            
            return $this->json(['Erreur dans la complétion du formulaire !']);
        }
    }

}

