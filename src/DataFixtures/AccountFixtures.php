<?php

namespace QiniuStorageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use QiniuStorageBundle\Entity\Account;

class AccountFixtures extends Fixture
{
    public const ACCOUNT_REFERENCE = 'account';

    public function load(ObjectManager $manager): void
    {
        $account = new Account();
        $account->setName('测试账号');
        $account->setAccessKey('test_access_key');
        $account->setSecretKey('test_secret_key');
        $account->setValid(true);
        $account->setRemark('测试用七牛云账号');

        $manager->persist($account);
        $manager->flush();

        $this->addReference(self::ACCOUNT_REFERENCE, $account);
    }
}
