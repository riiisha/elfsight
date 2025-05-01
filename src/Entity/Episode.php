<?php

namespace App\Entity;

use App\DTO\Request\EpisodeDto;
use App\Repository\EpisodeRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EpisodeRepository::class)]
class Episode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['episode:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['episode:read'])]
    private string $name;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['episode:read'])]
    private DateTimeInterface $airDate;

    #[ORM\Column(length: 10)]
    #[Groups(['episode:read'])]
    private string $episodeCode;

    #[ORM\Column(type: "json")]
    #[Groups(['episode:read'])]
    private array $characters;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $created;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'episode')]
    private Collection $reviews;

    /**
     * @param string $name
     * @param DateTimeInterface $airDate
     * @param string $episodeCode
     * @param array $characters
     * @param DateTimeInterface $created
     */
    public function __construct(string            $name,
                                DateTimeInterface $airDate,
                                string            $episodeCode,
                                array             $characters,
                                DateTimeInterface $created
    )
    {
        $this->name = $name;
        $this->airDate = $airDate;
        $this->episodeCode = $episodeCode;
        $this->characters = $characters;
        $this->created = $created;
        $this->reviews = new ArrayCollection();
    }

    /**
     * @param EpisodeDto $dto
     * @return self
     */
    public static function fromDto(EpisodeDto $dto): self
    {
        return new self(
            $dto->name,
            $dto->airDate,
            $dto->episodeCode,
            $dto->characters,
            $dto->created
        );
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAirDate(): DateTimeInterface
    {
        return $this->airDate;
    }

    public function getEpisodeCode(): string
    {
        return $this->episodeCode;
    }

    public function getCharacters(): array
    {
        return $this->characters;
    }

    public function getCreated(): DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }
}
