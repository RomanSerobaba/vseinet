<?php

namespace ServiceBundle\Components;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Image
{
    const THUMB_WIDTH = 80;
    const THUMB_HEIGHT = 80;

    const REPRESENTATIVE_WIDTH = 450;
    const REPRESENTATIVE_HEIGHT = 338;

    public function resize($contentData, string $filename, int $width, int $height) : \Imagick
    {
        $directory = dirname($filename);
        if (!file_exists($directory) && !@mkdir($directory, 0777, true)) {
            throw new BadRequestHttpException(sprintf('Unable to create directory "%s".', $directory));
        }

        $im = new \Imagick();
        if (false === $im->readimageblob($contentData)) {
            throw new BadRequestHttpException('Unknown image type');
        }

        $imSizeBytes = $im->getimagelength();
        $imSizeMb = $imSizeBytes / pow(1024, 2);
        if ($imSizeMb > 10) {
            throw new BadRequestHttpException('Image is too large ('.$imSizeMb.' Mb)');
        }

        $im = $im->coalesceimages();

        $w = $im->getimagewidth();
        $h = $im->getimageheight();

        if ($w < $width || $h < $height) {
            throw new BadRequestHttpException('Image is too small');
        }

        if ($width === $height) {
            $im = $this->_createThumbnail($im, $width, $height);
        } else {
            $im = $this->_resizeContentImage($im, $width, $height);
        }

        if ($im === false) {
            throw new BadRequestHttpException('Resize image problem');
        }

        if (false === $im->writeimage($filename)) {
            throw new BadRequestHttpException('Save image problem');
        }

        return $im;
    }


    /**
     * Crop image to thumbnail
     *
     * @param \Imagick $image
     * @param          $thumbnailWidth
     * @param          $thumbnailHeight
     *
     * @return \Imagick
     * @throws BadRequestHttpException
     * @internal param $destinationFilename
     */
    private function _createThumbnail(\Imagick $image, $thumbnailWidth, $thumbnailHeight)
    {
        $widthOrig = $image->getimagewidth();
        $heightOrig = $image->getimageheight();

        $ratioOrig = $widthOrig / $heightOrig;

        if ($widthOrig > $thumbnailWidth || $heightOrig > $thumbnailHeight) {
            if ($thumbnailWidth / $thumbnailHeight > $ratioOrig) {
                $thumbnailWidth = $thumbnailHeight * $ratioOrig;
            } else {
                $thumbnailHeight = $thumbnailWidth / $ratioOrig;
            }

            if (!$image->resizeimage($thumbnailWidth, $thumbnailHeight, \Imagick::FILTER_LANCZOS, 1)) {
                throw new BadRequestHttpException('Resize picture problem');
            }
        }

        return $image;
    }

    /**
     * @param \Imagick $image
     * @param          $width
     * @param          $height
     *
     * @return \Imagick
     * @throws BadRequestHttpException
     */
    private function _createThumbnailCutSquare(\Imagick $image, int $width, int $height) : \Imagick
    {
        $w = $image->getimagewidth();
        $h = $image->getimageheight();

        if ($w > $width || $h > $height) {
            $newW = $newH = $width;

            if ($h >= $w) {
                $dstW = $w;
                $dstH = $w;
                $dstX = 0;
            } else {
                $dstW = $h;
                $dstH = $h;
                $dstX = round(($w - $h) / 2);
            }

            $this->_cropImage($image, $dstW, $dstH, $dstX, 0);
            if (!$image->resizeimage($newW, $newH, \Imagick::FILTER_LANCZOS, 1)) {
                throw new BadRequestHttpException('Resize picture problem');
            }
        }

        return $image;
    }

    /**
     * @param \Imagick $image
     * @param int      $resizeWidth
     * @param int      $resizeHeight
     *
     * @return \Imagick
     */
    private function _resizeContentImage(\Imagick $image, int $resizeWidth, int $resizeHeight) : \Imagick
    {
        $newWidth = $maxWidth = $resizeWidth;
        $newHeight = $maxHeight = $resizeHeight;

        $imgWidth = $image->getImageWidth();
        $imgHeight = $image->getImageHeight();

        $x = $y = 0;
        if (($imgWidth / $imgHeight) >= ($maxWidth / $maxHeight)) {
            $newWidth = 0;
            $x = ($imgWidth / ($imgHeight / $maxHeight) - $maxWidth) / 2;
        } else {
            $newHeight = 0;
            $y = ($imgHeight / ($imgWidth / $maxWidth) - $maxHeight) / 2;
        }

        $image->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1);
        $image->cropImage($maxWidth, $maxHeight, $x, $y);
        $image->setImagePage($maxWidth, $maxHeight, 0, 0);

        return $image;
    }

    /**
     * @param \Imagick $image
     * @param          $dimension
     *
     * @return \Imagick
     * @throws BadRequestHttpException
     */
    private function _createThumbnailExpanded(\Imagick $image, $dimension)
    {
        $w = $image->getimagewidth();
        $h = $image->getimageheight();
        if ($w >= $dimension || $h >= $dimension) {
            return $image;
        }

        $aspectRatio = $w / $h;
        $rectW = $rectH = $dimension;

        // calculate new size
        $aspectRatioRect = $rectW / $rectH;
        if ($aspectRatio > $aspectRatioRect) {
            $newW = $rectW;
            $newH = (int)round(($newW * $h) / $w);
        } else {
            $newH = $rectH;
            $newW = (int)round(($newH * $w) / $h);
        }

        if (!$image->resizeimage($newW, $newH, \Imagick::FILTER_LANCZOS, 1)) {
            throw new BadRequestHttpException('Resize picture problem');
        }

        return $image;
    }

    /**
     * @param \Imagick $im
     * @param          $w
     * @param          $h
     * @param          $x
     * @param          $y
     */
    private function _cropImage(\Imagick $im, $w, $h, $x, $y)
    {
        $im->cropimage($w, $h, $x, $y);

        $geo = $im->getimagegeometry();
        $im->setimagepage($geo['width'], $geo['height'], 0, 0);
    }
}
