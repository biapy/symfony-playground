<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\Symfony\Bridge\Symfony\Routing\SymfonyRoutesProvider;
use Rector\Symfony\Contract\Bridge\Symfony\Routing\SymfonyRoutesProviderInterface;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
        __DIR__.'/migrations',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        // Don't rename variables and properties to match contents.
        naming: false,
        privatization: true,
        typeDeclarations: true,
        rectorPreset: true,
    )
    // Generate Rector configuration from composer.json
    // @see https://getrector.com/documentation/composer-based-sets
    ->withComposerBased(twig: true, doctrine: true, phpunit: true, symfony: true)
    ->withPhpSets()
    ->withAttributesSets()
    // import FQN
    // @see https://getrector.com/documentation/import-names
    ->withImportNames(
        importShortClasses: false,
        removeUnusedImports: true
    )

    // @see https://github.com/rectorphp/rector-symfony
    // Provide Symfony PHP container.
    ->withSymfonyContainerPhp(__DIR__.'/tests/RectorSymfonyContainer.php')

    // Add Symfony Route annotation config.
    ->registerService(SymfonyRoutesProvider::class, SymfonyRoutesProviderInterface::class)

    // Prevent using $this-> instead of self:: for PHPUnit static methods
    // @see https://getrector.com/documentation/ignoring-rules-or-paths
    ->withSkip([
        PreferPHPUnitThisCallRector::class => [
            __DIR__.'/tests/*',
        ],
    ]);
