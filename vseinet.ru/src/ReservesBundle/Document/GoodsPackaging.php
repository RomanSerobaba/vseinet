<?php

namespace ReservesBundle\Document;

use ReservesBundle\Bus\GoodsPackaging\Command;
use AppBundle\Container\ContainerAware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
    
class GoodsPackaging extends ContainerAware
{
    
    public function create(Command\CreateCommand $command)
    {
        
        $conn = $this->getDoctrine()->getManager()->getConnection();
        $conn->beginTransaction();
        try{
            
            $dId = $this->_create($command);
            $this->_registration($dId);
            $conn->commit();
        
        } catch (Exception $ex) {
            
            $conn->rollBack();
            throw $e;
            
        }
        
        return $dId;
        
    }
    
    public function delete(int $dId)
    {
        // Проверки, общие для всех документов
        $rootDoc = $this->_getRootDoc($dId);
        if (empty($rootDoc)) throw new NotFoundHttpException('Документ не найден');
        if (!empty($rootDoc['completed'])) throw new ConflictHttpException('Изменение завершенного документа невозможно.');
        
        // Вызов команд, специфичных для данного вида доумента
        $conn = $this->getDoctrine()->getManager()->getConnection();
        $conn->beginTransaction();
        try{
            
            $this->_unRegistration($dId);
            $this->_delete($dId);
            $conn->commit();
        
        } catch (Exception $ex) {
            
            $conn->rollBack();
            throw $e;
            
        }
        
    }
    
    public function update(Command\UpdateCommand $command)
    {
        
        // Проверки, общие для всех документов
        $rootDoc = $this->_getRootDoc($command->id);
        if (empty($rootDoc)) throw new NotFoundHttpException('Документ не найден');
        if (!empty($rootDoc['completed'])) throw new ConflictHttpException('Изменение завершенного документа невозможно.');
        
        // Вызов команд, специфичных для данного вида доумента
        $conn = $this->getDoctrine()->getManager()->getConnection();
        $conn->beginTransaction();
        try{
            
            $this->_unRegistration($command->id);
            $this->_update($command);
            $this->_registration($command->id);
            $conn->commit();
        
        } catch (Exception $ex) {
            
            $conn->rollBack();
            throw $e;
            
        }

    }
    
    public function setCompleted(int $dId, bool $value)
    {

        // Проверки, общие для всех документов
        $rootDoc = $this->_getRootDoc($dId);
        if (empty($rootDoc)) throw new NotFoundHttpException('Документ не найден');
        
        // Вызов команд, специфичных для данного вида доумента
        $conn = $this->getDoctrine()->getManager()->getConnection();
        $conn->beginTransaction();
        try{
            
            $this->_unRegistration($dId);
            $this->_isCompleted($dId, $value);
            $this->_registration($dId);
            $conn->commit();
        
        } catch (Exception $ex) {
            
            $conn->rollBack();
            throw $e;
            
        }

    }
    
    public function exists(int $dId): bool
    {

        $rootDoc = $this->_getRootDoc($dId);
        
        return (!empty($rootDoc));

    }
    
    public function getCompleted(int $dId): bool
    {

        $rootDoc = $this->_getRootDoc($dId);
        if (empty($rootDoc)) { throw new NotFoundHttpException('Документ не найден'); }
        
        return (!empty($rootDoc->completedAt));

    }
    
    public function childFree(int $dId): bool
    {

        $queryText = "
            select
                count(1) as cnt
            from any_doc
            where
                parent_doc_did = :dId
            ";
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('cnt', 'cnt', 'integer');

        $result = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, $rsm)
                ->setParameters(['dId' => $dId])->getResult();

        return (0 == $result[0]['cnt']);
        
    }
    
    //////////////////////////////////////////////////////////////////////
    
    protected function _getRootDoc(int $dId)
    {

        $queryText = "
            select
                ad.did,
                ad.created_at,
                ad.created_by,
                ad.completed_at,
                ad.completed_by,
                ad.registered_at,
                ad.registered_by,
                ad.parent_doc_did,
                ad.title
            from any_doc ad
            where
                ad.did = :dId
            ";
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('did', 'dId', 'integer');
        $rsm->addScalarResult('created_at', 'createdAt', 'datetime');
        $rsm->addScalarResult('created_by', 'createdBy', 'integer');
        $rsm->addScalarResult('completed_at', 'completedAt', 'datetime');
        $rsm->addScalarResult('completed_by', 'completedBy', 'integer');
        $rsm->addScalarResult('parent_doc_did', 'parentDocDId', 'integer');
        $rsm->addScalarResult('title', 'title', 'string');

        $result = $this->getDoctrine()->getManager()
                ->createNativeQuery($queryText, $rsm)
                ->setParameters(['dId' => $dId,])
                ->getResult();

        if (empty($result)) {
            $toRet = NULL;
        }else{
            $toRet = $result[0];
        }
        
        return $toRet;
        
    }
    
    
    //////////////////////////////////////////////////////////////////////
    //
    //  Вызов команд
    //

    protected function _create(Command\CreateCommand $command)
    {

        $uuid = $this->get('uuid.manager')->generate();        
        $command->uuid = $uuid;
        
        $this->get('command_bus')->handle($command);

        return $this->get('uuid.manager')->loadId($uuid);

    }
    
    protected function _update(Command\UpdateCommand $command)
    {
        $this->get('command_bus')->handle($command);
    }
    
    protected function _delete(int $dId)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $dId]));
    }
    
    protected function _unRegistration(int $dId)
    {
//        $this->get('command_bus')->handle(new Command\UnRegistrationCommand(['id' => $dId]));
    }
    
    protected function _registration(int $dId)
    {
//        $this->get('command_bus')->handle(new Command\RegistrationCommand(['id' => $dId]));
    }
    
    protected function _isCompleted(int $dId, bool $value)
    {
        $this->get('command_bus')->handle(new Command\CompletedCommand([
            'id' => $dId,
            'completed' => $value,
        ]));
    }
    
}
