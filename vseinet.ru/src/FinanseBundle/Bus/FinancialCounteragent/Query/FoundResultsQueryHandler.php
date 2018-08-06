<?php 

namespace FinanseBundle\Bus\FinancialCounteragent\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\ORM\Query\DTORSM;

class FoundResultsQueryHandler extends MessageHandler
{
    public function handle(FoundResultsQuery $query)
    {
        
        if (!$query->withIndividual && !$query->withLegal)
            return null;

        $q = '%'. mb_strtolower($query->q) .'%';
            
        $queryText = "
            select
            
                fc.id,
                case
                    when fc.counteragent_id is null then
                        concat(
                            case
                                when p.firstname is null  then ''
                                else concat(p.firstname,
                                    case when p.secondname is null and p.lastname is null then '' else ' ' end)
                                end,
                            case
                                when p.secondname is null then ''
                                else concat(p.secondname,
                                    case when p.lastname is null  then '' else ' ' end)
                                end,
                            case when p.lastname is null   then '' else p.lastname end)
                    when fc.user_id is null then c.name
                    else ''
                    end as name
                    
            from financial_counteragent fc
            
            left join \"user\" u on fc.user_id = u.id
            left join \"person\" p on p.id = u.person_id

            left join \"counteragent\" c on fc.counteragent_id = c.id

            where
                (
                    lower(p.firstname) like :q or
                    lower(p.secondname) like :q or
                    lower(p.lastname) like :q or
                    lower(c.name) like :q or
                    c.tin like :q
                )
        ";
        
        if (!$query->withIndividual) {
            $queryText .= "
                and fc.user_id is null";
        }
        
        if (!$query->withLegal) {
            $queryText .= "
                and fc.counteragent_id is null";
        }
        
        $queryText .= "
            limit {$query->limit}
        ";
        
        return $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, new DTORSM(DTO\FinancialCounteragentDTO::class, DTORSM::ARRAY_INDEX))                
                ->setParameters(['q' => $q])
                ->getResult('DTOHydrator');

    }

}