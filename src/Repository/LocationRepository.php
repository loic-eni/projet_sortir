<?php

namespace App\Repository;

use App\DTO\LocationFilter;
use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function findByFilter(LocationFilter $locationFilter) {
        $query = $this
                    ->createQueryBuilder('l')
                    ->where('l.deletedAt IS NULL');

        if($locationFilter->getName() !== null){
            $query
                ->andWhere($query->expr()->like('l.name', ':name'))
                ->setParameter('name', '%' . $locationFilter->getName() . '%');
        }

        $query = $query->getQuery();
        return $query->getResult();
    }

    public function findAllNotDeleted(): array
    {
        $query = $this->createQueryBuilder('l');
        $query
            ->andWhere('l.deletedAt IS NULL');

        $query = $query->getQuery();
        return $query->getResult();
    }
}
