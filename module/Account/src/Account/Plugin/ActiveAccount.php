<?php
/**
 * User: Josh
 * Date: 12/9/2013
 * Time: 6:13 μμ
 */

namespace Account\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManager;

class ActiveAccount extends AbstractPlugin{

    private $serviceManager;

    public function __invoke(){
        $em = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        $auth = $this->getServiceManager()->get('auth_service')->getIdentity()->getId();
        return $em->getRepository('Account\Entity\Account')->find($auth);

    }

    public function setServiceManager($sm){
        $this->serviceManager = $sm;
    }

    public function getServiceManager(){
        return $this->serviceManager;
    }
}