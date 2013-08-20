<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Paginator\Paginator;

use Admin\Entity\Film;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

class FilmController extends AbstractActionController {
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
		$film = new Film();
		$request = $this->getRequest();
		$page = $this->params('page',1);
		$count = $this->params('count',10);
		$sort = $this->params('sort');
		$type = $this->params('type');
		$repository = $em -> getRepository('Admin\Entity\Film');
		$queryBuilder = $repository->createQueryBuilder('film');
		if(!empty($sort) && !empty($type)){
			$queryBuilder->addOrderBy('film.' . $sort, $type);
		}
		// edw
		$adapter = new DoctrineAdapter(new ORMPaginator($queryBuilder));
		$paginator = new Paginator($adapter);
		$paginator->setDefaultItemCountPerPage($count);
		$paginator->setCurrentPageNumber($page);
		
		$form->bind($film);
	
		
		if($request->isPost()){
			$post = array_merge_recursive(
			            $request->getPost()->toArray(),
			            $request->getFiles()->toArray()
			        );		
			$form->setData($post);
			if($form->isValid()){
				// snapshot validation
				if(!empty($post['film']['snapshot']['name'])){
					$extension = '';
					switch($post['film']['snapshot']['type']){
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
					$rand = rand(1000,9000);
					$filter = new \Zend\Filter\File\Rename(array(
						'target' => PUBLIC_PATH .'/videos/video-' . $rand . '-snapshot.' . $extension 
					));
					$filter->filter($post['film']['snapshot']);
					chmod(PUBLIC_PATH .'/videos/video-' . $rand . '-snapshot.' . $extension, 0644);
					
					// create small avatars of the image
					$simpleImage = new \Admin\Model\SimpleImage();
					$simpleImage->load(PUBLIC_PATH .'/videos/video-' . $rand . '-snapshot.' . $extension);
					$simpleImage->createThumbnail(470,PUBLIC_PATH .'/videos/video-' . $rand . '-snapshot-470-445.' . $extension,441);	
					$simpleImage->createThumbnail(234,PUBLIC_PATH .'/videos/video-' . $rand . '-snapshot-234-220.' . $extension,220);						
					$film->setSnapshot('video-' . $rand . '-snapshot.' . $extension);	
				}else{
					$film->setSnapshot(null);
				}
				
 				foreach($post['film']['categories'] as $categoryId){
 					$category = $em->getRepository('Admin\Entity\Category')->find($categoryId);
					$film->setCategories($category);
 				}
				
				$em->persist($film);
				$em->flush();
				$this->flashMessenger()->addMessage($this->getTranslator()->translate('The film has been added successfully.'));
				#$this->redirect()->toRoute('admin_main/films');
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
		$em = $this->getEntityManager();
		$request = $this->getRequest();
		if($request->isXmlHttpRequest()){
			$id = $this->params('id',null);
			$success = 0;
			$force_refresh = 0;
			$message = '';
			if(!is_null($id)){
				$em = $this->getEntityManager();
				$entity = $em->find('Admin\Entity\Film',$id);
				if($entity){
					$form = $this->getForm();
					$form->bind($entity);
					if($request->isPost()){
						$post = array_merge_recursive(
						            $request->getPost()->toArray(),
						            $request->getFiles()->toArray()
						        );		
						$validationGroup = $entity->getUpdateValidationGroup($post);
						#$message = json_encode($validationGroup);
						if(!empty($validationGroup)){
							$form->setValidationGroup(array('security','film'=>$validationGroup));
							$form->setData($post);						
							if($form->isValid()){
								if(!empty($post['film']['snapshot'])){
									// snapshot validation
									$extension = '';
									switch($post['film']['snapshot']['type']){
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
									$rand = rand(1000,9000);
									$filter = new \Zend\Filter\File\Rename(array(
										'target' => PUBLIC_PATH .'/videos/video-' . $rand . '-snapshot.' . $extension 
									));
									$filter->filter($post['film']['snapshot']);
									chmod(PUBLIC_PATH .'/videos/video-' . $rand . '-snapshot.' . $extension, 0644);
									
									// create small avatars of the image
									$simpleImage = new \Admin\Model\SimpleImage();
									$simpleImage->load(PUBLIC_PATH .'/videos/video-' . $rand . '-snapshot.' . $extension);
									$simpleImage->createThumbnail(470,PUBLIC_PATH .'/videos/video-' . $rand . '-snapshot-470-445.' . $extension,441);	
									$simpleImage->createThumbnail(234,PUBLIC_PATH .'/videos/video-' . $rand . '-snapshot-234-220.' . $extension,220);										
									$entity->setSnapshot('video-' . $rand . '-snapshot.' . $extension);	
								}
								$entity->clearCategories();
				 				foreach($post['film']['categories'] as $categoryId){
				 					$category = $em->getRepository('Admin\Entity\Category')->find($categoryId);
									$entity->setCategories($category);
				 				}	
						
								
								$em->persist($entity);
								$em->flush();
								
								$success = 1;
								$force_refresh = 1;
								$this->flashMessenger()->addMessage($this->getTranslator()->translate('The film has been updated successfully'));
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
				$message = $this->getTranslator()->translate('The film is invalid or doesn\'t exist.');
			}else{
				$em = $this->getEntityManager();
				$entity = $em->find('Admin\Entity\Film',$id);
				$em->remove($entity);
				$em->flush();
				$message = $this->getTranslator()->translate('The film has been deleted successfully');
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
			$this -> setForm($this -> getServiceLocator() -> get('film_form'));
		}
		return $this -> form;
	}

	public function setForm($form) {
		$this -> form = $form;
	}

}
