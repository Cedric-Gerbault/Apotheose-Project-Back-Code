<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass=App\Repository\QuestionRepository::class)
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @groups({"question"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priority;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $UpdatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="questions")
     */
    private $tagId;

    /**
     * @ORM\ManyToMany(targetEntity=Choice::class, inversedBy="questions")
     */
    private $choiceId;

    /**
     * @ORM\OneToMany(targetEntity="QuestionQuiz", mappedBy="question")
     */
    private $quizId;

    public function __construct()
    {
        $this->tagId = new ArrayCollection();
        $this->choiceId = new ArrayCollection();
        $this->quizId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->UpdatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $UpdatedAt): self
    {
        $this->UpdatedAt = $UpdatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTagId(): Collection
    {
        return $this->tagId;
    }

    public function addTagId(Tag $tagId): self
    {
        if (!$this->tagId->contains($tagId)) {
            $this->tagId[] = $tagId;
        }

        return $this;
    }

    public function removeTagId(Tag $tagId): self
    {
        $this->tagId->removeElement($tagId);

        return $this;
    }

    /**
     * @return Collection<int, Choice>
     */
    public function getChoiceId(): Collection
    {
        return $this->choiceId;
    }

    public function addChoiceId(Choice $choiceId): self
    {
        if (!$this->choiceId->contains($choiceId)) {
            $this->choiceId[] = $choiceId;
        }

        return $this;
    }

    public function removeChoiceId(Choice $choiceId): self
    {
        $this->choiceId->removeElement($choiceId);

        return $this;
    }

    /**
     * @return Collection<int, QuestionQuiz>
     */
    public function getQuizId(): Collection
    {
        return $this->quizId;
    }

    public function addQuizId(Quiz $quizId): self
    {
        if (!$this->quizId->contains($quizId)) {
            $this->quizId[] = $quizId;
        }

        return $this;
    }

    public function removeQuizId(Quiz $quizId): self
    {
        $this->quizId->removeElement($quizId);

        return $this;
    }

}
