<?php

namespace QiniuStorageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Entity\BucketMinuteStatistic;

class BucketMinuteStatisticFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $bucket = $this->getReference(BucketFixtures::BUCKET_REFERENCE, Bucket::class);

        $statistic = new BucketMinuteStatistic();
        $statistic->setBucket($bucket);
        $statistic->setTime(new \DateTimeImmutable('2024-01-01 10:05:00'));
        $statistic->setStandardStorage(10 * 1024);
        $statistic->setLineStorage(5 * 1024);
        $statistic->setArchiveStorage(2 * 1024);
        $statistic->setStandardCount(5);
        $statistic->setLineCount(2);
        $statistic->setArchiveCount(1);
        $statistic->setGetRequests(50);
        $statistic->setPutRequests(10);
        $statistic->setInternetTraffic(100 * 1024);
        $statistic->setCdnTraffic(50 * 1024);

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
