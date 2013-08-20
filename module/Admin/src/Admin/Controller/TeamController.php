<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Paginator\Paginator;

use Admin\Entity\Member;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

class TeamController extends AbstractActionController {
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
		$form = $this->getForm();
		$member = new Member();
		$request = $this->getRequest();
		$page = $this->params('page',1);
		$count = $this->params('count',10);
		$sort = $this->params('sort');
		$type = $this->params('type');
		$repository = $em -> getRepository('Admin\Entity\Member');
		$queryBuilder = $repository->createQueryBuilder('team');
		if(!empty($sort) && !empty($type)){
			$queryBuilder->addOrderBy('team.' . $sort, $type);
		}
		// edw
		$adapter = new DoctrineAdapter(new ORMPaginator($queryBuilder));
		$paginator = new Paginator($adapter);
		$paginator->setDefaultItemCountPerPage($count);
		$paginator->setCurrentPageNumber($page);
		
		$form->bind($member);
		if($request->isPost()){
			$post = array_merge_recursive(
			            $request->getPost()->toArray(),
			            $request->getFiles()->toArray()
			        );			
			$form->setData($post);
			if($form->isValid()){
				$extension = '';
				switch($post['member']['avatar']['type']){
					case 'image/jpeg':
						$extension = 'jpg';
						break;
					case 'image/png':
						$extension = 'png';
						break;
					case 'image/gif':
						$extension = 'gif';
						break;
					default:
						#return;
				}
				$filter = new \Zend\Filter\File\Rename(array(
					'target' => PUBLIC_PATH .'/images/users/member-' . $post['member']['name'] . '.' . $extension 
				));
				$filter->filter($post['member']['avatar']);
				chmod(PUBLIC_PATH .'/images/users/member-' . $post['member']['name'] . '.' . $extension, 0644);
				$member->setAvatar('member-' . $post['member']['name'] . '.' . $extension);	
				
				$em->persist($member);
				$em->flush();
				$this->flashMessenger()->addMessage($this->getTranslator()->translate('The member has been added successfully.'));
				$this->redirect()->toRoute('admin_main/team');
			}
		}

		return new ViewModel( array(
						'paginator' => $paginator, 
						'form' => $this->getForm(),
						'sort' => $sort,
						'type' => $type,
        				'flashMessages' => $this->flashMessenger()->getMessages()));
	}

	public function editAction(){
		$viewModel = new ViewModel();
		$request = $this->getRequest();
		if($request->isXmlHttpRequest()){
			$id = $this->params('id',null);
			$success = 0;
			$message = '';
			$force_refresh = 0;
			if(!is_null($id)){
				$em = $this->getEntityManager();
				$entity = $em->find('Admin\Entity\Member',$id);
				if($entity){
					$form = $this->getForm();
					$form->bind($entity);
					if($request->isPost()){
						$post = array_merge_recursive(
						            $request->getPost()->toArray(),
						            $request->getFiles()->toArray()
						        );		
						$validationGroup = $entity->getUpdateValidationGroup($post);
						if(!empty($validationGroup)){
							$form->setValidationGroup(array('security','member'=>$validationGroup));
							$form->setData($post);						
							if($form->isValid()){
								if(!empty($post['member']['avatar'])) {
									$extension = '';
									switch($post['member']['avatar']['type']){
										case 'image/jpeg':
											$extension = 'jpg';
											break;
										case 'image/png':
											$extension = 'png';
											break;
										case 'image/gif':
											$extension = 'gif';
											break;
										default:
											#return;
									}
									$filter = new \Zend\Filter\File\Rename(array(
										'target' => PUBLIC_PATH .'/images/users/member-' . $entity->getName() . '.' . $extension,
										'overwrite' => true
									));
									$filter->filter($post['member']['avatar']);
									chmod(PUBLIC_PATH .'/images/users/member-' . $entity->getName() . '.' . $extension, 0644);
									$entity->setAvatar('member-' . $entity->getName() . '.' . $extension);	
								}						
								
								$em->persist($entity);
								$em->flush();
								
								$success = 1;
								$force_refresh = 1;
								$this->flashMessenger()->addMessage($this->getTranslator()->translate('The member has been updated successfully'));
							}else{
								$viewModel->setVariable('form', $form);
								$viewModel->setTerminal(true);
								return $viewModel;
							}
						}else{
							$message = $this->getTranslator()->translate('There is nothing new to update!');
						}
						return new JsonModel(array('success' => $success, 'message' => $message, 'force_refresh' => $force_refresh));
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
				$message = $this->getTranslator()->translate('The member is invalid or doesn\'t exist.');
			}else{
				$em = $this->getEntityManager();
				$member = $em->find('Admin\Entity\Member',$id);
				$em->remove($member);
				$em->flush();
				$message = $this->getTranslator()->translate('The member has been deleted successfully');
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
			$this -> setForm($this -> getServiceLocator() -> get('team_form'));
		}
		return $this -> form;
	}

	public function setForm($form) {
		$this -> form = $form;
	}

}
