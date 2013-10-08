<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="socials")
 */
class Social
{

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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Account\Entity\AccountsSocials", mappedBy="social")
     */
    private $accounts;

    public function __construct(){
        $this->accounts = new ArrayCollection();
    }

    /**
     * @param mixed $socials
     */
    public function setAccounts($accounts)
    {
        $this->accounts[] = $accounts;
    }

    /**
     * @return mixed
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    public function addAccounts($accounts){
        foreach($accounts as $account)
            $this->accounts->add($account);
    }

    public function removeAccounts($accounts){
        foreach($accounts as $account)
            $this->accounts->removeElement($account);
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }



}
