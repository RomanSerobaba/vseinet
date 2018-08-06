<?php 

namespace ShopBundle\Bus\BannerTemplate\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadCommandHandler extends MessageHandler
{
    public function handle(UploadCommand $command)
    {
        if (!$command->file instanceof UploadedFile) {
            throw new BadRequestHttpException('Файл изображения не загружен');
        }

        if (!file_exists($this->getParameter('banner.images.path'))) {
            mkdir($this->getParameter('banner.images.path'), 0777, true);
        }

        $command->file->move($this->getParameter('banner.images.path'), $command->filename);
    }
}