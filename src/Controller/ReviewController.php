<?php

namespace App\Controller;

use App\DTO\Request\CreateReviewDto;
use App\Service\ReviewCreateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{
    public function __construct(private readonly ReviewCreateService $reviewCreateService)
    {
    }

    #[Route('/api/episodes/{id}/reviews', name: 'episode_review_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateReviewDto $createReviewRequest, int $id): JsonResponse
    {
        $this->reviewCreateService->create($id, $createReviewRequest->content);

        return $this->json(['message' => 'Review created successfully.'], Response::HTTP_CREATED);
    }
}
