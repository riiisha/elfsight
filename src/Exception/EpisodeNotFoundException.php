<?php

namespace App\Exception;

use RuntimeException;

class EpisodeNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Episode with ID %d not found.', $id));
    }
}