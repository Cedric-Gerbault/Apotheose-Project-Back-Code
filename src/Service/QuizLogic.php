<?php

namespace App\Service;

use App\Repository\QuestionRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class QuizLogic
{


    private $values;
    private $questionNumber;
    private $lastValueEntered;
    private $userPlatforms;
    private $userAge;
    private $currentQuestion;



    private $session;

    // construc of the quizLogic Service with the session setup
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    // reset of the field in the session in order to start a new quiz
    public function sessionReset()
    {
        $this->setValues(null);
        $this->setQuestionNumber(null);
        $this->session->set('userAge', null);
        $this->setUserPlatforms(null);
    }

    // dynamic creation a of the IGDB request
    public function createRequest()
    {
        unset($stringRequest);
        // we get the answerValues before passing them into the filter function

        $filteredValues = $this->filterRequest($this->session->get('values'));
        // body of the request with persistent fields
        $stringRequest = 'fields id,name,genres.name,themes.name, age_ratings, slug, platforms.name, first_release_date, aggregated_rating, cover.image_id, screenshots.image_id,websites.url, involved_companies.company.name, summary; limit 15; sort aggregated_rating desc; where category = 0 & aggregated_rating > 79 & aggregated_rating_count > 5 ';

        if (!empty($this->getUserPlatforms())) {
            $userPlatforms = ' & platforms = ' . $this->getUserPlatforms();
            $stringRequest = $stringRequest . $userPlatforms;
        } elseif (!empty($this->getUserAge())) {
            $pegis = $this->getAuthorizedPegis();
            $stringRequest = $stringRequest . $pegis;
        }
        // loop in the answerValues array to add the chosen fields into the request
        foreach ($filteredValues as $key => $value) {


            if (count($value) > 1) {
                if ($value[0] == 0) {
                    $stringRequest = $stringRequest . ' & (' . strval($key) . ' = ' . '(' . $value[0] . ',' . $value[1] . ') | ' . strval($key) . ' = ' . '(' . $value[1] . '))';
                } elseif ($value[1] == 0) {
                    $stringRequest = $stringRequest . ' & (' . strval($key) . ' = ' . '(' . $value[0] . ',' . $value[1] . ') | ' . strval($key) . ' = ' . '(' . $value[0] . '))';
                } elseif ($value[0] > 0 & $value[1] > 0) {
                    $stringRequest = $stringRequest . ' & ' . strval($key) . ' = ' . '(' . $value[0] . ',' . $value[1] . ')';
                }
            } elseif (count($value) == 1) {
                if ($value[0] > 1) {
                    $stringRequest = $stringRequest . ' & ' . strval($key) . ' = ' . '(' . $value[0] . ')';
                }
            }
        }

        $stringRequest = $stringRequest . ';';
        return $stringRequest;
    }

    // filter of the answerValues array in order to keep what matters
    public function filterRequest($values)
    {

        // loop between all types sub-array in order to sort them and keep only the two with the better score
        foreach ($values as $type => $tags) {

            arsort($tags);

            $positiveTags = array_filter($tags, function ($value) {
                return $value > 0;
            });

            $positiveTags = array_flip($positiveTags);

            $positiveTags = array_values($positiveTags);

            $filteredValues[$type] = array_slice($positiveTags, 0, 2, true);
        }

        return $filteredValues;
    }

    /**
     * Get the value of questionNumber
     */
    public function getQuestionNumber()
    {
        return $this->session->get('questionNumber', []);
    }

    /**
     * Set the value of questionNumber
     *
     * @return  self
     */
    public function setQuestionNumber($questionNumber)
    {
        $this->questionNumber = $questionNumber;
        $this->session->set('questionNumber', $questionNumber);

        return $this;
    }

    /**
     * Get the value of values
     */
    public function getValues()
    {
        return $this->session->get('values', []);
    }

    /**
     * Set the value of values
     *
     * @return  self
     */
    public function setValues($values)
    {
        $this->values = $values;
        $this->session->set('values', $values);

        return $this;
    }


    /**
     * Get the value of lastValueEntered
     */
    public function getLastValueEntered()
    {
        return $this->session->get('lastValueEntered', []);
    }

    /**
     * Set the value of lastValueEntered
     *
     * @return  self
     */
    public function setLastValueEntered($lastValueEntered)
    {
        $this->lastValueEntered = $lastValueEntered;
        $this->session->set('lastValueEntered', $lastValueEntered);

        return $this;
    }


    /**
     * Get the value of userPlatforms
     */
    public function getUserPlatforms()
    {
        return $this->session->get('userPlatforms', []);
    }


    /**
     * Set the value of userPlatforms
     *
     * @return  self
     */
    public function setUserPlatforms($userPlatforms)
    {
        $this->userPlatforms = $userPlatforms;
        $this->session->set('userPlatforms', $userPlatforms);


        return $this;
    }

    /**
     * Get the value of userAge
     */
    public function getUserAge()
    {
        return $this->session->get('userAge', []);
    }

    /**
     * Set the value of userAge
     *
     * @return  self
     */
    public function setUserAge($userBirthDate)
    {
        $now = new DateTime();

        $diff = date_diff($userBirthDate, $now);

        $userAge = $diff->y;

        $this->userAge = $userAge;
        $this->session->set('userAge', $userAge);


        return $this;
    }

    public function getAuthorizedPegis()
    {

        $userAge = $this->session->get('userAge', []);

        if ($userAge >= 18) {
            $pegis = " & age_ratings.rating = (1,2,3,4,5)";
        } elseif ($userAge >= 16) {
            $pegis = " & age_ratings.rating = (1,2,3,4)";
        } elseif ($userAge >= 12) {
            $pegis = " & age_ratings.rating = (1,2,3)";
        } elseif ($userAge >= 7) {
            $pegis = " & age_ratings.rating = (1,2)";
        } elseif ($userAge >= 3) {
            $pegis = " & age_ratings.rating = (1)";
        }
        return $pegis;
    }

    public function getRandomQuestion($index, QuestionRepository $quesRepo, $quizId)
    {

        if ($index == 1) {
            $allQuestion = $quesRepo->findBy(['priority' => $index]);

            $oneQuestion = array_rand($allQuestion);

            return $allQuestion[$oneQuestion];
        } else {


            $previousQuestion = $quesRepo->findOneByPriorityAndQuiz($index - 1, $quizId);

            $tags = $previousQuestion->getTagId();

            $randId = rand(0, count($tags) - 1);

            $tagId = $tags[$randId]->getId();

            $allQuestion = $quesRepo->findByPriorityAndTag($index, $tagId);


            if (empty($allQuestion)) {

                $allQuestion = $quesRepo->findBy(['priority' => $index]);
            }
            $oneQuestion = array_rand($allQuestion);

            return $allQuestion[$oneQuestion];
        }
    }




    /**
     * Get the value of currentQuestion
     */
    public function getCurrentQuestion()
    {
        return $this->session->get('currentQuestion', []);
    }

    /**
     * Set the value of currentQuestion
     *
     * @return  self
     */
    public function setCurrentQuestion($currentQuestion)
    {
        $this->currentQuestion = $currentQuestion;
        $this->session->set('currentQuestion', $currentQuestion);

        return $this;
    }
}
