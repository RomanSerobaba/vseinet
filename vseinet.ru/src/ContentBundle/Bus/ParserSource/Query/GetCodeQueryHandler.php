<?php 

namespace ContentBundle\Bus\ParserSource\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserSource;
use Symfony\Component\Finder\Finder;
use ContentBundle\Bus\ParserSource\Query\DTO\Code;

/**
 * @deprecated
 */
class GetCodeQueryHandler extends MessageHandler
{
    public function handle(GetCodeQuery $query)
    {
        $source = $this->getDoctrine()->getManager()->getRepository(ParserSource::class)->find($query->id);
        if (!$source instanceof ParserSource) {
            throw new NotFoundHttpException(sprintf('Источник парсинга %s не найден', $query->id));
        }

        $codes = [];
        foreach ($this->getParameter('parsers.types') as $type) {
            $files = new Finder();
            $files->name($source->getFilename())->in($this->getParameter('parser.sources'.'/'.$type));
            if (0 < $files->count()) {
                foreach ($files as $file) {
                    $codes[] = new Code($type, file_get_contents($file->getFilename()));
                    break;
                }
            }   
        }

        return $codes;
    }
}