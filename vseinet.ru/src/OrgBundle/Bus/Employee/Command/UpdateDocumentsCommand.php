<?php

namespace OrgBundle\Bus\Employee\Command;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use OrgBundle\Bus\Employee\Command\Schema\Document;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateDocumentsCommand extends Message
{
    /**
     * @var int
     *
     * @VIA\Description("Employee Id")
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     */
    public $id;

    /**
     * @var Document[]
     *
     * @VIA\Description("List of documents")
     * @Assert\All({
     *      @Assert\Type(type="OrgBundle\Bus\Employee\Command\Schema\Document")
     * })
     */
    public $documents;


    /**
     * Initial object with values from array.
     *
     * @param array $values
     * @param array $extra
     */
    public function __construct(array $values = [], array $extra = [])
    {
        $data = $this->empty2null($extra + $values);

        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                if ($property == 'documents') {
                    $documents = [];
                    if (is_array($value) && (count($value) > 0)) {
                        if (isset($value[0])) {
                            foreach ($value as $docArr) {
                                $document = new Document();
                                foreach ($docArr as $docKey => $docVal) {
                                    if (property_exists($document, $docKey)) {
                                        if ($docKey == 'dueDate') {
                                            $document->$docKey = ($docVal instanceof \DateTime) ? $docVal : new \DateTime($docVal);
                                        } elseif (substr($docKey, 0, 2) == 'is') {
                                            $document->$docKey = boolval($docVal);
                                        } else {
                                            $document->$docKey = $docVal;
                                        }
                                    }
                                }
                                $documents[] = $document;
                            }
                        } else {
                            $document = new Document();
                            foreach ($value as $docKey => $docVal) {
                                if (property_exists($document, $docKey)) {
                                    if ($docKey == 'dueDate') {
                                        $document->$docKey = ($docVal instanceof \DateTime) ? $docVal : new \DateTime($docVal);
                                    } elseif (substr($docKey, 0, 2) == 'is') {
                                        $document->$docKey = boolval($docVal);
                                    } else {
                                        $document->$docKey = $docVal;
                                    }
                                }
                            }
                            $documents[] = $document;
                        }
                    } else {
                        throw new InvalidArgumentException('Invalid format of command');
                    }
                    $value = $documents;
                }
                $this->$property = $value;
            }
        }
    }
}