<?php
namespace User\Controller;

use Zend\View\Model\JsonModel;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;

class UserController extends AbstractActionController
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Zend\Form\Form
     */
    private $detailsForm;

    /**
     * @var \Zend\Form\Form
     */
    private $socialsForm;

    public function profileAction(){
		return new ViewModel();
	}

	public function detailsAction()
	{
        $form = $this->getDetailsForm();
        $user = $this->identity();
        $form->bind($user);
		return new ViewModel(array(
            'form' =>   $form,
            'bodyClass' => 'userPage'
        ));
	}

	public function socialsAction(){
        $form = $this->getSocialsForm();
        $user = $this->identity();
        $form->bind($user);



        return new ViewModel(array(
            'form' => $form,
            'bodyClass' => 'userPage'
        ));
	}

	public function gamesAction(){
        $user = $this->getEntityManager()->getRepository('\Account\Entity\Account')->find($this->identity()->getId());
        $games = $user->getGames();

        return new ViewModel(array(
            'games' => $games,
            'bodyClass' => 'userPage'
        ));
	}

	public function followAction(){
		return new JsonModel();
	}

	public function unfollowAction(){
		return new JsonModel();
	}

	public function followingAction(){
        $user = $this->getEntityManager()->getRepository('\Account\Entity\Account')->find($this->identity()->getId());
        $following = $user->getFollowing();
        return new ViewModel(array(
            'following' => $following,
            'bodyClass' => 'userPage'
        ));
	}

	public function feedsAction(){
        return new ViewModel(array(
            'bodyClass' => 'userPage'
        ));
	}

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        if (!$this -> entityManager) {
            $this -> setEntityManager($this -> getServiceLocator() -> get('Doctrine\ORM\EntityManager'));
        }
        return $this -> entityManager;
    }

    public function setEntityManager($em) {
        $this -> entityManager = $em;
    }

    public function getSocialsForm(){
        if(!$this->socialsForm){
            $this->setSocialsForm($this->getServiceLocator()->get('user_socials_form'));
        }
        return $this->socialsForm;
    }

    public function setSocialsForm($socialsForm){
        $this->socialsForm = $socialsForm;
    }

    public function getDetailsForm(){
        if(!$this->detailsForm){
            $this->setDetailsForm($this->getServiceLocator()->get('user_details_form'));
        }
        return $this->detailsForm;
    }

    public function setDetailsForm($detailsForm){
        $this->detailsForm = $detailsForm;
    }
}
