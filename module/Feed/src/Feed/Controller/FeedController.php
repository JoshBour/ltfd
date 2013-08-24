<?php
namespace Feed\Controller;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;

class FeedController extends AbstractActionController
{
    public function viewAction()
    {
        return new ViewModel();
    }

    public function newAction()
    {
        return new ViewModel();
    }

    public function deleteAction()
    {
        return new ViewModel();
    }

    public function rateAction()
    {
        return new JsonModel();
    }

    public function reportAction()
    {
        return new JsonModel();
    }

}
