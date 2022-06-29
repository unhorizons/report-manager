<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Twig\Sidebar;

use Domain\Authentication\Entity\User;
use Domain\Notification\Repository\NotificationRepositoryInterface;
use Infrastructure\Shared\Symfony\Twig\Sidebar\AbstractSidebar;
use Infrastructure\Shared\Symfony\Twig\Sidebar\SidebarBuilderInterface;
use Infrastructure\Shared\Symfony\Twig\Sidebar\SidebarCollection;
use Infrastructure\Shared\Symfony\Twig\Sidebar\Type\SidebarHeader;
use Infrastructure\Shared\Symfony\Twig\Sidebar\Type\SidebarLink;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserSidebar.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class EmployeeSidebar extends AbstractSidebar
{
    public function __construct(
        private readonly Security $security,
        private readonly NotificationRepositoryInterface $notificationRepository,
    ) {
    }

    public function build(SidebarBuilderInterface $builder): SidebarCollection
    {
        /** @var User $manager */
        $manager = $this->security->getUser();
        $notifications = $this->notificationRepository->countUnreadForUser($manager);

        return $builder
            ->add(new SidebarHeader('report.sidebars.users.headers.index'))
            ->add(new SidebarLink('report_employee_dashboard_index', 'report.sidebars.users.links.dashboard', 'growth-fill'))
            ->add(new SidebarLink('notification_index', 'report.sidebars.managers.links.notification', 'bell', (string) $notifications))
            ->add(new SidebarLink('report_employee_report_index', 'report.sidebars.users.links.index', 'folder-list'))
            ->add(new SidebarLink('report_employee_evaluation_index', 'report.sidebars.users.links.evaluation', 'comments'))

            ->setTranslationDomain('report')
            ->create();
    }
}
