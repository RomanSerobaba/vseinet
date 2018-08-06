<?php 

namespace AppBundle\SphinxQL;

class SphinxQL
{
    /**
     * @var mysqli
     */
    protected $driver;

    /**
     * @var string
     */
    protected $host;

    /**
     * @param integer
     */
    protected $port;
    
    /**
     * An array of escaped characters for escapeMatch()
     * @var array
     */
    protected $escapeChars = [
        '\\' => '\\\\',
        '(' => '\(',
        ')' => '\)',
        '|' => '\|',
        '-' => '\-',
        '!' => '\!',
        '@' => '\@',
        '~' => '\~',
        '"' => '\"',
        '&' => '\&',
        '/' => '\/',
        '^' => '\^',
        '$' => '\$',
        '=' => '\=',
        '<' => '\<',
        "'" => "\'",
    ];


    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function connect()
    {
        if (null === $this->driver) {
            $this->driver = new \mysqli($this->host, null, null, null, $this->port);
            if ($this->driver->connect_error) {
                throw new \RuntimeException(sprintf('SphinxQL connection error: %s.', $this->driver->connect_error));
            }
        }
    }

    public function disconnect()
    {
        if (null !== $this->driver) {
            $this->driver->close();
            $this->driver = null;
        }
    }

    public function getDriver()
    {
        if (null === $this->driver) {
            $this->connect();
        }

        return $this->driver;
    }

    public function execute($sql)
    {
        if (!$this->getDriver()->multi_query($sql)) {
            throw new \RuntimeException(sprintf('SphinxQL error: %s.', $this->driver->error)); 
        }

        $results = [];

        $index = 0;
        do {
            if ($result = $this->getDriver()->store_result()) {
                $results[$index] = [];
                while ($row = $result->fetch_assoc()) {
                    $results[$index][] = $row;      
                }
                $result->free();        
            }
            $index++;
        } while ($this->getDriver()->more_results() && $this->getDriver()->next_result());

        return $results;      
    }

    public function escape($str) 
    {
        return $this->getDriver()->real_escape_string($str);
    }

    public function escapeMatch($str) 
    {
        return $this->escape(str_replace(array_keys($this->escapeChars), array_values($this->escapeChars), mb_strtolower($str, 'UTF-8')));
    }

    public function snippet($str)
    {
        return str_replace(array_keys($this->escapeChars), ' ', mb_strtolower($str, 'UTF-8'));
    }

    public function fetchAssoc($result, $key = 'id')
    {
        $items = [];
        foreach ($result as $item) {
            $items[$item[$key]] = $item;
        } 

        return $items;
    }
}