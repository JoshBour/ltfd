<?php
/**
 * User: Josh
 * Date: 9/10/2013
 * Time: 1:04 μμ
 */

namespace Feed\Service;


use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Form\Form;
use Zend\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManager;

class Comment implements ServiceManagerAwareInterface
{

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Form
     */
    private $commentForm;

    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $commentRepository;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $feedRepository;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $accountRepository;

    /**
     * Create and store a new comment.
     *
     * @param $data
     * @param id $feedId
     * @return ViewModel
     */
    public function create($data, $feedId)
    {
        $em = $this->getEntityManager();
        $feed = $this->getFeedRepository()->find($feedId);
        if ($feed) {
            $form = $this->getCommentForm();
            $entity = new \Feed\Entity\Comment();
            $form->bind($entity);
            $form->setData($data);
            if ($form->isValid()) {
                $entity->setAuthor($this->getAuthService()->getIdentity()->getId())
                    ->setFeed($feed)
                    ->setPostTime(date('Y-m-d H:i:s'));
                try {
                    $em->persist($entity);
                    $em->flush();

                    return $entity;
                } catch (Exception $e) {
                    return false;
                }
            }
        }
        return false;
    }


    /**
     * Get the comment repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getCommentRepository()
    {
        if (!$this->commentRepository) {
            $this->setCommentRepository($this->getEntityManager()->getRepository('Feed\Entity\Comment'));
        }
        return $this->commentRepository;
    }

    /**
     * Set the comment repository.
     *
     * @param $commentRepository
     */
    public function setCommentRepository($commentRepository)
    {
        $this->commentRepository = $commentRepository;
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
     * Get the account repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getAccountRepository()
    {
        if (!$this->accountRepository) {
            $this->setAccountRepository($this->getEntityManager()->getRepository('Account\Entity\Account'));
        }
        return $this->accountRepository;
    }

    /**
     * Set the account repository.
     *
     * @param $accountRepository
     */
    public function setAccountRepository($accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * getAuthService
     *
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        if (null === $this->authService) {
            $this->authService = $this->getServiceManager()->get('auth_service');
        }
        return $this->authService;
    }

    /**
     * setAuthenticationService
     *
     * @param AuthenticationService $authService
     * @return Feed
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
        return $this;
    }

    /**
     * Set the doctrine entity manager.
     *
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retrieve the doctrine entity manager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager)
            $this->setEntityManager($this->getServiceManager()->get('Doctrine\ORM\EntityManager'));
        return $this->entityManager;
    }

    /**
     * Set service manager.
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Retrieve the service manager.
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set the comment post form.
     *
     * @param Form $commentForm
     */
    public function setCommentForm(Form $commentForm)
    {
        $this->commentForm = $commentForm;
    }

    /**
     * Retrieve the comment post form.
     *
     * @return Form
     */
    public function getCommentForm()
    {
        if (null === $this->commentForm)
            $this->setCommentForm($this->getServiceManager()->get('comment_form'));
        return $this->commentForm;
    }


}