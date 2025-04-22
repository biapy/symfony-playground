<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class BlogPostCrudController extends AbstractCrudController
{
    #[\Override]
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
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

        $blogPost = new BlogPost(
            title: '',
            content: '',
        );

        return $blogPost;
    }
}
