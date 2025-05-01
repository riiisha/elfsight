<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @param Review $review
     * @param bool $flash
     * @return void
     */
    public function save(Review $review, bool $flash = false): void
    {
        $em = $this->getEntityManager();
        $em->persist($review);
        if ($flash) {
            $em->flush();
        }
    }

    /**
     * @param int $episodeId
     * @return float|null
     */
    public function getAverageSentimentScoreByEpisodeId(int $episodeId): ?float
    {
        return $this->createQueryBuilder('r')
            ->select('AVG(r.sentimentScore) as avgScore')
            ->where('r.episode = :episodeId')
            ->setParameter('episodeId', $episodeId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param int $episodeId
     * @param int $limit
     * @return Review[]
     */
    public function findLastReviewsForEpisode(int $episodeId, int $limit = 3): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.episode = :episodeId')
            ->setParameter('episodeId', $episodeId)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
