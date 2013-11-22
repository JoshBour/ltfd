<?php
namespace Account\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Filter\Null;
use Feed\Entity\Feed;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

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
     * @ORM\ManyToMany(targetEntity="Feed\Entity\Feed")
     * @ORM\JoinTable(name="accounts_feeds_favorites",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="id")}
     *      )
     */
    private $favoriteFeeds;

    /**
     * @ORM\ManyToMany(targetEntity="Feed\Entity\Feed")
     * @ORM\JoinTable(name="accounts_feeds_history",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="id")}
     *      )
     */
    private $watchedFeeds;

    /**
     * @ORM\ManyToMany(targetEntity="Feed\Entity\Feed")
     * @ORM\JoinTable(name="accounts_feeds_deleted",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="id")}
     *      )
     */
    private $deletedFeeds;

    /**
     * @ORM\ManyToMany(targetEntity="Feed\Entity\Feed")
     * @ORM\JoinTable(name="accounts_feeds_liked",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="id")}
     *      )
     */
    private $likedFeeds;

    /**
     * @ORM\ManyToMany(targetEntity="Feed\Entity\Feed")
     * @ORM\JoinTable(name="accounts_feeds_queue",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="id")}
     *      )
     */
    private $feedQueue;

    public function __construct()
    {
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->socials = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->favoriteFeeds = new ArrayCollection();
        $this->watchedFeeds = new ArrayCollection();
        $this->deletedFeeds = new ArrayCollection();
        $this->feedQueue = new ArrayCollection();
        $this->likedFeeds = new ArrayCollection();
    }

    public function hasInteractedWithFeed(Feed $feed)
    {
        return (in_array($feed->getVideoId(), $this->getInteractedFeedIds(true, null, true)));
    }

    public function hasFavorite($feedId)
    {
        $feedFavoriteArray = $this->getInteractedFeedIds(false, 'favorites');
        return in_array($feedId, $feedFavoriteArray);
    }

    public function hasLiked($feedId)
    {
        $feedFavoriteArray = $this->getInteractedFeedIds(false, 'liked');
        return in_array($feedId, $feedFavoriteArray);
    }

    /**
     * Returns an array with the video ids of the feed queue's videos.
     * If an feed array is provided, it returns the video ids using that array.
     *
     * @param null|ArrayCollection $feeds
     * @return array
     */
    public function getFeedQueueVideoIds($feeds = null)
    {
        $ids = array();
        if ($feeds == null) {
            $feeds = $this->feedQueue;
        }
        /**
         * @var Feed $feed
         */
        foreach ($feeds as $feed) {
            $ids[] = $feed->getVideoId();
        }
        return $ids;
    }

    /**
     * Returns an array of ids from a type of feeds the user has interacted with.
     * Valid types: history, favorites, liked, deleted
     *
     * @param bool $merged If set to true it will return all the interacted feeds.
     * @param string $defaultType The type of feeds to return
     * @param bool $useVideoUrls  If set to true it will return the video ids instead of the feed ids
     * @return array
     */
    public function getInteractedFeedIds($merged = false, $defaultType = "history", $useVideoUrls = false)
    {
        $feedIds = array();
        if ($merged) {
            $feeds = array_merge($this->watchedFeeds->toArray(),
                $this->favoriteFeeds->toArray(),
                $this->likedFeeds->toArray(),
                $this->deletedFeeds->toArray());
        } else {
            if ($defaultType == "history") {
                $feeds = $this->watchedFeeds->toArray();
            } else if ($defaultType == "favorites") {
                $feeds = $this->favoriteFeeds->toArray();
            } else if ($defaultType == "liked") {
                $feeds = $this->likedFeeds->toArray();
            } else {
                $feeds = $this->deletedFeeds->toArray();
            }
        }
        /**
         * @var Feed $feed
         */
        foreach ($feeds as $feed) {
            $feedIds[] = ($useVideoUrls) ? $feed->getVideoId() : $feed->getId();
        }
        return $feedIds;
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
     * Set the account's deleted feeds.
     *
     * @param \Feed\Entity\Feed|Array $deletedFeeds
     */
    public function setDeletedFeeds($deletedFeeds)
    {
        $this->deletedFeeds[] = $deletedFeeds;
    }

    /**
     * Gets the account's deleted feeds.
     *
     * @param bool $paginated
     * @param null|\Game\Entity\Game $game
     * @return array|Paginator
     */
    public function getDeletedFeeds($paginated = false, $game = null)
    {
        $feeds = array();
        if ($game != null) {
            /**
             * @var Feed $feed
             */
            foreach ($this->deletedFeeds as $feed) {
                if ($feed->getGame()->getName() == $game->getName()) $feeds[] = $feed;
            }
        } else {
            $feeds = $this->deletedFeeds->toArray();
        }
        return ($paginated) ? new Paginator(new ArrayAdapter($feeds)) : $feeds;
    }

    /**
     * Add feed(s) to the account's deleted.
     *
     * @param \Feed\Entity\Feed|Array $deletedFeeds
     */
    public function addDeletedFeeds($deletedFeeds)
    {
        if (is_array($deletedFeeds)) {
            foreach ($deletedFeeds as $feed)
                $this->deletedFeeds->add($feed);
        } else {
            $this->deletedFeeds->add($deletedFeeds);
        }
    }

    /**
     * Remove feed(s) from the account's deleted.
     *
     * @param \Feed\Entity\Feed|Array $deletedFeeds
     */
    public function removeDeletedFeeds($deletedFeeds)
    {
        if (is_array($deletedFeeds)) {
            foreach ($deletedFeeds as $feed)
                $this->deletedFeeds->removeElement($feed);
        } else {
            $this->deletedFeeds->removeElement($deletedFeeds);
        }
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
    public function setFavoriteFeeds($favoriteFeeds)
    {
        $this->favoriteFeeds[] = $favoriteFeeds;
        return $this;
    }

    /**
     * Get the account's favorite feeds.
     *
     * @param bool $paginated
     * @param \Game\Entity\Game|null $game
     * @return ArrayCollection | Paginator
     */
    public function getFavoriteFeeds($paginated = false, $game = null)
    {
        $feeds = array();
        if ($game != null) {
            /**
             * @var Feed $feed
             */
            foreach ($this->favoriteFeeds as $feed) {
                if ($feed->getGame()->getName() == $game->getName()) $feeds[] = $feed;
            }
        } else {
            $feeds = $this->favoriteFeeds->toArray();
        }
        return ($paginated) ? new Paginator(new ArrayAdapter($feeds)) : $feeds;
    }

    /**
     * Add a feed to the account's favorites.
     *
     * @param array|\Feed\Entity\Feed $favoriteFeeds
     */
    public function addFavoriteFeeds($favoriteFeeds)
    {
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
    public function removeFavoriteFeeds($favoriteFeeds)
    {
        if (is_array($favoriteFeeds)) {
            foreach ($favoriteFeeds as $feed)
                $this->favoriteFeeds->removeElement($feed);
        } else {
            $this->favoriteFeeds->removeElement($favoriteFeeds);
        }
    }

    /**
     * Sets the account's feed queue.
     *
     */
    public function setFeedQueue($feedQueue)
    {
        $this->feedQueue[] = $feedQueue;
    }

    /**
     * Returns the account's feed queue.
     *
     * @param bool $paginated
     * @param null|\Game\Entity\Game $game
     * @return array|Paginator
     */
    public function getFeedQueue($paginated = false, $game = null)
    {
        $feeds = array();
        if ($game != null) {
            /**
             * @var Feed $feed
             */
            foreach ($this->feedQueue as $feed) {
                if ($feed->getGame()->getName() == $game->getName()) $feeds[] = $feed;
            }
        } else {
            $feeds = ($paginated) ? $this->feedQueue->toArray() : $this->feedQueue;
        }
        return ($paginated) ? new Paginator(new ArrayAdapter($feeds)) : $feeds;
    }

    /**
     * Add a feed to the account's queue.
     *
     * @param array|\Feed\Entity\Feed $feedQueue
     */
    public function addFeedQueue($feedQueue)
    {
        if (is_array($feedQueue)) {
            foreach ($feedQueue as $feed)
                $this->feedQueue->add($feed);
        } else {
            $this->feedQueue->add($feedQueue);
        }
    }

    /**
     * Remove a feed from the account's queue.
     *
     * @param array|\Feed\Entity\Feed $feedQueue
     */
    public function removeFeedQueue($feedQueue)
    {
        if (is_array($feedQueue)) {
            foreach ($feedQueue as $feed)
                $this->feedQueue->removeElement($feed);
        } else {
            $this->feedQueue->removeElement($feedQueue);
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
     * Sets the account's liked feeds.
     *
     * @param ArrayCollection $likedFeeds
     */
    public function setLikedFeeds($likedFeeds)
    {
        $this->likedFeeds[] = $likedFeeds;
    }

    /**
     * @param bool $paginated
     * @param null|\Game\Entity\Game $game
     * @return array|Paginator
     */
    public function getLikedFeeds($paginated = false, $game = null)
    {
        $feeds = array();
        if ($game != null) {
            /**
             * @var Feed $feed
             */
            foreach ($this->likedFeeds as $feed) {
                if ($feed->getGame()->getName() == $game->getName()) $feeds[] = $feed;
            }
        } else {
            $feeds = $this->likedFeeds->toArray();
        }
        return ($paginated) ? new Paginator(new ArrayAdapter($feeds)) : $feeds;
    }

    /**
     * Adds feed(s) to the account liked ones.
     *
     * @param array|\Feed\Entity\Feed $likedFeeds
     */
    public function addLikedFeeds($likedFeeds)
    {
        if (is_array($likedFeeds)) {
            foreach ($likedFeeds as $feed)
                $this->likedFeeds->add($feed);
        } else {
            $this->likedFeeds->add($likedFeeds);
        }
    }

    /**
     * Removes feed(s) from the account liked ones.
     *
     * @param array|\Feed\Entity\Feed $likedFeeds
     */
    public function removeLikedFeeds($likedFeeds)
    {
        if (is_array($likedFeeds)) {
            foreach ($likedFeeds as $feed)
                $this->likedFeeds->removeElement($feed);
        } else {
            $this->likedFeeds->removeElement($likedFeeds);
        }
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
     * @param array|\Application\Entity\Social $socials
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
    public function setWatchedFeeds($watchedFeeds)
    {
        $this->watchedFeeds[] = $watchedFeeds;
        return $this;
    }

    /**
     * Returns the account's feed history.
     *
     * @param bool $paginated
     * @param null|\Game\Entity\Game $game
     * @return array|Paginator
     */
    public function getWatchedFeeds($paginated = false, $game = null)
    {
        $feeds = array();
        if ($game != null) {
            /**
             * @var Feed $feed
             */
            foreach ($this->watchedFeeds as $feed) {
                if ($feed->getGame()->getName() == $game->getName()) $feeds[] = $feed;
            }
        } else {
            $feeds = $this->watchedFeeds->toArray();
        }
        return ($paginated) ? new Paginator(new ArrayAdapter($feeds)) : $feeds;
    }

    /**
     * Add a feed to the account's watched ones.
     *
     * @param array|Feed $watchedFeeds
     */
    public function addWatchedFeeds($watchedFeeds)
    {
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
    public function removeWatchedFeeds($watchedFeeds)
    {
        if (is_array($watchedFeeds)) {
            foreach ($watchedFeeds as $feed)
                $this->watchedFeeds->removeElement($feed);
        } else {
            $this->watchedFeeds->removeElement($watchedFeeds);
        }
    }


}
