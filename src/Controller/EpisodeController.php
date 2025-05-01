<?php

namespace App\Controller;

use App\Service\EpisodeService;
use App\Service\EpisodeSummaryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EpisodeController extends AbstractController
{

    public function __construct(
        readonly EpisodeService        $episodeService,
        readonly EpisodeSummaryService $episodeSummaryService
    )
    {
    }

    #[Route('/api/episodes', name: 'api_episodes', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $episodes = $this->episodeService->getAllEpisodes();

        return $this->json($episodes, Response::HTTP_OK, [], ['groups' => 'episode:read']);
    }

    #[Route('/api/episode/{id}', name: 'api_episode_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $episode = $this->episodeSummaryService->getSummary($id);

        return $this->json($episode);
    }
}
