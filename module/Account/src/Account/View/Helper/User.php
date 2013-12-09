<?php
/**
 * User: Josh
 * Date: 12/9/2013
 * Time: 7:14 μμ
 */

namespace Account\View\Helper;

use Zend\View\Helper\AbstractHelper;

class User extends AbstractHelper{

    private $serviceManager;

    public function __invoke(){
        $auth = $this->getServiceManager()->get('auth_service');
        if($auth->hasIdentity()){
            $em = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
            return $em->getRepository('Account\Entity\Account')->find($auth->getIdentity()->getAccountId());
        }
        return null;

    }

    public function setServiceManager($sm){
        $this->serviceManager = $sm;
    }

    public function getServiceManager(){
        return $this->serviceManager;
    }
}