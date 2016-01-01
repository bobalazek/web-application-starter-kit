<?php

namespace Application\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Cocur\Slugify\Slugify;

/**
 * Abstract image upload
 *
 * Some default methods for the abstract image upload
 *
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class AbstractImageUpload
{
    /**
     * @var File
     */
    protected $image;

    /**
     * @var string
     */
    protected $imageUploadPath;

    /**
     * @var string
     */
    protected $imageUploadDir;

    /**
     * @var string
     */
    protected $imageUrl;

    /*** Image ***/
    /**
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param File $image
     *
     * @return AbstractImageUpload
     */
    public function setImage(File $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /*** Image path ***/
    /**
     * @return string
     */
    public function getImageUploadPath()
    {
        return $this->imageUploadPath;
    }

    /**
     * @param string $imageUploadPath
     *
     * @return AbstractImageUpload
     */
    public function setImageUploadPath($imageUploadPath)
    {
        $this->imageUploadPath = $imageUploadPath;

        return $this;
    }

    /*** Image upload dir ***/
    /**
     * @return string
     */
    public function getImageUploadDir()
    {
        return $this->imageUploadDir;
    }

    /**
     * @param string $imageUploadDir
     *
     * @return AbstractImageUpload
     */
    public function setImageUploadDir($imageUploadDir)
    {
        $this->imageUploadDir = $imageUploadDir;

        return $this;
    }

    /*** Image URL ***/
    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /*** Image upload ***/
    /**
     * @return AbstractImageUpload
     *
     * @throws \Exception If upload dir and path are not set
     */
    public function imageUpload()
    {
        $image = $this->getImage();

        if (null !== $image) {
            $uploadDir = $this->getImageUploadDir();
            $uploadPath = $this->getImageUploadPath();

            if (!($uploadDir || $uploadPath)) {
                throw new \Exception('You must define the imageUploadDir and the imageUploadPath!');
            }

            $slugify = new Slugify();

            $filename = $slugify->slugify(
                $image->getClientOriginalName()
            );

            $filename .= '_'.sha1(uniqid(mt_rand(), true)).'.'.
                $image->guessExtension()
            ;

            $image->move(
                $uploadDir,
                $filename
            );

            $this->setImageUrl($uploadPath.$filename);

            $this->setImage(null);
        }

        return $this;
    }
}
