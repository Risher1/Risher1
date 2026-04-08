<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\AppExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('count_status', [$this, 'countstatus']),
        ];
    }

        public function countStatus(array $tasks, string $status): int
    {
        return count(array_filter($tasks, fn($task) => $task->getStatus() === $status));
    }
}
