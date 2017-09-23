<?php

namespace App\Tests\Uploader;

use App\Generator\UniqFileNameGenerator;
use App\Uploader\PictureUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureUploaderTest extends TestCase
{
    public function testExecute()
    {
        $generator = $this->createMock(UniqFileNameGenerator::class);
        $generator
            ->expects($this->once())
            ->method('execute')
            ->withAnyParameters()
            ->willReturn('filename')
        ;

        $uploader = new PictureUploader($generator, '/path/to/pictures');
        $file = $this->createMock(UploadedFile::class);
        $file
            ->expects($this->once())
            ->method('move')
            ->with('/path/to/pictures', 'filename')
        ;

        $this->assertSame('filename', $uploader->execute($file));
    }
}
