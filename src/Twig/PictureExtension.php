<?php

namespace App\Twig;

use App\Entity\Picture;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PictureExtension extends AbstractExtension
{
    /** @var string */
    private $pathPictures;

    /**
     * @param string $pathPublic
     * @param string $pathPictures
     */
    public function __construct(string $pathPublic, string $pathPictures)
    {
        $this->pathPictures = substr($pathPictures, strlen($pathPublic) + 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('picture_path', [$this, 'getPath']),
        ];
    }

    /**
     * @param Picture $picture
     *
     * @return string
     */
    public function getPath(Picture $picture): string
    {
        return sprintf('%s/%s', $this->pathPictures, $picture->getName());
    }
}
