<?php

namespace App\Repository;

use App\DTO\FilterFaecher;
use App\Entity\Faecher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Faecher>
 *
 * @method Faecher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Faecher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Faecher[]    findAll()
 * @method Faecher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaecherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private LoggerInterface $logger)
    {
        parent::__construct($registry, Faecher::class);
    }

    public function save(Faecher $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Faecher $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function filterAll(FilterFaecher $dtoFilter){
        $this->logger->info("Filtermethode für faecher aufgerufen");
        $qb = $this->createQueryBuilder("b");

        if($dtoFilter->fach) {
            $this->logger->info("Filter faecher: {fach}", ["fach" => $dtoFilter->fach]);
            $qb = $qb->andWhere("b.Fach like :p")
                ->setParameter("p", $dtoFilter->fach . "%");
        }

        if ($dtoFilter->orderby){

            $qb->orderBy($dtoFilter->orderby, $dtoFilter->orderdirection ?? "ASC");

        }


        return $qb
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Faecher[] Returns an array of Faecher objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Faecher
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}



