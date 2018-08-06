<?php 

namespace ContentBundle\Bus\ColorComposite\Query;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Entity\ColorComposite;
use ContentBundle\Bus\ColorComposite\Query\DTO\Schema;

class GetSchemasQueryHandler extends MessageHandler
{
    public function handle(GetSchemasQuery $query)
    {
        $schemas = $this->getDoctrine()->getManager()->getRepository(ColorComposite::class)->getSchemas();

        return array_map(function($schema) {
            return new Schema($schema['type'], $schema['name']);
        }, $schemas);
    }
}