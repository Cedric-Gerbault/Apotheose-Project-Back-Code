<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;





/**
 * @ORM\Entity
 * @ORM\Table(name="question_quiz")
 */
class QuestionQuiz
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\ManyToOne(targetEntity="Question",inversedBy="quizId")
     * @ORM\JoinColumn(nullable=false)
     */
    private $question;

    /**
     * @ORM\ManyToOne(targetEntity="Quiz",inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quiz;


    /**
     * @ORM\Column(type="integer")
     */
    private $answerValue;




    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of question
     */ 
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set the value of question
     *
     * @return  self
     */ 
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get the value of quiz
     */ 
    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * Set the value of quiz
     *
     * @return  self
     */ 
    public function setQuiz($quiz)
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * Get the value of answerValue
     */ 
    public function getAnswerValue()
    {
        return $this->answerValue;
    }

    /**
     * Set the value of answerValue
     *
     * @return  self
     */ 
    public function setAnswerValue($answerValue)
    {
        $this->answerValue = $answerValue;

        return $this;
    }
}