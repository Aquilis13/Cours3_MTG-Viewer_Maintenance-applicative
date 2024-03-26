<?php

namespace App\Repository;

use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Card>
 *
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    public function getAllUuids(): array
    {
        $result =  $this->createQueryBuilder('c')
            ->select('c.uuid')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY)
        ;
        return array_column($result, 'uuid');
    }

    public function findAllSetCodes()
    {
        $result = $this->createQueryBuilder('c')
            ->select('DISTINCT c.setCode')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY)
        ;
        return array_column($result, 'setCode');
    }

    public function countCards($setCode) : int
    {
        $result = $this->createQueryBuilder('c')
            ->select('COUNT(c) as nb_result');

        if ($setCode !== null) {
            $result->where('c.setCode = :setCode')
                ->setParameter('setCode', $setCode);
        }

        $result = $result
            ->getQuery()
            ->getSingleScalarResult()
        ;
        return $result;
    }
}
