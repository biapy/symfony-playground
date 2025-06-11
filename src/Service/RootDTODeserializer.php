<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Service;

use App\DTO\RootDTO;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class RootDTODeserializer implements RootDTODeserializerInterface
{

    public function __construct(
        public readonly SerializerInterface $serializer,
    ) {
    }

    #[\Override]
    public function deserialize(string $json): RootDTO
    {
        return $this->serializer->deserialize($json, RootDTO::class, 'json');
    }
}
