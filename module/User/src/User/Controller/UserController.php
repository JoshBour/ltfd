<?php
namespace User\Controller;

use Symfony\Component\Console\Application;
use Zend\View\Model\JsonModel;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DoctrineModule\Paginator\Adapter\Collection as CollectionAdapter;
use Zend\Paginator\Paginator;

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
     * @var \Zend\Form\Form
     */
    private $commentForm;

    /**
     * @var \Zend\I18n\Translator
     */
    private $translator;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $accountRepository;

    public function profileAction()
    {
        return new ViewModel();
    }

    public function detailsAction()
    {
        $form = $this->getDetailsForm();
        $em = $this->getEntityManager();
        $user = $this->user();
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
                        $simpleImage->createThumbnail(32, PUBLIC_PATH . '/images/users/' . $user->getId() . '/user-default-16x16.' . $extension, 32);
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
            'form' => $form,
            'bodyClass' => 'userPage'
        ));
    }

    public function socialsAction()
    {
        $form = $this->getSocialsForm();
        $form->bind($this->identity());
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
        $games = $this->user()->getGames();

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
        $following = $this->user()->getFollowing();
        return new ViewModel(array(
            'following' => $following,
            'bodyClass' => 'userPage'
        ));
    }

    public function feedsAction()
    {
        $categories = array('posted','favorites','history','liked');
        $activeCategory = $this->params()->fromRoute('category','posted');
        $em = $this->getEntityManager();
        $feeds = array();
        switch($activeCategory){
            case 'liked':
                $feeds = $em->getRepository('Feed\Entity\Feed')->findRatedFeeds($this->identity()->getId(),1);
                break;
            case 'posted':
                $feeds = $this->user()->getFeeds();
                $adapter = new CollectionAdapter($feeds);
                $feeds = new Paginator($adapter);
                $feeds->setItemCountPerPage(10)
                          ->setCurrentPageNumber(1);
                break;
            default:
                $feeds = $em->getRepository('Feed\Entity\Feed')->findFeedsByCategory($activeCategory,$this->identity()->getId());
        }

        return new ViewModel(array(
            'bodyClass' => 'userPage',
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'feeds' => $feeds,
            'form' => $this->getCommentForm()
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

    /**
     * @var \Zend\I18n\Translator
     */
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

    /**
     * @return \Zend\Form\Form
     */
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

    /**
     * @return \Zend\Form\Form
     */
    public function getCommentForm()
    {
        if (!$this->commentForm) {
            $this->setCommentForm($this->getServiceLocator()->get('comment_form'));
        }
        return $this->commentForm;
    }

    public function setCommentForm($commentForm)
    {
        $this->commentForm = $commentForm;
    }

    /**
     * @return \Zend\Form\Form
     */
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

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getAccountRepository(){
        if(!$this->accountRepository){
            $this->setAccountRepository($this->getEntityManager()->getRepository('Account\Entity\Account'));
        }
        return $this->accountRepository;
    }

    public function setAccountRepository($accountRepository){
        $this->accountRepository = $accountRepository;
    }
}
