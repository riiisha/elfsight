<?php

namespace App\Service;

use App\Entity\Review;
use App\Exception\EpisodeNotFoundException;
use App\Exception\ReviewCreationException;
use App\Repository\EpisodeRepository;
use App\Repository\ReviewRepository;
use Psr\Log\LoggerInterface;
use Sentiment\Analyzer;
use Symfony\Contracts\Cache\CacheInterface;
use Throwable;

readonly class ReviewCreateService
{
    public function __construct(
        private EpisodeRepository $episodeRepository,
        private ReviewRepository  $reviewRepository,
        private LoggerInterface   $logger,
        private CacheInterface    $cache,
    )
    {
    }

    public function create(int $episodeId, string $content): void
    {
        $episode = $this->episodeRepository->find($episodeId);
        if (!$episode) {
            throw new EpisodeNotFoundException($episodeId);
        }

        try {
            $sentimentScore = $this->analyzeSentiment($content);
        } catch (Throwable $e) {
            $this->logger->critical('ReviewCreateService: Sentiment analysis error.', ['exception' => $e]);
            throw new ReviewCreationException('Unable to analyze sentiment.');
        }

        $review = new Review($episode, $content, $sentimentScore);

        try {
            $this->reviewRepository->save($review, true);
            $this->cache->delete("episode_summary_$episodeId");
        } catch (Throwable $e) {
            $this->logger->debug('ReviewCreateService: Database error while creating review.', ['exception' => $e]);
            throw new ReviewCreationException('Failed to save review.');
        }
    }

    private function analyzeSentiment(string $content): float
    {
        $analyzer = new Analyzer();
        $sentimentResult = $analyzer->getSentiment($content);
        $compound = $sentimentResult['compound'];

        return ($compound + 1) / 2;
    }
}