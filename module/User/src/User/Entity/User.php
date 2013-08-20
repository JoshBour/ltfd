<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User{

	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 * @ORM\Column(length=11)
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 * @ORM\Column(length=50)
	 */
	private $username;
	
	/**
	 * @ORM\Column(type="string")
	 * @ORM\Column(length=128)
	 */
	private $password;

	/**
	 * @ORM\Column(type="string")
	 * @ORM\Column(length=255)
	 */
	private $email;


	
	public function getUpdateValidationGroup($data){
		$validationGroup = array();
		if($data->user['username'] != $this->username){
			$validationGroup[] = 'username';
		}
		if($data->user['email'] != $this->email){
			$validationGroup[] = 'email';
		}
		if(!empty($data->user['password'])){
			$validationGroup[] = 'password';
		}
		return $validationGroup;	
	}

	public function getId()
	{
	    return $this->id;
	}

	public function setId($id)
	{
	    $this->id = $id;
	}

	public function getUsername()
	{
	    return $this->username;
	}

	public function setUsername($username)
	{
	    $this->username = $username;
	}

	public function getPassword()
	{
	    return $this->password;
	}

	public function setPassword($password)
	{
	    $this->password = crypt($password);
	}

	public function getEmail()
	{
	    return $this->email;
	}

	public function setEmail($email)
	{
		if(empty($email)) $email = null;
	    $this->email = $email;
	}
	
	public static function hashPassword($user, $password){
		return ($user->getPassword() === crypt($password,$user->getPassword()));
	}
	
}
