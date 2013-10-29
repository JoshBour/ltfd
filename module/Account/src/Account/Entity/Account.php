<?php
namespace Account\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Filter\Null;

/**
 * @ORM\Entity(repositoryClass="\Account\Repository\AccountRepository")
 * @ORM\Table(name="accounts")
 */
class Account
{

    const CACHE_RATING_KEY = 'user-ratings-';

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
     * @ORM\Column(type="smallint")
     * @ORM\Column(length=1)
     * @ORM\Column(name="is_active")
     */
    private $isActive;

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
     * @ORM\OneToMany(targetEntity="Account\Entity\AccountsSocials", mappedBy="account")
     */
    private $socials;

    /**
     * @ORM\OneToMany(targetEntity="Feed\Entity\Feed", mappedBy="uploader")
     * @ORM\OrderBy({"postTime" = "DESC"})
     */
    private $feeds;

    /**
     * @ORM\OneToMany(targetEntity="Feed\Entity\Rating", mappedBy="user")
     */
    private $ratings;

    /**
     * @ORM\ManyToMany(targetEntity="Feed\Entity\Feed")
     * @ORM\JoinTable(name="account_favorites",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="id")}
     *      )
     */
    private $favoriteFeeds;

    /**
     * @ORM\ManyToMany(targetEntity="Feed\Entity\Feed")
     * @ORM\JoinTable(name="account_history",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="id")}
     *      )
     */
    private $watchedFeeds;

    public function __construct()
    {
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->socials = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->feeds = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->favoriteFeeds = new ArrayCollection();
        $this->watchedFeeds = new ArrayCollection();
    }

    /**
     * Returns an array with the rated feeds.
     *
     * @return array
     */
    public function getFeedRatingArray(){
        $ratings = array();
        foreach($this->ratings->toArray() as $rating)
            $ratings[] = $rating->getFeed();
        return $ratings;
    }


    /**
     * Check if the user has rated a feed.
     *
     * @param \Feed\Entity\Feed $feed
     * @return null|\Feed\Entity\Rating
     */
    public function getRated($feed)
    {
        foreach ($this->ratings->toArray() as $rating)
            if ($rating->getFeed()->getId() == $feed) return $rating;
        return null;
    }


    /**
     * Get the fields that need to be validated
     *
     * @param array $data
     * @return array
     */
    public function getUpdateValidationGroup($data)
    {
        $validationGroup = array();
        foreach ($data['account'] as $field => $value) {
            if (property_exists($this, $field) && !empty($value) && $value != $this->{$field}) {
                if ($field == 'avatar') {
                    if (!empty($value['name']) && $value['name'] != $this->avatar) {
                        $validationGroup[] = $field;
                    }
                } else {
                    $validationGroup[] = $field;
                }
            }
        }
        return $validationGroup;
    }

    /**
     * Get the full path to the avatar
     * according to the dimensions given
     *
     * @param int $dimensionX
     * @param int $dimensionY
     * @return string
     */
    public function getAvatarIcon($dimensionX = 35, $dimensionY = 35)
    {
        if (empty($this->avatar)) {
            return 'user-default-' . $dimensionX . 'x' . $dimensionY . '.jpg';
        } else {
            $extension = pathinfo($this->avatar);
            $avatarName = 'users/' . $this->id . '/user-default-' . $dimensionX . 'x' . $dimensionY . '.' . $extension['extension'];
            return $avatarName;
        }
    }

    /**
     * Hash the password.
     *
     * @param string $password
     * @return string
     */
    public static function getHashedPassword($password)
    {
        return crypt($password . 'leetfeedpenbour');
    }

    /**
     * Check if the user's password is the same as the provided one.
     *
     * @param Account $user
     * @param string $password
     * @return bool
     */
    public static function hashPassword($user, $password)
    {
        return ($user->getPassword() === crypt($password . 'leetfeedpenbour', $user->getPassword()));
    }

    /**
     * Set the account's avatar.
     *
     * @param $avatar
     * @return Account
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * Get the account's avatar.
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set the account's email.
     *
     * @param string $email
     * @return Account
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get the account's email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the account's favorite feeds.
     *
     * @param $favoriteFeeds
     * @return Account
     */
    public function setFavoriteFeeds($favoriteFeeds){
        $this->favoriteFeeds[] = $favoriteFeeds;
        return $this;
    }

    /**
     * Get the account's favorite feeds.
     *
     * @return ArrayCollection
     */
    public function getFavoriteFeeds(){
        return $this->favoriteFeeds;
    }

    /**
     * Add a feed to the account's favorites.
     *
     * @param array|\Feed\Entity\Feed $favoriteFeeds
     */
    public function addFavoriteFeeds($favoriteFeeds){
        if (is_array($favoriteFeeds)) {
            foreach ($favoriteFeeds as $feed)
                $this->favoriteFeeds->add($feed);
        } else {
            $this->favoriteFeeds->add($favoriteFeeds);
        }
    }

