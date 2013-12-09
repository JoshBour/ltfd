<?php
namespace Account\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Group
 * @package Account\Entity
 * @ORM\Entity
 * @ORM\Table(name="groups")
 */
class Group
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="group_id")
     */
    private $groupId;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Account", inversedBy="groups")
     */
    private $accounts;

    public function __construct(){
        $this->accounts = new ArrayCollection();
    }

    /**
     * Sets the corresponding accounts to the group.
     *
     * @param Account $accounts
     * @return Group
     */
    public function setAccounts($accounts){
        $this->accounts[] = $accounts;
        return $this;
    }

    /**
     * Gets the corresponding accounts to the group.
     *
     * @return Account
     */
    public function getAccounts(){
        return $this->accounts;
    }

    /**
     * Sets the group's unique id.
     *
     * @param int $groupId
     * @return Group
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * Gets the group's unique id.
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Sets the group's name.
     *
     * @param string $name
     * @return Group
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets the group's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }



}
