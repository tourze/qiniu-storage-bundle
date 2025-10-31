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
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Entity\Bucket;

/**
 * 存储空间管理控制器
 *
 * @extends AbstractCrudController<Bucket>
 */
#[AdminCrud(routePath: '/qiniu-storage/bucket', routeName: 'qiniu_storage_bucket')]
final class BucketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bucket::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('存储空间')
            ->setEntityLabelInPlural('存储空间')
            ->setPageTitle('index', '存储空间管理')
            ->setPageTitle('new', '新建存储空间')
            ->setPageTitle('edit', '编辑存储空间')
            ->setPageTitle('detail', '存储空间详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('account')
            ->add('name')
            ->add('region')
            ->add('private')
            ->add('valid')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            AssociationField::new('account', '七牛账号')
                ->setRequired(true)
                ->setHelp('选择关联的七牛云账号配置')
                ->formatValue(function ($value) {
                    return ($value instanceof Account) ? $value->getName() : '';
                }),

            TextField::new('name', '存储空间名称')
                ->setRequired(true)
                ->setMaxLength(50)
                ->setHelp('七牛云存储空间的名称'),

            TextField::new('region', '存储区域')
                ->setRequired(true)
                ->setMaxLength(50)
                ->setHelp('存储空间所在的区域，如华东、华北等'),

            TextField::new('domain', '访问域名')
                ->setRequired(true)
                ->setMaxLength(255)
                ->setHelp('存储空间的访问域名'),

            BooleanField::new('private', '私有空间')
                ->setHelp('是否为私有存储空间'),

            DateTimeField::new('lastSyncTime', '最后同步时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->setHelp('最近一次同步的时间'),

            TextareaField::new('remark', '备注')
                ->setMaxLength(255)
                ->setNumOfRows(3)
                ->hideOnIndex()
                ->setHelp('存储空间的备注信息'),

            BooleanField::new('valid', '有效状态')
                ->setHelp('存储空间是否有效'),

            DateTimeField::new('createdAt', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),

            DateTimeField::new('updatedAt', '更新时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];

        // 根据页面调整字段显示
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                $fields[0], // id
                $fields[1], // account
                $fields[2], // name
                $fields[3], // region
                $fields[4], // domain
                $fields[5], // private
                $fields[8], // valid
                $fields[9], // createdAt
            ];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return $fields;
        }

        // 表单页面 (new/edit)
        return [
            $fields[1], // account
            $fields[2], // name
            $fields[3], // region
            $fields[4], // domain
            $fields[5], // private
            $fields[7], // remark
            $fields[8], // valid
        ];
    }
}
