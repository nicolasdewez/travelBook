<?php

namespace App\Tests\Serializer;

use App\Entity\InvalidationPicture;
use App\Serializer\InvalidationPictureDenormalizer;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class InvalidationPictureDenormalizerTest extends TestCase
{
    public function testSupportsDenormalization()
    {
        $invalidationPictureDenormalizer = new InvalidationPictureDenormalizer($this->createMock(EntityManagerInterface::class));

        $this->assertTrue($invalidationPictureDenormalizer->supportsDenormalization(['id' => 1], InvalidationPicture::class));
        $this->assertFalse($invalidationPictureDenormalizer->supportsDenormalization([], InvalidationPicture::class));
        $this->assertFalse($invalidationPictureDenormalizer->supportsDenormalization(['id' => 1], InvalidationPictureDenormalizer::class));
    }

    public function testDenormalize()
    {
        $invalidationPicture = new InvalidationPicture();

        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($invalidationPicture)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(InvalidationPicture::class)
            ->willReturn($repository)
        ;

        $invalidationPictureDenormalizer = new InvalidationPictureDenormalizer($manager);
        $this->assertSame($invalidationPicture, $invalidationPictureDenormalizer->denormalize(['id' => 1], ''));
    }
}
