<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Twig\Sidebar;

use Infrastructure\Shared\Symfony\Twig\Sidebar\AbstractSidebar;
use Infrastructure\Shared\Symfony\Twig\Sidebar\SidebarBuilderInterface;
use Infrastructure\Shared\Symfony\Twig\Sidebar\SidebarCollection;
use Infrastructure\Shared\Symfony\Twig\Sidebar\Type\SidebarHeader;
use Infrastructure\Shared\Symfony\Twig\Sidebar\Type\SidebarLink;

/**
 * Class UserSidebar.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UserSidebar extends AbstractSidebar
{
    public function build(SidebarBuilderInterface $builder): SidebarCollection
    {
        return $builder
            ->add(new SidebarHeader('report.sidebars.users.headers.index'))
            ->add(new SidebarLink('app_index', 'report.sidebars.users.links.index', 'folder-list'))
            ->add(new SidebarLink('app_index', 'report.sidebars.users.links.evaluation', 'comments'))

            ->setTranslationDomain('report')
            ->create();
    }
}
