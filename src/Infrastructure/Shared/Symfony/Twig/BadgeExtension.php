<?php

declare(strict_types=1);

namespace Infrastructure\Shared\Symfony\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class BadgeExtension
 * @package Infrastructure\Shared\Symfony\Twig
 * @author bernard-ng <bernard@devscast.tech>
 */
class BadgeExtension extends AbstractExtension
{
    private TranslatorInterface $translator;
    private array $badges;

    /**
     * BadgeExtension constructor.
     * @param array $config
     * @param TranslatorInterface $translator
     * @author bernard-ng <bernard@devscast.tech>
     */
    public function __construct(array $config, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->badges = $config['badges'] ?: [];
    }

    /**
     * @return array
     * @author bernard-ng <bernard@devscast.tech>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('badge', [$this, 'badge'], ['is_safe' => ['html']]),
            new TwigFilter('boolean', [$this, 'boolean'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param int $value
     * @return string
     * @author bernard-ng <bernard@devscast.tech>
     */
    public function boolean(int $value): string
    {
        if ($value === 1) {
            return <<< HTML
                <em class="icon ni ni-check-circle-fill text-success" aria-label="icon check" role="img"></em>
            HTML;
        } else {
            return <<< HTML
                <em class="icon ni ni-cross-circle-fill text-danger" aria-label="icon cross" role="img"></em>
            HTML;
        }
    }

    /**
     * @param string $label
     * @return string
     * @author bernard-ng <bernard@devscast.tech>
     */
    public function badge(string $label): string
    {
        if (array_key_exists($label, $this->badges)) {
            $style = $this->badges[$label]['style'];
            $state = $this->badges[$label]['state'];
            $label = $this->translator->trans($label);

            return <<< HTML
                <span aria-label="$label" class="badge badge-$style badge-outline-$state">
                    $label
                </span>
            HTML;
        }

        throw new \InvalidArgumentException(sprintf("Unknown %s badge, did you forget to configure it ?", $label));
    }
}
