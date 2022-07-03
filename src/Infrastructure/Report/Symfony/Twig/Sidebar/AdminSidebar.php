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
 * Class AdminSidebar.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class AdminSidebar extends AbstractSidebar
{
    public function __construct(
        private readonly Security $security,
        private readonly NotificationRepositoryInterface $notificationRepository,
    ) {
    }

    public function build(SidebarBuilderInterface $builder): SidebarCollection
    {
        /** @var User $admin */
        $admin = $this->security->getUser();
        $n = $this->notificationRepository->countUnreadForUser($admin);

        return $builder->add(new SidebarHeader('report.sidebars.admin'))
            ->add(new SidebarLink('report_admin_dashboard_index', 'report.sidebars.users.links.dashboard', 'growth-fill'))
            ->add(new SidebarLink('notification_index', 'report.sidebars.admins.links.notification', 'bell', $n > 0 ? (string) $n : null))
            ->add(new SidebarLink('administration_user_index', 'report.sidebars.admins.links.user', 'users'))

            ->setTranslationDomain('report')
            ->create();
    }
}
