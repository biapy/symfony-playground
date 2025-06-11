<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Service;

use App\DTO\RootDTO;
use Symfony\Component\Serializer\SerializerInterface;

interface RootDTODeserializerInterface
{
    public function deserialize(string $json): RootDTO;
}
