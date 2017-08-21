<?php

namespace App\Tests\Serializer;

use App\Entity\Picture;
use App\Serializer\PictureDenormalizer;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PictureDenormalizerTest extends TestCase
{
    public function testSupportsDenormalization()
    {
        $pictureDenormalizer = new PictureDenormalizer($this->createMock(EntityManagerInterface::class));

        $this->assertTrue($pictureDenormalizer->supportsDenormalization(['id' => 1], Picture::class));
        $this->assertFalse($pictureDenormalizer->supportsDenormalization([], Picture::class));
        $this->assertFalse($pictureDenormalizer->supportsDenormalization(['id' => 1], PictureDenormalizer::class));
    }

    public function testDenormalize()
    {
        $picture = new Picture();

        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($picture)
        ;

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Picture::class)
            ->willReturn($repository)
        ;

        $pictureDenormalizer = new PictureDenormalizer($manager);
        $this->assertSame($picture, $pictureDenormalizer->denormalize(['id' => 1], ''));
    }
}
