<?php
namespace User\Controller;

use Symfony\Component\Console\Application;
use Zend\View\Model\JsonModel;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;

use Application\Entity\ImageHelper;

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

    /**
     * @var \Zend\I18n\Translator
     */
    private $translator;

    public function profileAction()
    {
        return new ViewModel();
    }

    public function detailsAction()
    {
        $form = $this->getDetailsForm();
        $em = $this->getEntityManager();
        $user = $em->find('Account\Entity\Account',$this->identity()->getId());
        $form->bind($user);
        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $validationGroup = $user->getUpdateValidationGroup($post);
            if (!empty($validationGroup)) {
                $form->setValidationGroup(array('security', 'account' => $validationGroup));
                $form->setData($post);
                if ($form->isValid()) {
                    if (in_array('avatar', $validationGroup)) {
                        $extension = ImageHelper::getImageExtension($post['account']['avatar']['type']);
                        $filter = new \Zend\Filter\File\Rename(array(
                            'target' => PUBLIC_PATH . '/images/users/' . $user->getId() . '/user-default.' . $extension,
                            'overwrite' => true
                        ));
                        $filter->filter($post['account']['avatar']);
                        chmod(PUBLIC_PATH . '/images/users/' . $user->getId() . '/user-default.' . $extension, 0644);

                        // create small avatars of the image
                        $simpleImage = new \Application\Entity\SimpleImage();
                        $simpleImage->load(PUBLIC_PATH . '/images/users/' . $user->getId() . '/user-default.' . $extension);
                        $simpleImage->createThumbnail(200, PUBLIC_PATH . '/images/users/' . $user->getId() . '/user-default-200x200.' . $extension, 200);
                        $simpleImage->createThumbnail(65, PUBLIC_PATH . '/images/users/' . $user->getId() . '/user-default-65x65.' . $extension, 65);
                        $user->setAvatar('user-default.' . $extension);
                    }
                    if(in_array('password',$validationGroup)){
                        $user->setPassword(\Account\Entity\Account::getHashedPassword($post['account']['password']));
                    }
                    $em->persist($user);
                    $em->flush();
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Your account details have been updated successfully.'));
                    return $this->redirect()->toRoute('user/details');
                }
            }else{
                $this->flashMessenger()->addMessage($this->getTranslator()->translate('There is nothing new to update.'));
                return $this->redirect()->toRoute('user/details');
            }
        }

        return new ViewModel(array(
            'user' => $user,
            'form' => $form,
            'bodyClass' => 'userPage'
        ));
    }

    public function socialsAction()
    {
        $form = $this->getSocialsForm();
        $user = $this->identity();
        $form->bind($user);
        $request = $this->getRequest();

        if($request->isPost()){
            $data = $request->getPost();
        }

        return new ViewModel(array(
            'form' => $form,
            'bodyClass' => 'userPage'
        ));
    }

    public function gamesAction()
    {
        $user = $this->getEntityManager()->getRepository('\Account\Entity\Account')->find($this->identity()->getId());
        $games = $user->getGames();

        return new ViewModel(array(
            'games' => $games,
            'bodyClass' => 'userPage'
        ));
    }

    public function followAction()
    {
        return new JsonModel();
    }

    public function unfollowAction()
    {
        return new JsonModel();
    }

    public function followingAction()
    {
        $user = $this->getEntityManager()->getRepository('\Account\Entity\Account')->find($this->identity()->getId());
        $following = $user->getFollowing();
        return new ViewModel(array(
            'following' => $following,
            'bodyClass' => 'userPage'
        ));
    }

    public function feedsAction()
    {
        return new ViewModel(array(
            'bodyClass' => 'userPage'
        ));
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
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

    public function getSocialsForm()
    {
        if (!$this->socialsForm) {
            $this->setSocialsForm($this->getServiceLocator()->get('user_socials_form'));
        }
        return $this->socialsForm;
    }

    public function setSocialsForm($socialsForm)
    {
        $this->socialsForm = $socialsForm;
    }

    public function getDetailsForm()
    {
        if (!$this->detailsForm) {
            $this->setDetailsForm($this->getServiceLocator()->get('user_details_form'));
        }
        return $this->detailsForm;
    }

    public function setDetailsForm($detailsForm)
    {
        $this->detailsForm = $detailsForm;
    }
}
