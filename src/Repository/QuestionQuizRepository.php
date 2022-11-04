<?php

namespace App\Repository;


use App\Entity\QuestionQuiz;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Proxies\__CG__\App\Entity\Question;

/**
 * @extends ServiceEntityRepository<QuestionQuiz>
 *
 * @method QuestionQuiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionQuiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionQuiz[]    findAll()
 * @method QuestionQuiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionQuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionQuiz::class);
    }

    public function add(QuestionQuiz $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(QuestionQuiz $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Question[] Returns an array of Question objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

   public function findOneByQuestionAndQuiz($quiz, $question): ?QuestionQuiz
   {
       return $this->createQueryBuilder('qq')
           ->innerJoin(Question::class, 'que', 'qq.id = que.id')
           ->innerJoin(Quiz::class, 'qui', 'qq.id = qui.id')
           ->andWhere('que.id = :val')
           ->andWhere('qui.id = :val2')
           ->setParameter('val', $question)
           ->setParameter('val2', $quiz)
           ->setMaxResults(1)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }
}
