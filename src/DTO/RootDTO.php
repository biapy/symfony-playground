<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;

final readonly class RootDTO
{
    public function __construct(
        public readonly string $name,
        #[Context([UnwrappingDenormalizer::UNWRAP_PATH => 'this'])]
        public readonly ?ChildDTO $child,
    ) {
    }
}
