<?php
namespace Feed\Controller;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;

class FeedController extends AbstractActionController
{
    public function profileAction()
    {
        return new ViewModel();
    }

}