    /**
     * Remove a feed from the account's favorites.
     *
     * @param array|\Feed\Entity\Feed $favoriteFeeds
     */
    public function removeFavoriteFeeds($favoriteFeeds){
        if (is_array($favoriteFeeds)) {
            foreach ($favoriteFeeds as $feed)
                $this->favoriteFeeds->removeElement($feed);
        } else {
            $this->favoriteFeeds->removeElement($favoriteFeeds);
        }
    }

    /**
     * Set the account's feeds.
     *
     * @param \Feed\Entity\Feed $feeds
     * @return Account
     */
    public function setFeeds($feeds)
    {
        $this->feeds[] = $feeds;
        return $this;
    }

    /**
     * Get the account's feeds.
     *
     * @return ArrayCollection
     */
    public function getFeeds()
    {
        return $this->feeds;
    }

    /**
     * Add feed(s) to the existing ones.
     *
     * @param array|\Feed\Entity\Feed $feeds
     */
    public function addFeeds($feeds)
    {
        if (is_array($feeds)) {
            foreach ($feeds as $feed)
                $this->feeds->add($feed);
        } else {
            $this->feeds->add($feeds);
        }
    }

    /**
     * Remove feed(s) from the existing ones.
     *
     * @param array|\Feed\Entity\Feed $feeds
     */
    public function removeFeeds($feeds)
    {
        if (is_array($feeds)) {
            foreach ($feeds as $feed)
                $this->feeds->removeElement($feed);
        } else {
            $this->feeds->removeElement($feeds);
        }
    }

    /**
     * Set the account's followers
     *
     * @param Account $followers
     * @return Account
     */
    public function setFollowers($followers)
    {
        $this->followers[] = $followers;
        return $this;
    }

    /**
     * Get the account's followers
     *
     * @return ArrayCollection
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Add follower(s) to the existing ones.
     *
     * @param array|Account $followers
     */
    public function addFollowers($followers)
    {
        if (is_array($followers)) {
            foreach ($followers as $follower)
                $this->followers->add($follower);
        } else {
            $this->followers->add($followers);
        }
    }

    /**
     * Remove follower(s) from the existing ones.
     *
     * @param array|Account $followers
     */
    public function removeFollowers($followers)
    {
        if (is_array($followers)) {
            foreach ($followers as $follower)
                $this->followers->removeElement($follower);
        } else {
            $this->followers->removeElement($followers);
        }
    }

    /**
     * Set the account's following.
     *
     * @param Account $following
     * @return Account
     */
    public function setFollowing($following)
    {
        $this->following = $following;
        return $this;
    }

    /**
     * Get the account's following.
     *
     * @return ArrayCollection
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * Add following account(s) to the existing ones.
     *
     * @param array|Account $following
     */
    public function addFollowing($following)
    {
        if (is_array($following)) {
            foreach ($following as $account)
                $this->following->add($account);
        } else {
            $this->following->add($following);
        }
    }

    /**
     * Remove following account(s) from the existing ones.
     *
     * @param array|Account $following
     */
    public function removeFollowing($following)
    {
        if (is_array($following)) {
            foreach ($following as $account)
                $this->following->removeElement($account);
        } else {
            $this->following->removeElement($following);
        }
    }

    /**
     * Set the account's games
     *
     * @param \Game\Entity\Game $games
     * @return Account
     */
    public function setGames($games)
    {
        $this->games[] = $games;
        return $this;
    }

    /**
     * Get the account's games
     *
     * @return ArrayCollection
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * Add game(s) to the existing ones.
     *
     * @param array|\Game\Entity\Game $games
     */
    public function addGames($games)
    {
        if (is_array($games)) {
            foreach ($games as $game)
                $this->games->add($game);
        } else {
            $this->games->add($games);
        }
    }

    /**
     * Remove game(s) from the existing ones.
     *
     * @param array|\Game\Entity\Game $games
     */
    public function removeGames($games)
    {
        if (is_array($games)) {
            foreach ($games as $game)
                $this->games->removeElement($game);
        } else {
            $this->games->removeElement($games);
        }
    }

    /**
     * Set the account's groups
     *
     * @param Group $groups
     * @return Account
     */
    public function setGroups($groups)
    {
        $this->groups[] = $groups;
        return $this;
    }

    /**
     * Get the account's groups
     *
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add group(s) to the existing ones.
     *
     * @param array|Group $groups
     */
    public function addGroups($groups)
    {
        if (is_array($groups)) {
            foreach ($groups as $group)
                $this->groups->add($group);
        } else {
            $this->groups->add($groups);
        }
    }

    /**
     * Remove group(s) from the existing ones.
     *
     * @param array|Group $groups
     */
    public function removeGroups($groups)
    {
        if (is_array($groups)) {
            foreach ($groups as $group)
                $this->groups->removeElement($group);
        } else {
            $this->groups->removeElement($groups);
        }
    }

