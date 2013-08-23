<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Paginator\Paginator;

use Admin\Entity\General;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

class GeneralController extends AbstractActionController {
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $entityManager;

	/**
	 * @var Zend\I18n\Translator\Translator
	 */
	private $translator;

	/**
	 * @var Zend\Form
	 */
	private $form;

	public function listAction() {
	    if (!$user = $this->identity()) {
	 		$this->redirect()->toRoute('account-login');   
		}			
		$em = $this->getEntityManager();
		$general = new General();
		$hideForm = true;
		$request = $this->getRequest();
		$page = $this->params('page',1);
		$count = $this->params('count',10);
		$sort = $this->params('sort');
		$type = $this->params('type');
		$repository = $em -> getRepository('Admin\Entity\General');
		$queryBuilder = $repository->createQueryBuilder('general');
		if(!empty($sort) && !empty($type)){
			$queryBuilder->addOrderBy('general.' . $sort, $type);
		}
		// edw
		$adapter = new DoctrineAdapter(new ORMPaginator($queryBuilder));
		$paginator = new Paginator($adapter);
		$paginator->setDefaultItemCountPerPage($count);
		$paginator->setCurrentPageNumber($page);

		return new ViewModel( array(
						'paginator' => $paginator, 
						'sort' => $sort,
						'type' => $type));
	}

	public function editAction(){
		$viewModel = new ViewModel();
		$request = $this->getRequest();
		if($request->isXmlHttpRequest()){
			$id = $this->params('id',null);
			$success = 0;
			$message = '';
			if(!is_null($id)){
				$em = $this->getEntityManager();
				$entity = $em->find('Admin\Entity\General',$id);
				if($entity){
					$form = $this->getForm();
					$form->bind($entity);
					if($request->isPost()){
						$data = $request->getPost();
						$validationGroup = $entity->getUpdateValidationGroup($data);
						if(!empty($validationGroup)){
							$form->setValidationGroup(array('security','general'=>$validationGroup));
							$form->setData($data);
							if($form->isValid()){
								$em->persist($entity);
								$em->flush();
								
								$success = 1;
								$message = $this->getTranslator()->translate('The information has been updated successfully');
							}else{
								$viewModel->setVariable('form', $form);
								$viewModel->setTerminal(true);
								return $viewModel;
							}
						}else{
							$message = $this->getTranslator()->translate('There is nothing new to update!');
						}
						return new JsonModel(array('success' => $success, 'message' => $message));
					}
					$viewModel->setVariable('form', $form);
				}
			}
	  		$viewModel->setTerminal(true);
			return $viewModel;
		}else{
			$this->getResponse()->setStatusCode(404);
			return;			
		}
	}
	
	public function deleteAction(){
		$request = $this->getRequest();
		if($request->isXmlHttpRequest()){
			$success = 0;
			$message = '';
			$id = $this->params()->fromPost('id',null);
			if(null === $id){
				$message = $this->getTranslator()->translate('The feed is invalid or doesn\'t exist.');
			}else{
				$em = $this->getEntityManager();
				$user = $em->find('Feed\Entity\Feed',$id);
				$em->remove($user);
				$em->flush();
				$message = $this->getTranslator()->translate('The feed has been deleted successfully');
				$success = 1;
			}
			return new JsonModel(array('success'=>$success,'message'=>$message));
		}else{
			$this->getResponse()->setStatusCode(404);
			return;
		}
	}

	public function getEntityManager() {
		if (!$this -> entityManager) {
			$this -> setEntityManager($this -> getServiceLocator() -> get('Doctrine\ORM\EntityManager'));
		}
		return $this -> entityManager;
	}

	public function setEntityManager($em) {
		$this -> entityManager = $em;
	}

	public function getTranslator() {
		if (!$this -> translator) {
			$this -> setTranslator($this -> getServiceLocator() -> get('translator'));
		}
		return $this -> translator;
	}

	public function setTranslator($translator) {
		$this -> translator = $translator;
	}

	/**
	 * @return \Zend\Form\Form
	 */
	public function getForm() {
		if (!$this -> form) {
			$this -> setForm($this -> getServiceLocator() -> get('general_form'));
		}
		return $this -> form;
	}

	public function setForm($form) {
		$this -> form = $form;
	}

}
