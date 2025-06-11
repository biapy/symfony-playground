<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use App\Security\Traits\SecurityAwareTrait;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<BlogPost>
 */
final class BlogPostCrudController extends AbstractCrudController
{
    use SecurityAwareTrait;

    #[\Override]
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        unset($pageName);

        return [
            TextField::new('title'),
            TextEditorField::new('content'),
            DateTimeField::new('published')
                ->setFormat('dd/MM/yyyy HH:mm:ss')
                ->setHelp('Date and time of publication'),
        ];
    }

    #[\Override]
    public function createEntity(string $entityFqcn): BlogPost
    {
        unset($entityFqcn);

        return new BlogPost(
            title: '',
            content: '',
            author: $this->getAuthenticatedUserEntity(),
        );
    }
}
