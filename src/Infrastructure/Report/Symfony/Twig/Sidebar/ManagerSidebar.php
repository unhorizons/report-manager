<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Twig\Sidebar;

use Domain\Authentication\Entity\User;
use Domain\Notification\Repository\NotificationRepositoryInterface;
use Domain\Report\Repository\ReportRepositoryInterface;
use Infrastructure\Shared\Symfony\Twig\Sidebar\AbstractSidebar;
use Infrastructure\Shared\Symfony\Twig\Sidebar\SidebarBuilderInterface;
use Infrastructure\Shared\Symfony\Twig\Sidebar\SidebarCollection;
use Infrastructure\Shared\Symfony\Twig\Sidebar\Type\SidebarHeader;
use Infrastructure\Shared\Symfony\Twig\Sidebar\Type\SidebarLink;
use Symfony\Component\Security\Core\Security;

/**
 * Class ReportManagerSidebar.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ManagerSidebar extends AbstractSidebar
{
    public function __construct(
        private readonly Security $security,
        private readonly NotificationRepositoryInterface $notificationRepository,
        private readonly ReportRepositoryInterface $reportRepository
    ) {
    }

    public function build(SidebarBuilderInterface $builder): SidebarCollection
    {
        /** @var User $manager */
        $manager = $this->security->getUser();
        $n = $this->notificationRepository->countUnreadForUser($manager);
        $r = $this->reportRepository->countUnseenForManager($manager);

        return $builder
            ->add(new SidebarHeader('report.sidebars.managers.headers.index'))
            ->add(new SidebarLink('report_manager_dashboard_index', 'report.sidebars.users.links.dashboard', 'growth-fill'))
            ->add(new SidebarLink('notification_index', 'report.sidebars.managers.links.notification', 'bell', $n > 0 ? (string) $n : null))
            ->add(new SidebarLink('report_manager_report_index', 'report.sidebars.managers.links.all', 'folder-list', $r > 0 ? (string) $r : null))
            ->add(new SidebarLink('report_manager_employee_index', 'report.sidebars.managers.links.employee', 'users'))
            ->add(new SidebarLink('report_search_index', 'report.sidebars.managers.links.search', 'search'))

            ->setTranslationDomain('report')
            ->create();
    }
}
