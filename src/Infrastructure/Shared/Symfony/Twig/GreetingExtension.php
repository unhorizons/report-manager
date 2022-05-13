<?php

declare(strict_types=1);

namespace Infrastructure\Shared\Symfony\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class GreetingExtension.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class GreetingExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('greeting', [$this, 'greeting']),
        ];
    }

    public function greeting(string $name): string
    {
        $time = (int) (new \DateTimeImmutable('now'))->format('H');

        $greeting = match (true) {
            $time >= 13 && $time < 17 => 'Bon après midi',
            $time >= 17 => 'Bonsoir',
            default => 'Bonjour'
        };

        return sprintf('%s, %s', $greeting, $name);
    }
}
