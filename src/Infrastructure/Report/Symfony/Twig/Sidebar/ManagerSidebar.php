<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Twig\Sidebar;

use Infrastructure\Shared\Symfony\Twig\Sidebar\AbstractSidebar;
use Infrastructure\Shared\Symfony\Twig\Sidebar\SidebarBuilderInterface;
use Infrastructure\Shared\Symfony\Twig\Sidebar\SidebarCollection;
use Infrastructure\Shared\Symfony\Twig\Sidebar\Type\SidebarHeader;
use Infrastructure\Shared\Symfony\Twig\Sidebar\Type\SidebarLink;

/**
 * Class ReportManagerSidebar.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ManagerSidebar extends AbstractSidebar
{
    public function build(SidebarBuilderInterface $builder): SidebarCollection
    {
        return $builder
            ->add(new SidebarHeader('report.sidebars.managers.headers.index'))
            ->add(new SidebarLink('app_index', 'report.sidebars.users.links.dashboard', 'home'))

            ->add(new SidebarLink('report_manager_report_index', 'report.sidebars.managers.links.all', icon: 'folder-list'))
            ->add(new SidebarLink('report_manager_employee_index', 'report.sidebars.managers.links.employee', icon: 'users'))

            ->setTranslationDomain('report')
            ->create();
    }
}
