<?php

namespace App\Command;

use App\DTO\Request\EpisodeDto;
use App\Entity\Episode;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;
use UnexpectedValueException;


#[AsCommand(
    name: 'app:import-episodes',
    description: 'Imports episodes from the Rick and Morty API and stores them in the database.',
)]
class ImportEpisodesCommand extends Command
{
    private string $apiUrl;

    public function __construct(
        private readonly HttpClientInterface    $httpClient,
        private readonly EpisodeRepository      $episodeRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface        $logger,
        private readonly SerializerInterface    $serializer,
        private readonly ValidatorInterface     $validation,
        string                                  $rickAndMortyApiUrl

    )
    {
        $this->apiUrl = $rickAndMortyApiUrl . 'episode';
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting to import episodes...');
        $url = $this->apiUrl;
        do {
            try {
                $response = $this->httpClient->request('GET', $url);
                $data = $response->toArray();
                if (empty($data['results'])) {
                    throw new UnexpectedValueException('ImportEpisodesCommand: API response contains no results');
                }
            } catch (Throwable $e) {
                $this->logger->warning('ImportEpisodesCommand: Failed to fetch data from API', ['exception' => $e]);
                $output->writeln('<error>Failed to fetch data from API</error>');
                return Command::FAILURE;
            }

            $url = $data['info']['next'] ?? null;

            foreach ($data['results'] as $episodeData) {
                try {
                    $episodeDto = $this->serializer->denormalize($episodeData, EpisodeDto::class);
                    $errors = $this->validation->validate($episodeDto);
                    if (count($errors) > 0) {
                        throw new UnexpectedValueException($errors[0]->getMessage());
                    }
                    $this->handleEpisodeData($episodeDto);
                } catch (Throwable $e) {
                    $this->logger->critical('ImportEpisodesCommand: Failed to process episode', [
                        'episode' => $episodeData['episode'] ?? null,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

        } while ($url);

        $this->entityManager->flush();
        $output->writeln('Episodes have been successfully imported!');

        return Command::SUCCESS;
    }


    /**
     * @param EpisodeDto $episodeDto
     * @return void
     */
    private function handleEpisodeData(EpisodeDto $episodeDto): void
    {
        $existingEpisode = $this->episodeRepository->findOneBy(['episodeCode' => $episodeDto->episodeCode]);
        if ($existingEpisode) {
            return;
        }

        $episode = Episode::fromDto($episodeDto);
        $this->episodeRepository->save($episode);
    }
}
