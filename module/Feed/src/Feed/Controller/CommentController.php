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

    public function addAction()
    {
        $request = $this->getRequest();
        if($request->isXmlHttpRequest()){
            $feedId = $this->params()->fromQuery('feedId');
            $em = $this->getEntityManager();
            $feed = $em->getRepository('Feed\Entity\Feed')->find($feedId);
            $success = 0;
            $message = '';
            if($feed){
                $form = $this->getCommentForm();
                $entity = new \Feed\Entity\Comment();
                $form->bind($entity);
                $data = $request->getPost();
                $form->setData($data);
                if($form->isValid()){
                    $entity->setAuthor($this->user())
                           ->setFeed($feed)
                           ->setPostTime(date('Y-m-d H:i:s'));
                    try{
                        $em->persist($entity);
                        $em->flush();
                        $success = 1;
                    }catch(Exception $e){
                        $message = $e->getMessage();
                    }
                }else{
                    $message = $form->getMessages();
                }
            }else{
                $message = $this->translator->translate('There was something wrong with the feed, please try again.');
            }
            return new JsonModel(array(
                'message' => $message,
                'success' => $success
            ));
        }else{
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
            $success = 0;
            $message = '';
            $viewModel = new ViewModel();
            $feed = $this->getEntityManager()->getRepository('\Feed\Entity\Feed')->find($feedId);
            if($feed){
                $comments = $feed->getComments();
            }else{
                echo $this->getTranslator()->translate('The comments could not be retrieved, please try again.');
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
}
