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

    const MESSAGE_COMMENT_ADD_FAIL = 'There was an error when saving the comment, please try again or refresh the page.';
    const MESSAGE_COMMENT_ADD_SUCCESS = 'The comment has been stored successfully.';
    const MESSAGE_NO_MORE_COMMENTS = 'There are no more comments.';
    const MESSAGE_COMMENTS_LOAD_FAIL = 'There was an error when loading the comments, please try again or refresh the page.';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    /**
     * @var \Zend\Form\Form
     */
    private $commentForm;

    /**
     * @var \Feed\Service\Comment
     */
    private $commentService;

    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $feedId = $this->params()->fromQuery('feedId');
            $comment = $this->getCommentService()->create($request->getPost(),$feedId);
            if($comment){
                $viewModel = new ViewModel(array('comment' => $comment));
                $viewModel->setTerminal(true);
                return $viewModel;
            }else{
                $success = 0;
                $message = $this->getTranslator()->translate(self::MESSAGE_COMMENT_ADD_FAIL);
            }

            return new JsonModel(array(
                'message' => $message,
                'success' => $success
            ));
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }

    public function deleteAction()
    {
        return new JsonModel();
    }

    public function editAction()
    {
        return new JsonModel();
    }

    public function reportAction()
    {
        return new JsonModel();
    }

    public function listAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $feedId = $this->params()->fromQuery('feedId');
            $pageNumber = $this->params()->fromQuery('page', 1);
            $commentsPerPage = $this->params()->fromQuery('itemCount', 20);

            $viewModel = new ViewModel();
            $feed = $this->getEntityManager()->getRepository('\Feed\Entity\Feed')->find($feedId);
            if ($feed) {
                $comments = $feed->getComments();
                $commentNumber = $comments->count();
                if ($commentNumber-1 > 0) {
                    if ($pageNumber >= $comments->count() - 1) {
                        return new JsonModel(array('success' => 0, 'message' => $this->getTranslator()->translate(self::MESSAGE_NO_MORE_COMMENTS)));
                    }
                    $comments->setCurrentPageNumber($pageNumber)
                        ->setItemCountPerPage($commentsPerPage);
                } else {
                    return new JsonModel(array('success' => 0, 'message' => $this->getTranslator()->translate(self::MESSAGE_NO_MORE_COMMENTS)));
                }
            } else {
                echo $this->getTranslator()->translate(self::MESSAGE_COMMENTS_LOAD_FAIL);
            }


            $viewModel->setVariables(array('comments' => $comments));
            $viewModel->setTerminal(true);
            return $viewModel;
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }


    /**
     * Get the comment service.
     *
     * @return \Feed\Service\Comment
     */
    public function getCommentService(){
        if(null === $this->commentService){
            $this->setCommentService($this->getServiceLocator()->get('comment_service'));
        }
        return $this->commentService;
    }

    /**
     * Set the comment service.
     *
     * @param $commentService
     */
    public function setCommentService($commentService){
        $this->commentService = $commentService;
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
     * Retrieve the zend translator.
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
     * Set the zend translator.
     *
     * @param $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get the comment form.
     *
     * @return Form
     */
    public function getCommentForm()
    {
        if (!$this->commentForm) {
            $this->setCommentForm($this->getServiceLocator()->get('comment_form'));
        }
        return $this->commentForm;
    }

    /**
     * Set the comment form.
     *
     * @param Form $commentForm
     */
    public function setCommentForm($commentForm)
    {
        $this->commentForm = $commentForm;
    }
}
