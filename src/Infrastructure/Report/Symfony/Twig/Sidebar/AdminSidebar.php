<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Twig\Sidebar;

use Infrastructure\Shared\Symfony\Twig\Sidebar\AbstractSidebar;
use Infrastructure\Shared\Symfony\Twig\Sidebar\SidebarBuilderInterface;
use Infrastructure\Shared\Symfony\Twig\Sidebar\SidebarCollection;
use Infrastructure\Shared\Symfony\Twig\Sidebar\Type\SidebarHeader;

/**
 * Class AdminSidebar.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class AdminSidebar extends AbstractSidebar
{
    public function build(SidebarBuilderInterface $builder): SidebarCollection
    {
        return $builder->add(new SidebarHeader('report.sidebars.admin'))
            ->create();
    }
}
