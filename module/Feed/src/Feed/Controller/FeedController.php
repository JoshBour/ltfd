<?php
namespace Feed\Controller;

use Feed\Entity\Feed;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class FeedController extends AbstractActionController
{
    const ROUTE_USER_FEEDS = 'user/feeds';
    const ROUTE_LOGIN = 'login';

    const MESSAGE_RATE_SUCCESS = 'The rating has been saved successfully.';
    const MESSAGE_RATE_FAIL = 'Something went wrong when saving the rating, please try again.';
    const MESSAGE_FEED_POST_SUCCESS = 'The feed has been posted successfully.';
    const MESSAGE_FEED_POST_FAIL = 'Something went wrong when saving the post.';
    const MESSAGE_FAVORITE_SUCCESS = 'The feed has been added to your favorites.';
    const MESSAGE_UNFAVORITE_SUCCESS = 'The feed has been removed from your favorites.';
    const MESSAGE_FAVORITE_FAIL = 'Something went wrong when saving the feed as favorite, please try again';
    const MESSAGE_REMOVE_SUCCESS = 'The feed has been removed successfully.';
    const MESSAGE_REMOVE_FAIL = 'Something went wrong when removing the feed, please try again';

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

    /**
     * @var \Feed\Service\Feed
     */
    private $feedService;

    public function viewAction()
    {
        return new ViewModel();
    }

    public function newAction()
    {
        $request = $this->getRequest();
        $form = $this->getFeedForm();
        if ($request->isPost()) {
            $data = $request->getPost();
            $feed = $this->getFeedService()->create($data);
            if(!$feed || $feed instanceof Form){
                $this->flashMessenger()->addMessage($this->getTranslator()->translate(self::MESSAGE_FEED_POST_FAIL));
                if($feed instanceof Form) $form = $feed;
            }else{
                $this->flashMessenger()->addMessage($this->getTranslator()->translate(self::MESSAGE_FEED_POST_SUCCESS));
                $this->redirect()->toRoute(self::ROUTE_USER_FEEDS);
            }
        }
        return new ViewModel(array(
            'form' => $form,
            'bodyClass' => 'feedPage'
        ));
    }

    public function removeAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->identity()) {
                $id = $this->params()->fromPost('feedId');
                $feed = $this->getFeedService()->remove($id);

                if ($feed) {
                    $success = 1;
                    $message = $this->getTranslator()->translate(self::MESSAGE_REMOVE_SUCCESS);
                } else {
                    $success = 0;
                    $message = $this->getTranslator()->translate(self::MESSAGE_REMOVE_FAIL);
                }
                return new JsonModel(array(
                        'success' => $success,
                        'message' => $message,
                    )
                );
            } else {
                return $this->redirect()->toRoute(self::ROUTE_LOGIN);
            }
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function rateAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->identity()) {
                $id = $this->params()->fromPost('feedId');
                $type = $this->params()->fromPost('type');
                $feed = $this->getFeedService()->rate($id, $type);

                if ($feed) {
                    $success = 1;
                    $message = $this->getTranslator()->translate(self::MESSAGE_RATE_SUCCESS);
                } else {
                    $success = 0;
                    $message = $this->getTranslator()->translate(self::MESSAGE_RATE_FAIL);
                }
                return new JsonModel(array(
                        'success' => $success,
                        'message' => $message,
                    )
                );
            } else {
                return $this->redirect()->toRoute(self::ROUTE_LOGIN);
            }
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function addToWatchedAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->identity()) {
                $feedId = $this->params()->fromPost('feedId');

                $feed = $this->getFeedService()->addWatched($feedId);
                $success = ($feed) ? 1 : 0;
                return new JsonModel(array('success' => $success));
            }
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function setFavoriteAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->identity()) {
                $feedId = $this->params()->fromPost('feedId');
                $type = $this->params()->fromPost('type');

                $feed = $this->getFeedService()->setFavorite($feedId, $type);
                if ($feed) {
                    if($type == "favorite"){
                        $message = $this->getTranslator()->translate(self::MESSAGE_FAVORITE_SUCCESS);
                    }else{
                        $message = $this->getTranslator()->translate(self::MESSAGE_UNFAVORITE_SUCCESS);
                    }
                    $success = 1;
                } else {
                    $message = $this->getTranslator()->translate(self::MESSAGE_FAVORITE_FAIL);
                    $success = 0;
                }
                return new JsonModel(array(
                    'message' => $message,
                    'success' => $success
                ));
            }

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
     * Retrieve the doctrine entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (!$this->entityManager) {
            $this->setEntityManager($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
        }
        return $this->entityManager;
    }

    /**
     * Set the doctrine entity manager.
     *
     * @param $em
     */
    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }

    /**
     * Get the translator.
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (!$this->translator) {
            $this->setTranslator($this->getServiceLocator()->get('translator'));
        }
        return $this->translator;
    }

    /**
     * Set the translator.
     *
     * @param $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get the feed repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getFeedRepository()
    {
        if (!$this->feedRepository) {
            $this->setFeedRepository($this->getEntityManager()->getRepository('Feed\Entity\Feed'));
        }
        return $this->feedRepository;
    }

    /**
     * Set the feed repository.
     *
     * @param $feedRepository
     */
    public function setFeedRepository($feedRepository)
    {
        $this->feedRepository = $feedRepository;
    }

    /**
     * Get the feed form.
     *
     * @return \Zend\Form\Form
     */
    public function getFeedForm()
    {
        if (!$this->feedForm) {
            $this->setFeedForm($this->getServiceLocator()->get('feed_form'));
        }
        return $this->feedForm;
    }

    /**
     * Set the feed form.
     *
     * @param $feedForm
     */
    public function setFeedForm($feedForm)
    {
        $this->feedForm = $feedForm;
    }

    /**
     * Get the feed service.
     *
     * @return \Feed\Service\Feed
     */
    public function getFeedService()
    {
        if (null === $this->feedService)
            $this->setFeedService($this->getServiceLocator()->get('feed_service'));
        return $this->feedService;
    }

    /**
     * Set the feed service.
     *
     * @param \Feed\Service\Feed $feedService
     */
    public function setFeedService(\Feed\Service\Feed $feedService)
    {
        $this->feedService = $feedService;
    }
}
