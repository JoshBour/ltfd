<?php
namespace Feed\Controller;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Authentication\AuthenticationService;

class CommentController extends AbstractActionController
{
    public function addAction()
    {
        return new ViewModel();
    }

    public function deleteAction()
    {
        return new JsonModel();
    }

    public function editAction()
    {
        return new JsonModel();
    }

    public function reportAction(){
        return new JsonModel();
    }

    public function listAction(){
        return new ViewModel();
    }

}
