<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class ChildDTO
{
    public function __construct(
        #[SerializedName('child-name')]
        public readonly string $name,
    ) {
    }
}
