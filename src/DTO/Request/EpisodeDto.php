<?php

namespace App\DTO\Request;

use DateTimeInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class EpisodeDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,
        #[SerializedName('air_date')]
        #[Assert\NotBlank]
        public DateTimeInterface $airDate,
        #[SerializedName('episode')]
        #[Assert\NotBlank]
        #[Assert\Length(max: 10)]
        public string $episodeCode,
        #[Assert\NotBlank]
        #[Assert\Type('array')]
        #[Assert\All([
            new Assert\Url(message: 'Each character must be a valid URL')
        ])]
        public array  $characters,
        #[Assert\NotBlank]
        public DateTimeInterface $created
    )
    {
    }
}
