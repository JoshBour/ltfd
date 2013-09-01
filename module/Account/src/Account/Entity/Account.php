<?php
namespace Account\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Filter\Null;

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
     * @ORM\Column(length=15)
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
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     */
    private $avatar;

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
     * @ORM\ManyToMany(targetEntity="Account\Entity\Account", inversedBy="followers")
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

    /**
     * @ORM\ManyToMany(targetEntity="Game\Entity\Game")
     * @ORM\JoinTable(name="games_followers",
     *      joinColumns={@ORM\JoinColumn(name="follower_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")}
     *      )
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity="Account\Entity\AccountSocial", mappedBy="account")
     */
    private $socials;

    public function __construct(){
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->socials = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->games = new ArrayCollection();

    }

    public function getAvatarIcon($dimensionX = 35, $dimensionY = 35){
        if(empty($this->avatar)){
            return 'user-default-' . $dimensionX . 'x' . $dimensionY . '.jpg';
        }else{
            $extension = pathinfo($this->avatar);
            $avatarName = '/users/'. $this->id .'/user-avatar-' . $dimensionX . 'x'.$dimensionY . '.' . $extension['extension'];
            return $avatarName;
        }
    }

    public function getAvatar(){
        if(is_null($this->avatar)){
            return 'user-default.jpg';
        }else{
            return '/users/' . $this->id . '/' . $this->avatar;
        }
    }

    public function setAvatar($avatar){
        $this->avatar = $avatar;
    }
    /**
     * @param mixed $socials
     */
    public function setSocials($socials)
    {
        $this->socials[] = $socials;
    }

    /**
     * @return mixed
     */
    public function getSocials()
    {
        return $this->socials;
    }

    public function addSocials($socials){
        foreach($socials as $social)
            $this->socials->add($social);
    }

    public function removeSocials($socials){
        foreach($socials as $social)
            $this->socials->removeElement($social);
    }

    /**
     * @param mixed $games
     */
    public function setGames($games)
    {
        $this->games = $games;
    }

    /**
     * @return mixed
     */
    public function getGames()
    {
        return $this->games;
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
        return ($user->getPassword() === crypt($password . 'leetfeedpenbour', $user->getPassword()));
    }

}
