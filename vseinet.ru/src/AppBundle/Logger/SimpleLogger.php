<?php 

namespace AppBundle\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SimpleLogger extends Logger 
{
    protected $dir;

    public function setDir($dir)
    {
        $this->dir = $dir;
    }

    public function setName($name, $dir = null)
    {
        $this->name = $name;
        $this->handlers = [];
        $this->pushHandler(new StreamHandler($this->dir.'/'.($dir ?: $name).'/'.$name.'-'.date('Y-m-d').'.log', Logger::INFO));

        return $this;
    }
}