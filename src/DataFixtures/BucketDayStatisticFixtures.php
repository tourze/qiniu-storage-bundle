<?php

namespace QiniuStorageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Entity\BucketDayStatistic;

class BucketDayStatisticFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $bucket = $this->getReference(BucketFixtures::BUCKET_REFERENCE, Bucket::class);

        $statistic = new BucketDayStatistic();
        $statistic->setBucket($bucket);
        $statistic->setTime(new \DateTimeImmutable('2024-01-01'));
        $statistic->setStandardStorage(1024 * 1024);
        $statistic->setLineStorage(512 * 1024);
        $statistic->setArchiveStorage(256 * 1024);
        $statistic->setStandardCount(100);
        $statistic->setLineCount(50);
        $statistic->setArchiveCount(25);
        $statistic->setGetRequests(1000);
        $statistic->setPutRequests(200);
        $statistic->setInternetTraffic(10 * 1024 * 1024);
        $statistic->setCdnTraffic(5 * 1024 * 1024);

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
