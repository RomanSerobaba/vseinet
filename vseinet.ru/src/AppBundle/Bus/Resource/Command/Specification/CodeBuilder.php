<?php 

namespace AppBundle\Bus\Resource\Command\Specification;

class CodeBuilder
{
    static public function build($path)
    {
        $parts = explode('/', trim($path, '/'));

        array_walk($parts, function(&$part) {
            $part = preg_replace('/(?<!^)[A-Z]/', '_$0', $part);
            if (substr($part, 0, 1) == ':') {
                $part = 'BY_'.trim($part, ':');
            }
        });
        
        return strtoupper(implode('_', $parts));
    }
}