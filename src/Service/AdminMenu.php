<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Service;

use Knp\Menu\ItemInterface;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Entity\BucketDayStatistic;
use QiniuStorageBundle\Entity\BucketHourStatistic;
use QiniuStorageBundle\Entity\BucketMinuteStatistic;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        // 创建或获取七牛云存储菜单
        $qiniuMenu = $item->getChild('七牛云存储');
        if (null === $qiniuMenu) {
            $qiniuMenu = $item->addChild('七牛云存储');
        }
        $qiniuMenu->setAttribute('icon', 'fas fa-cloud');

        // 创建或获取账号管理菜单
        $accountMenu = $qiniuMenu->getChild('账号管理');
        if (null === $accountMenu) {
            $accountMenu = $qiniuMenu->addChild('账号管理');
        }
        $accountMenu->setAttribute('icon', 'fas fa-user-gear');

        // 添加账号管理子菜单
        $accountMenu
            ->addChild('七牛云账号')
            ->setUri($this->linkGenerator->getCurdListPage(Account::class))
            ->setAttribute('icon', 'fas fa-key')
        ;

        $accountMenu
            ->addChild('存储空间')
            ->setUri($this->linkGenerator->getCurdListPage(Bucket::class))
            ->setAttribute('icon', 'fas fa-database')
        ;

        // 创建或获取统计数据菜单
        $statsMenu = $qiniuMenu->getChild('统计数据');
        if (null === $statsMenu) {
            $statsMenu = $qiniuMenu->addChild('统计数据');
        }
        $statsMenu->setAttribute('icon', 'fas fa-chart-line');

        // 添加统计数据子菜单
        $statsMenu
            ->addChild('天级统计')
            ->setUri($this->linkGenerator->getCurdListPage(BucketDayStatistic::class))
            ->setAttribute('icon', 'fas fa-calendar-day')
        ;

        $statsMenu
            ->addChild('小时统计')
            ->setUri($this->linkGenerator->getCurdListPage(BucketHourStatistic::class))
            ->setAttribute('icon', 'fas fa-clock')
        ;

        $statsMenu
            ->addChild('分钟统计')
            ->setUri($this->linkGenerator->getCurdListPage(BucketMinuteStatistic::class))
            ->setAttribute('icon', 'fas fa-stopwatch')
        ;
    }
}
