<?php
namespace Account\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="accounts")
 */
class Account
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
     * @ORM\Column(length=25)
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

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="register_date")
     */
    private $registerDate;

    /**
     * @ORM\Column(type="timestamp")
     * @ORM\Column(name="last_seen")
     */
    private $lastSeen;

    /**
     * @ORM\Column(type="smallint")
     * @ORM\Column(length=1)
     * @ORM\Column(name="is_activated")
     */
    private $isActivated;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     */
    private $ip;

    /**
     * @ORM\ManyToMany(targetEntity="Account\Entity\Account", mappedBy="following")
     */
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity="Account\Entity\User", inversedBy="followers")
     * @ORM\JoinTable(name="followers",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="follower_id", referencedColumnName="id")}
     *      )
     */
    private $following;

    /**
     * @ORM\ManyToMany(targetEntity="Account\Entity\Group")
     * @ORM\JoinTable(name="accounts_groups",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     *      )
     */
    private $groups;

    public function __construct(){
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $followers
     */
    public function setFollowers($followers)
    {
        $this->followers[] = $followers;
    }

    /**
     * @return mixed
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    public function addFollowers($followers){
        foreach($followers as $follower)
            $this->followers->add($follower);
    }

    public function removeFollowers($followers){
        $this->followers->removeElement($followers);
    }

    /**
     * @param mixed $following
     */
    public function setFollowing($following)
    {
        $this->following[] = $following;
    }

    /**
     * @return mixed
     */
    public function getFollowing()
    {
        return $this->following;
    }

    public function addFollowing($followers){
        foreach($followers as $follower)
            $this->following->add($follower);
    }

    public function removeFollowing($followers){
        $this->following->removeElement($followers);
    }

    /**
     * @param mixed $groups
     */
    public function setGroups($groups)
    {
        $this->groups[] = $groups;
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    public function addGroups($groups){
        foreach($groups as $group)
            $this->groups->add($group);
    }

    public function removeGroups($groups){
        $this->groups->removeElement($groups);
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
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $isActivated
     */
    public function setIsActivated($isActivated)
    {
        $this->isActivated = $isActivated;
    }

    /**
     * @return mixed
     */
    public function getIsActivated()
    {
        return $this->isActivated;
    }

    /**
     * @param mixed $lastSeen
     */
    public function setLastSeen($lastSeen)
    {
        $this->lastSeen = $lastSeen;
    }

    /**
     * @return mixed
     */
    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $registerDate
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;
    }

    /**
     * @return mixed
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }



    public static function hashPassword($user, $password)
    {
        return ($user->getPassword() === crypt($password, $user->getPassword()));
    }

}
