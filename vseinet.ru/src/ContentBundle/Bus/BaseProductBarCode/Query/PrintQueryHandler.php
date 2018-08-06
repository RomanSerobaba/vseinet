<?php 

namespace ContentBundle\Bus\BaseProductBarCode\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\BaseProductBarCode;

class PrintQueryHandler extends MessageHandler
{
    public function handle(PrintQuery $query)
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $item = $em->getRepository(BaseProductBarCode::class)->find($query->id);
        if (!$item instanceof BaseProductBarCode) {
            throw new NotFoundHttpException('Штрихкод не найден.');
        }

        $cacheDir = $this->storeDir = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_FILENAME']), 0, -2)). '/var/barCodesCache';
        
        $fileName = $cacheDir .'/'. $query->id .'.'. $query->formatImage;
        
        if (!file_exists($fileName)) {
            
            switch ($query->formatImage) {
                case 'jpg':
                    $generator = new \Picqer\Barcode\BarcodeGeneratorJPG();
                    break;

                case 'png':
                    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                    break;

                case 'svg':
                    $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
                    break;
            }

            switch ($item->getBarCodeType()) {
                case 'EAN-13':
                    file_put_contents($fileName, $generator->getBarcode($item->getBarCode(), $generator::TYPE_EAN_13, 1, 20));
                    break;

//                case 'EAN-13+2':
//                    $barCodeParts = explode(' ', $item->barCode);
//                    $barCodeType = $generator::TYPE_EAN_2;
//                    break;
//
//                case 'EAN-13+5':
//                    $barCodeType = $generator::TYPE_EAN_5;
//                    break;

                case 'EAN-8':
                    $barCodeType = $generator::TYPE_EAN_8;
                    file_put_contents($fileName, $generator->getBarcode($item->getBarCode(), $generator::TYPE_EAN_8, 1, 20));
                    break;

//                case 'EAN-8+2':
//                    $barCodeType = $generator::TYPE_EAN_2;
//                    break;
//
//                case 'EAN-8+5':
//                    $barCodeType = $generator::TYPE_EAN_5;
//                    break;

                case 'code 128':
                    file_put_contents($fileName, $generator->getBarcode($item->getBarCode(), $generator::TYPE_CODE_128, 1, 20));
                    break;
                
                default:
                    throw new BadRequestHttpException('Поддержка формата ШК не реализована.');
                    break;
            }

        }

        return($fileName);
        
    }

}