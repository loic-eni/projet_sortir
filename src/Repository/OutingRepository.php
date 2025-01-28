<?php

namespace App\Repository;

use App\DTO\OutingFilter;
use App\Entity\Outing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Outing>
 */
class OutingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outing::class);
    }

    public function findByFilter(OutingFilter $filter, ?int $limit = 20){
        $query = $this->createQueryBuilder('o')->setMaxResults($limit|10);

        if($filter->getCampus() !== null)
            $query
                ->where('o.campus = :campus')
                ->setParameter('campus', $filter->getCampus());

        if($filter->getNameSearch() !== null && $filter->getNameSearch() !== "")
            $query
                ->andWhere('o.name LIKE %:nameSearch%')
                ->setParameter('nameSearch', $filter->getNameSearch());

        if($filter->getStartsAfter() !== null)
            $query
                ->andWhere('o.startDate >= :startsAfter')
                ->setParameter('startsAfter', $filter->getStartsAfter());

        if($filter->getStartsBefore() !== null)
            $query
                ->andWhere('o.startDate <= :endsBefore')
                ->setParameter('endsBefore', $filter->getStartsBefore());

        if($filter->getStartsBefore() == null && $filter->getStartsAfter() == null) {
            if ($filter->isOutingPast())
                $query
                    ->andWhere('o.startDate <= :now')
                    ->setParameter('now', new \DateTime());
            if($filter->isOutingPast() === false)
                $query
                    ->andWhere('o.startDate >= :now')
                    ->setParameter('now', new \DateTime());
        }

        if($filter->getUser() !== null){
            if($filter->isUserOrganizer())
                $query->andWhere('o.organizer = :organizer')
                      ->setParameter('organizer', $filter->getUser());
            if($filter->isUserOrganizer() === false)
                $query->andWhere('o.organizer != :organizer')
                      ->setParameter('organizer', $filter->getUser());

            if($filter->isUserRegistered())
                $query
                    ->andWhere(':user MEMBER OF o.participants')
                    ->setParameter('user', $filter->getUser());
            if($filter->isUserRegistered() === false)
                $query
                    ->andWhere(':user NOT MEMBER OF o.participants')
                    ->setParameter('user', $filter->getUser());
        }

        $query = $query->getQuery();
        dump($query);
        return $query->getResult();
    }

//    /**
//     * @return Outing[] Returns an array of Outing objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Outing
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
