<?php

namespace App\Service;

use App\Entity\Episode;
use App\Repository\EpisodeRepository;

readonly class EpisodeService
{
    public function __construct(
        private EpisodeRepository $episodeRepository
    )
    {
    }

    /**
     * @return Episode[]
     */
    public function getAllEpisodes(): array
    {
        return $this->episodeRepository->findAll();
    }
}