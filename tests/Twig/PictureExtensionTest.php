<?php

namespace App\Tests\Twig;

use App\Entity\Picture;
use App\Twig\PictureExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class PictureExtensionTest extends TestCase
{
    public function testGetFunctions()
    {
        $pictureExtension = new PictureExtension('', '');

        $this->assertEquals(
            [new TwigFunction('picture_path', [$pictureExtension, 'getPath'])],
            $pictureExtension->getFunctions()
        );
    }

    public function testGetPath()
    {
        $picture = (new Picture())
            ->setName('name')
        ;

        $pictureExtension = new PictureExtension('/path/to/public', '/path/to/public/data/pictures');
        $this->assertSame('data/pictures/name', $pictureExtension->getPath($picture));
    }
}