    /**
     * Set the account's id.
     *
     * @param int $id
     * @return Account
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the account's id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set the account's ip
     *
     * @param string $ip
     * @return Account
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * Get the account's ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set the account's activated status
     *
     * @param int $isActivated
     * @return Account
     */
    public function setIsActivated($isActivated)
    {
        $this->isActivated = $isActivated;
        return $this;
    }

    /**
     * Get the account's activated status
     *
     * @return int
     */
    public function getIsActivated()
    {
        return $this->isActivated;
    }

    /**
     * Set the account's activate status
     *
     * @param int $isActive
     * @return Account
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * Get the account's activate status
     *
     * @return int
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set the account's last seen time
     * Accepted format is Y-m-d H:i:s
     *
     * @param string $lastSeen
     * @return Account
     */
    public function setLastSeen($lastSeen)
    {
        $this->lastSeen = $lastSeen;
        return $this;
    }

    /**
     * Get the account's last seen time
     * The format is Y-m-d H:i:s
     *
     * @return string
     */
    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    /**
     * Set the account's password
     *
     * @param string $password
     * @return Account
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get the account's password (crypted)
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the account's feed ratings.
     *
     * @param $ratings
     * @return Account
     */
    public function setRatings($ratings)
    {
        $this->ratings[] = $ratings;
        return $this;
    }

    /**
     * Get the account's feed ratings.
     *
     * @return ArrayCollection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * Add rating(s) to the existing ones.
     *
     * @param array|\Feed\Entity\Rating $ratings
     */
    public function addRatings($ratings)
    {
        if (is_array($ratings)) {
            foreach ($ratings as $rating)
                $this->ratings->add($rating);
        } else {
            $this->ratings->add($ratings);
        }
    }

    /**
     * Remove rating(s) from the existing ones.
     *
     * @param array|\Feed\Entity\Rating $ratings
     */
    public function removeRatings($ratings)
    {
        if (is_array($ratings)) {
            foreach ($ratings as $rating)
                $this->ratings->removeElement($rating);
        } else {
            $this->ratings->removeElement($ratings);
        }
    }

    /**
     * Set the account's register date.
     * Accepted format is Y-m-d H:i:s
     *
     * @param string $registerDate
     * @return Account
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;
        return $this;
    }

    /**
     * Get the account's register date.
     * Format is Y-m-d H:i:s
     *
     * @return mixed
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * Set the account's socials.
     *
     * @param \Application\Entity\Social $socials
     * @return Account
     */
    public function setSocials($socials)
    {
        $this->socials[] = $socials;
        return $this;
    }

    /**
     * Get the account's socials
     *
     * @return ArrayCollection
     */
    public function getSocials()
    {
        return $this->socials;
    }

    /**
     * Add social(s) to the existing ones.
     *
     * @param array|\Application\Entity\Social $socials
     */
    public function addSocials($socials)
    {
        if (is_array($socials)) {
            foreach ($socials as $social)
                $this->socials->add($social);
        } else {
            $this->socials->add($socials);
        }
    }

    /**
     * Remove social(s) from the existing ones.
     *
     * @param array|\Feed\Entity\Rating $socials
     */
    public function removeSocials($socials)
    {
        if (is_array($socials)) {
            foreach ($socials as $social)
                $this->socials->removeElement($social);
        } else {
            $this->socials->removeElement($socials);
        }
    }

    /**
     * Set the account's username.
     *
     * @param string $username
     * @return Account
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get the account's username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the account's watched feeds.
     *
     * @param $watchedFeeds
     * @return Account
     */
    public function setWatchedFeeds($watchedFeeds){
        $this->watchedFeeds[] = $watchedFeeds;
        return $this;
    }

    /**
     * Get the account's watched feeds.
     *
     * @return ArrayCollection
     */
    public function getWatchedFeeds(){
        return $this->watchedFeeds;
    }

    /**
     * Add a feed to the account's watched ones.
     *
     * @param array|\Feed\Entity\Feed $watchedFeeds
     */
    public function addWatchedFeeds($watchedFeeds){
        if (is_array($watchedFeeds)) {
            foreach ($watchedFeeds as $feed)
                $this->watchedFeeds->add($feed);
        } else {
            $this->watchedFeeds->add($watchedFeeds);
        }
    }

    /**
     * Remove a feed from the account's watched ones.
     *
     * @param array|\Feed\Entity\Feed $watchedFeeds
     */
    public function removeWatchedFeeds($watchedFeeds){
        if (is_array($watchedFeeds)) {
            foreach ($watchedFeeds as $feed)
                $this->watchedFeeds->removeElement($feed);
        } else {
            $this->watchedFeeds->removeElement($watchedFeeds);
        }
    }


}
