<?php

namespace App\DTO\Response;

readonly class ReviewSummaryDto
{
    /**
     * @param string $content
     * @param float $sentimentScore
     * @param string $createdAt
     */
    public function __construct(
        public string $content,
        public float  $sentimentScore,
        public string $createdAt
    )
    {
    }

}