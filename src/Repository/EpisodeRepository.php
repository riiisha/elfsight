<?php

namespace App\Repository;

use App\Entity\Episode;
use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Episode>
 */
class EpisodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Episode::class);
    }

    /**
     * @param Episode $review
     * @param bool $flash
     * @return void
     */
    public function save(Episode $review, bool $flash = false): void
    {
        $em = $this->getEntityManager();
        $em->persist($review);
        if ($flash) {
            $em->flush();
        }
    }

}
