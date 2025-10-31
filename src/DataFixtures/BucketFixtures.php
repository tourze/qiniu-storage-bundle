<?php

namespace QiniuStorageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Entity\Bucket;

class BucketFixtures extends Fixture implements DependentFixtureInterface
{
    public const BUCKET_REFERENCE = 'bucket';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('test-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('https://images.unsplash.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucket->setRemark('测试存储空间');

        $manager->persist($bucket);
        $manager->flush();

        $this->addReference(self::BUCKET_REFERENCE, $bucket);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
