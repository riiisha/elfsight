<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['episode:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private Episode $episode;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['episode:read'])]
    private string $content;

    #[ORM\Column]
    #[Groups(['episode:read'])]
    private float $sentimentScore;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $createdAt;

    /**
     * @param Episode $episode
     * @param string $content
     * @param float $sentimentScore
     */
    public function __construct(Episode $episode, string $content, float $sentimentScore)
    {
        if ($sentimentScore < 0 || $sentimentScore > 1) {
            throw new InvalidArgumentException('Sentiment score must be between 0 and 1.');
        }

        $this->episode = $episode;
        $this->content = $content;
        $this->sentimentScore = $sentimentScore;
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEpisode(): Episode
    {
        return $this->episode;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSentimentScore(): float
    {
        return $this->sentimentScore;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
