<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\BlogPost;
use AutoMapper\Attribute\Mapper;
use Symfony\Component\Validator\Constraints as Assert;

#[Mapper(source: BlogPost::class, target: BlogPost::class, dateTimeFormat: 'Y-m-d H:i:s')]
final class BlogPostModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $title,

        #[Assert\NotBlank]
        public string $content,

        #[Assert\DateTime]
        public ?string $published = null,
    ) {
    }
}
