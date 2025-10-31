<?php

namespace QiniuStorageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Entity\BucketHourStatistic;

class BucketHourStatisticFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $bucket = $this->getReference(BucketFixtures::BUCKET_REFERENCE, Bucket::class);

        $statistic = new BucketHourStatistic();
        $statistic->setBucket($bucket);
        $statistic->setTime(new \DateTimeImmutable('2024-01-01 10:00:00'));
        $statistic->setStandardStorage(100 * 1024);
        $statistic->setLineStorage(50 * 1024);
        $statistic->setArchiveStorage(25 * 1024);
        $statistic->setStandardCount(10);
        $statistic->setLineCount(5);
        $statistic->setArchiveCount(2);
        $statistic->setGetRequests(100);
        $statistic->setPutRequests(20);
        $statistic->setInternetTraffic(1024 * 1024);
        $statistic->setCdnTraffic(512 * 1024);

        $manager->persist($statistic);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BucketFixtures::class,
        ];
    }
}
