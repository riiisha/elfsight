<?php

namespace App\Service;

use App\DTO\Response\EpisodeSummaryDto;
use App\DTO\Response\ReviewSummaryDto;
use App\Exception\EpisodeNotFoundException;
use App\Repository\EpisodeRepository;
use App\Repository\ReviewRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class EpisodeSummaryService
{
    public function __construct(
        private EpisodeRepository $episodeRepository,
        private ReviewRepository  $reviewRepository,
        private CacheInterface    $cache
    )
    {
    }

    /**
     * @param int $episodeId
     * @return EpisodeSummaryDto
     * @throws InvalidArgumentException
     */
    public function getSummary(int $episodeId): EpisodeSummaryDto
    {
        $episode = $this->episodeRepository->find($episodeId);
        if (!$episode) {
            throw new EpisodeNotFoundException($episodeId);
        }

        $cacheKey = "episode_summary_$episodeId";
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($episode) {
            $item->expiresAfter(3600);

            $avgSentimentScore = $this->reviewRepository->getAverageSentimentScoreByEpisodeId($episode->getId());
            $lastReviews = $this->reviewRepository->findLastReviewsForEpisode($episode->getId());

            $reviewDtos = array_map(
                fn($review) => new ReviewSummaryDto(
                    content: $review->getContent(),
                    sentimentScore: $review->getSentimentScore(),
                    createdAt: $review->getCreatedAt()->format('Y-m-d H:i:s')
                ),
                $lastReviews
            );

            return new EpisodeSummaryDto(
                id: $episode->getId(),
                name: $episode->getName(),
                airDate: $episode->getAirDate()->format('Y-m-d'),
                averageSentimentScore: $avgSentimentScore ? round($avgSentimentScore, 3) : null,
                lastReviews: $reviewDtos
            );
        });
    }
}