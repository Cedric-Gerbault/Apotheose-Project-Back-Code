<?php

namespace App\Controller\Api\V1;

use App\Entity\QuestionQuiz;
use App\Entity\Quiz;
use App\Repository\QuestionQuizRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Repository\UserRepository;
use App\Service\QuizLogic;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController
{

    // We create/import a quiz.

    /**
     * @Route("/api/quiz", name="app_quiz", methods={"GET"})
     */
    public function create(ManagerRegistry $doctrine, QuizLogic $quizLogic, UserRepository $userRepository): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        //in mvp we go get the id of a fixed quiz
        $quiz = new Quiz();
        
        
        // we set the the count to the first question
        $quizLogic->setQuestionNumber(1);
        $user= $this->getUser();

    if (!empty($user)) {
         
        $userEntity = $userRepository->findOneBy(["email" => $user->getUserIdentifier()]);
        $quiz->setUserId($userEntity);
        $quizLogic->setUserPlatforms($userEntity->getPlatform());
        $quizLogic->setUserAge($userEntity->getBirthdate());
    }
    $entityManager->persist($quiz);
    $entityManager->flush();
            

        // we return the quizz id for future referal in json
        return $this->json(
            ['quiz' => $quiz],
            Response::HTTP_OK,
            [],
            ['groups' => 'quiz']
        );
    }


    // We get the questions (related to the quiz) one by one.

    /**
     * @Route("/api/quiz/{id}/ask", name="app_ask", methods={"GET"})
     */
    public function ask( $id, QuizLogic $quizLogic,QuestionRepository $quesRepo): JsonResponse
    {

        $index = $quizLogic->getQuestionNumber();
                

        $question = $quizLogic->getRandomQuestion($index,$quesRepo,$id);
        $quizLogic->setCurrentQuestion($question->getId());


            $choices = $question->getChoiceId();
            $questionContent = $question->getContent();
                
            
            
            // for each question we get the correlated choices of answer to return

            foreach ($choices as $choice) {
                $test[$choice->getName()] = $choice->getValue();
            }
        
        return $this->json(
            [
                'question' => $questionContent,
                'choices' => $test,
                'questionNumber' => $quizLogic->getQuestionNumber()
            ],
            Response::HTTP_OK,
            [],
            ['groups' => 'question']
        );
    }

    // We get the answer back from Front and add the values to the quizlogic session.

    /**
     * @Route("/api/quiz/{id}/answer/{answerValue}", name="app_answer", methods={"GET"})
     */
    public function answer(ManagerRegistry $doctrine,QuizRepository $quizRepository, $id, $answerValue, QuizLogic $quizLogic,QuestionRepository $quesRepo): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        // we get the question again in order to retrieve the correlated tags
        $index = $quizLogic->getQuestionNumber();
        $currentQuestion = $quesRepo->find($quizLogic->getCurrentQuestion());

        $quiz = $quizRepository->find($id);

        $questionQuiz = new QuestionQuiz();
        $questionQuiz->setQuiz($quiz);
        $questionQuiz->setQuestion($currentQuestion);
        $questionQuiz->setAnswerValue($answerValue);
        $entityManager->persist($questionQuiz);
        $entityManager->flush();
    
                
        $tags = $currentQuestion->getTagId();
        
        $quizLogic->setQuestionNumber(intval($index) + 1);


        $values = $quizLogic->getValues();
        $quizLogic->setLastValueEntered($values);



        foreach ($tags as $tag) {

            // we get the type name for each tag
            $type = $tag->getTypeId()->getName();

            // we split the logic between fixed choices and generic ones
            if ($type !== "platforms" || $type !== "age_ratings") {
                // we check if the answerValue exist already in the array
                if (isset($values[$type][$tag->getApiId()])) {
                    // we add the answer value to the existing one
                    $values[$type][intval($tag->getApiId())] += $answerValue;
                } else {
                    // we create the new entry in the array and set the answer value to it
                    $values[$type][intval($tag->getApiId())] = $answerValue;
                }
            } elseif ($type == "platforms") {
                $values[$type] = $answerValue;
            } elseif ($type == "age_ratings") {
                $values[$type] = $answerValue;
            }

            $quizLogic->setValues($values);
        }





      

        return $this->json(Response::HTTP_OK);
    }


    /**
     * @Route("/api/quiz/{id}/askBack", name="app_askBack", methods={"GET"})
     */
    public function askBack(QuestionQuizRepository $questionQuizRepository, $id, QuizLogic $quizLogic,QuestionRepository $quesRepo): JsonResponse
    {

        $index = $quizLogic->getQuestionNumber();
        $question = $quesRepo->findOneByPriorityAndQuiz($index-1, $id);
        
        $qQ = $questionQuizRepository->findOneByQuestionAndQuiz($id,$question->getId());
        $tags = $question->getTagId();

        $choices = $question->getChoiceId();
        $questionContent = $question->getContent();

        $values = $quizLogic->getValues();
                
        foreach($tags as $tag){
            $type = $tag->getTypeId()->getName();

            $values[$type][intval($tag->getApiId())]-= $qQ->getAnswerValue();
            $quizLogic->setValues($values);

        }

            // for each question we get the correlated choices of answer to return

            foreach ($choices as $choice) {
                $test[$choice->getName()] = $choice->getValue();
            }
        $quizLogic->setQuestionNumber(intval($index - 1));
        return $this->json(
            [
                'question' => $questionContent,
                'choices' => $test,
                'questionNumber' => $quizLogic->getQuestionNumber()
            ],
            Response::HTTP_OK,
            [],
            ['groups' => 'question']
        );
    }


    /**
     * @Route("/api/quiz/restart", name="app_restart", methods={"GET"})
     */
    public function restart(QuizLogic $quizLogic): JsonResponse
    {

        $quizLogic->sessionReset();
        $quizLogic->setQuestionNumber(1);


        return $this->json(Response::HTTP_OK);
    }

}
