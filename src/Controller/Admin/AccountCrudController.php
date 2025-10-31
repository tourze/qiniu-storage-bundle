<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Controller\Admin;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use QiniuStorageBundle\Entity\Account;

/**
 * 七牛云账号配置管理控制器
 *
 * @extends AbstractCrudController<Account>
 */
#[AdminCrud(routePath: '/qiniu-storage/account', routeName: 'qiniu_storage_account')]
final class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('七牛云账号')
            ->setEntityLabelInPlural('七牛云账号')
            ->setPageTitle('index', '七牛云账号列表')
            ->setPageTitle('new', '新建七牛云账号')
            ->setPageTitle('edit', '编辑七牛云账号')
            ->setPageTitle('detail', '七牛云账号详情')
            ->setHelp('index', '管理七牛云存储服务的账号配置信息，包含Access Key和Secret Key等认证信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'accessKey', 'remark'])
            ->setPaginatorPageSize(25)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        // 基本字段
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->onlyOnIndex()
        ;

        yield TextField::new('name', '配置名称')
            ->setHelp('便于识别的配置名称，如：生产环境、测试环境等')
            ->setRequired(true)
            ->setMaxLength(50)
        ;

        yield BooleanField::new('valid', '有效状态')
            ->setHelp('是否启用该配置，只有有效的配置才会被使用')
        ;

        yield TextField::new('accessKey', 'Access Key')
            ->setHelp('七牛云提供的访问密钥ID，用于身份识别')
            ->setRequired(true)
            ->setMaxLength(100)
        ;

        // secretKey字段在不同页面的不同处理
        if (Crud::PAGE_INDEX === $pageName) {
            // 列表页显示掩码
            yield TextField::new('secretKeyMasked', 'Secret Key');
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            // 详情页显示为密码类型
            yield TextField::new('secretKey', 'Secret Key')
                ->setHelp('七牛云提供的秘密访问密钥，请妥善保管')
                ->formatValue(function ($value) {
                    return '••••••••••••••••••••';
                })
            ;
        } else {
            // 新建和编辑页面
            yield TextField::new('secretKey', 'Secret Key')
                ->setHelp('七牛云提供的秘密访问密钥，请妥善保管')
                ->setRequired(true)
                ->setMaxLength(100)
                ->setFormTypeOption('attr', ['type' => 'password'])
            ;
        }

        yield TextareaField::new('remark', '备注')
            ->hideOnIndex()
            ->setMaxLength(255)
            ->setHelp('可以记录该配置的用途、环境等额外信息')
        ;

        // 时间戳字段
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        // 创建者字段（仅详情页显示）
        if (Crud::PAGE_DETAIL === $pageName) {
            yield TextField::new('createdBy', '创建者')
                ->hideOnForm()
                ->formatValue(function ($value) {
                    return $value ?? '系统';
                })
            ;

            yield TextField::new('updatedBy', '更新者')
                ->hideOnForm()
                ->formatValue(function ($value) {
                    return $value ?? '系统';
                })
            ;
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '配置名称'))
            ->add(TextFilter::new('accessKey', 'Access Key'))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(TextFilter::new('remark', '备注'))
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->select('entity')
            ->orderBy('entity.id', 'DESC')
        ;
    }
}
