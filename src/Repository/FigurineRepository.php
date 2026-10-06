<?php

namespace App\Repository;

use App\Entity\Figurine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Figurine>
 */
class FigurineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Figurine::class);
    }

    /**
     * Toutes les figurines, de la plus récente à la plus ancienne,
     * avec leur auteur chargé en une seule requête.
     *
     * @return Figurine[]
     */
    public function findAllWithOwner(): array
    {
        return $this->createQueryBuilder('figurine')
            ->addSelect('owner')
            ->innerJoin('figurine.owner', 'owner')
            ->orderBy('figurine.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Les dernières figurines publiées (pour la page d'accueil).
     *
     * @return Figurine[]
     */
    public function findLatest(int $limit = 6): array
    {
        return $this->createQueryBuilder('figurine')
            ->addSelect('owner')
            ->innerJoin('figurine.owner', 'owner')
            ->orderBy('figurine.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
