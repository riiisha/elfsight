<?php

namespace App\DTO\Response;

use InvalidArgumentException;

readonly class EpisodeSummaryDto
{
    /**
     * @param int $id
     * @param string $name
     * @param string $airDate
     * @param float|null $averageSentimentScore
     * @param ReviewSummaryDto[] $lastReviews
     */
    public function __construct(
        public int    $id,
        public string $name,
        public string $airDate,
        public ?float  $averageSentimentScore,
        public array  $lastReviews
    )
    {
        foreach ($lastReviews as $lastReview) {
            if (!$lastReview instanceof ReviewSummaryDto) {
                throw new InvalidArgumentException(
                    'EpisodeSummaryDto: Invalid element in $lastReviews, expected ReviewSummaryDto'
                );
            }
        }
    }
}