<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    public function homeAction()
    {
        $viewModel = new ViewModel();
        if($user = $this->identity()){
           # $user = $this->getEntityManager()->getRepository('Account\Entity\Account')->find($user->getId());
            $viewModel->setTemplate('application/home.phtml');
        }else{
            $viewModel->setTemplate('application/unregistered.phtml');
        }

        return new ViewModel(array(
            'user' => $user
        ));
    }

    public function aboutAction()
    {
        return new ViewModel();
    }

    public function faqAction()
    {
        return new ViewModel();
    }

    public function tosAction()
    {
        return new ViewModel();
    }

    public function teamAction()
    {
        return new ViewModel();
    }

    public function contactAction()
    {
        return new ViewModel();
    }

    public function getEntityManager()
    {
        if (!$this->entityManager) {
            $this->setEntityManager($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
        }
        return $this->entityManager;
    }

    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }

    public function getTranslator()
    {
        if (!$this->translator) {
            $this->setTranslator($this->getServiceLocator()->get('translator'));
        }
        return $this->translator;
    }

    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }


}
