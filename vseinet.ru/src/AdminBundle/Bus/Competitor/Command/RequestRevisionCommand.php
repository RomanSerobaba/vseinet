<?

namespace AdminBundle\Bus\Competitor\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class RequestRevisionCommand extends Message 
{
    /**
     * @Assert\Type(type="numeric")
     */
    public $id;
}
