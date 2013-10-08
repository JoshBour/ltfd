<?php
namespace Feed\Controller;

use Feed\Entity\Feed;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use \Doctrine\ORM\Tools\Pagination\Paginator;

class FeedController extends AbstractActionController
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $feedRepository;

    /**
     * @var \Zend\Form\Form
     */
    private $feedForm;

    public function viewAction()
    {
        return new ViewModel();
    }

    public function newAction()
    {
        $request = $this->getRequest();
        $entity = new Feed();
        $form = $this->getFeedForm();
        if($request->isPost()){
            $data = $request->getPost();
            $form->bind($entity);
            $form->setData($data);
            if($form->isValid()){
                $entity = Feed::create($entity,$this->user(),$data['feed']['video']);
                $em = $this->getEntityManager();
                try{
                    $em->persist($entity);
                    $em->flush();

                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('Your feed has been saved successfully!'));
                    $this->redirect()->toRoute('user/feeds');

                }catch(Exception $e){
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate('There was an error when saving the feed: ') . $e->getMessage());
                }
            }else{
                // the form was not valid
            }
        }
        return new ViewModel(array(
            'form' => $form,
            'bodyClass' => 'feedPage'
        ));
    }

    public function deleteAction()
    {
        return new ViewModel();
    }

    public function rateAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($user = $this->user()) {
                $em = $this->getEntityManager();
                $feed = $this->getFeedRepository()->find($this->params('id'));
                if ($this->params('rating') == 'up') {
                    $rating = 1;
                    $otherRating = 0;
                } else {
                    $rating = 0;
                    $otherRating = 1;
                }
                $rateEntity = $em->getRepository('Feed\Entity\Rating')->findOneBy(array('user' => $user->getId(), 'feed' => $feed->getId()));
                $success = 0;
                $message = '';
                try {
                    if ($rateEntity) {
                        // check if the rating happens to be the same with the existing one, if so exit
                        if ($rateEntity->getRating() != $rating) {
                            $rateEntity->setRating($rating);
                        }
                        $newRating = ($rating > 0) ? 2 : -2;
                    } else {
                        $rateEntity = new \Feed\Entity\Rating($user, $feed, $rating);
                        $newRating = ($rating > 0) ? 1 : -1;
                    }
                    $feed->setRating($feed->getRating() + $newRating);
                    $em->persist($rateEntity);
                    $em->persist($feed);
                    $em->flush();
                    $success = 1;
                } catch (Exception $e) {
                    $message = $e->getMessage();
                }
                return new JsonModel(array('success' => $success, 'message' => $message, 'newRatingTotal' => $feed->getRating()));
            } else {
                return $this->redirect()->toRoute('login');
            }
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function addToUserFeedCategoryAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $success = 0;
            $message = '';
            if ($this->identity()) {
                $feed = $this->getFeedRepository()->find($this->params()->fromPost('feed'));
                if ($feed) {
                    $user = $this->user();
                    $em = $this->getEntityManager();
                    $action = $this->params()->fromPost('defAction', null);
                    $category = $this->params()->fromPost('category');

                    try {
                        if ($action && $action == 'unfavorite') {
                            $categorizedFeed = $em->getRepository('Account\Entity\AccountsFeeds')->findOneBy(array('account' => $user->getId(), 'feed' => $feed->getId(), 'category' => $category));
                            $em->remove($categorizedFeed);
                        } else {
                            $categorizedFeed = new \Account\Entity\AccountsFeeds($user, $feed, $category);
                            $em->persist($categorizedFeed);
                        }
                        $em->flush();
                        $success = 1;
                    } catch (Exception $e) {
                        $message = $e->getMessage();
                    }
                } else {
                    $message = $this->translator->translate('There was an error when storing the feed.');
                }
            } else {
                $message = $this->translator()->translate('You must be logged in to save the video to your history.');
            }
            return new JsonModel(array('message' => $message, 'success' => $success));
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function reportAction()
    {
        return new JsonModel();
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
     * @return \Zend\I18n\Translator\Translator
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
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getFeedRepository()
    {
        if (!$this->feedRepository) {
            $this->setFeedRepository($this->getEntityManager()->getRepository('Feed\Entity\Feed'));
        }
        return $this->feedRepository;
    }

    public function setFeedRepository($feedRepository)
    {
        $this->feedRepository = $feedRepository;
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getFeedForm(){
        if(!$this->feedForm){
            $this->setFeedForm($this->getServiceLocator()->get('feed_form'));
        }
        return $this->feedForm;
    }

    public function setFeedForm($feedForm){
        $this->feedForm = $feedForm;
    }
}
