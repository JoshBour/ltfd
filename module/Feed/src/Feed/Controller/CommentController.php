<?php
namespace Feed\Controller;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;

class CommentController extends AbstractRestfulController
{
    public function addAction()
    {
        return new ViewModel();
    }

    public function create()

}
