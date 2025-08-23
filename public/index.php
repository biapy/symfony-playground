<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    if (!is_string($context['APP_ENV'])) {
        throw new InvalidArgumentException('APP_ENV must be a string');
    }

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
