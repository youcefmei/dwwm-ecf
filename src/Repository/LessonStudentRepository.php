<?php

namespace App\Repository;

use App\Entity\LessonStudent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LessonStudent|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonStudent|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonStudent[]    findAll()
 * @method LessonStudent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonStudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonStudent::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(LessonStudent $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(LessonStudent $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    public function findLessonsByStudent($student)
    {
        $result = $this->createQueryBuilder('ls')
        ->andWhere('ls.student = :val')
        ->setParameter('val', $student)
        ->getQuery()
        ->getResult()
    ;
    
    // dd($result);
    $ret = [];
        foreach ($result as $value) {
            if($value){
                $ret[] =$value->getLesson()->getId();
            }
        }
        return $ret;
    }

    // /**
    //  * @return LessonStudent[] Returns an array of LessonStudent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LessonStudent
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
