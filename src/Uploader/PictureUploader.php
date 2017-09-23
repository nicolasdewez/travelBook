<?php

namespace App\Uploader;

use App\Generator\UniqFileNameGenerator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureUploader
{
    /** @var string */
    private $pathPictures;

    /** @var UniqFileNameGenerator */
    private $generator;

    /**
     * @param UniqFileNameGenerator $generator
     * @param string                $pathPictures
     */
    public function __construct(UniqFileNameGenerator $generator, string $pathPictures)
    {
        $this->pathPictures = $pathPictures;
        $this->generator = $generator;
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    public function execute(UploadedFile $file): string
    {
        $fileName = $this->generator->execute();

        $file->move($this->pathPictures, $fileName);

        return $fileName;
    }
}
