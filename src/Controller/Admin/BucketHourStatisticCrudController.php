<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Entity\BucketHourStatistic;

/**
 * 七牛云存储空间小时统计管理控制器
 *
 * @extends AbstractCrudController<BucketHourStatistic>
 */
#[AdminCrud(routePath: '/qiniu-storage/bucket-hour-statistic', routeName: 'qiniu_storage_bucket_hour_statistic')]
final class BucketHourStatisticCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BucketHourStatistic::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('存储空间小时统计')
            ->setEntityLabelInPlural('存储空间小时统计列表')
            ->setPageTitle('index', '存储空间小时统计管理')
            ->setPageTitle('detail', fn (BucketHourStatistic $statistic) => sprintf('存储空间小时统计详情: %s - %s', $statistic->getBucket()->getName(), $statistic->getTime()->format('Y-m-d H:00')))
            ->setDefaultSort(['time' => 'DESC'])
            ->setSearchFields(['bucket.name'])
            ->setHelp('index', '查看七牛云存储空间的小时统计数据')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('bucket', '存储空间'))
            ->add(DateTimeFilter::new('time', '统计时间'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('bucket', '存储空间')
            ->formatValue(fn ($value) => ($value instanceof Bucket) ? $value->getName() : '')
            ->setRequired(false)
        ;

        yield DateTimeField::new('time', '统计时间')
            ->setFormat('yyyy-MM-dd HH:mm')
            ->setRequired(false)
        ;

        // 存储量统计 (字节)
        yield NumberField::new('standardStorage', '标准存储量(字节)')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('lineStorage', '低频存储量(字节)')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('archiveStorage', '归档存储量(字节)')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('archiveIrStorage', '归档直读存储量(字节)')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('deepArchiveStorage', '深度归档存储量(字节)')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('intelligentTieringStorage', '智能分层存储量(字节)')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('intelligentTieringFrequentStorage', '智能分层频繁访问层存储量(字节)')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('intelligentTieringInfrequentStorage', '智能分层不频繁访问层存储量(字节)')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('intelligentTieringArchiveStorage', '智能分层归档直读访问层存储量(字节)')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        // 文件数统计
        yield NumberField::new('standardCount', '标准存储文件数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('lineCount', '低频存储文件数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('archiveCount', '归档存储文件数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('archiveIrCount', '归档直读存储文件数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('deepArchiveCount', '深度归档存储文件数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('intelligentTieringCount', '智能分层存储文件数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('intelligentTieringFrequentCount', '智能分层频繁访问层文件数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('intelligentTieringInfrequentCount', '智能分层不频繁访问层文件数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('intelligentTieringArchiveCount', '智能分层归档直读访问层文件数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('intelligentTieringMonitorCount', '智能分层监控文件数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        // 流量统计 (字节)
        yield NumberField::new('internetTraffic', '外网流出流量(字节)')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('cdnTraffic', 'CDN回源流量(字节)')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        // 请求次数统计
        yield NumberField::new('getRequests', 'GET请求次数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        yield NumberField::new('putRequests', 'PUT请求次数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;

        // 其他统计
        yield NumberField::new('storageTypeConversions', '存储类型转换次数')
            ->setNumDecimals(0)
            ->hideOnIndex()
        ;
    }
}
