<?php
namespace Account\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Filter\Null;
use Feed\Entity\Feed;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

/**
 * Class Account
 * @package Account\Entity
 * @ORM\Entity(repositoryClass="\Account\Repository\AccountRepository")
 * @ORM\Table(name="accounts")
 */
class Account
{

    const CACHE_RATING_KEY = 'account-ratings-';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="BigInt")
     * @ORM\Column(length=20)
     * @ORM\Column(name="account_id")
     */
    private $accountId;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     */
    private $avatar;

    /**
     * @ORM\ManyToMany(targetEntity="Feed\Entity\Feed", inversedBy="deletedFeedsAccounts")
     * @ORM\JoinTable(name="accounts_feeds_deleted",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="account_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")}
     *      )
     */
    private $deletedFeeds;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=255)
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity="Feed\Entity\Feed", inversedBy="favoritedFeedsAccounts")
     * @ORM\JoinTable(name="accounts_feeds_favorites",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="account_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")}
     *      )
     */
    private $favoriteFeeds;

    /**
     * @ORM\ManyToMany(targetEntity="Account\Entity\Account", mappedBy="following")
     */
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity="Account\Entity\Account", inversedBy="followers")
     * @ORM\JoinTable(name="followers",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="account_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="follower_id", referencedColumnName="account_id")}
     *      )
     */
    private $following;

    /**
     * @ORM\ManyToMany(targetEntity="Game\Entity\Game", inversedBy="followers")
     * @ORM\JoinTable(name="accounts_games",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="account_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="game_id")}
     *      )
     */
    private $games;

    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="accounts")
     * @ORM\JoinTable(name="accounts_groups",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="account_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="group_id")}
     *      )
     */
    private $groups;

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
     * @ORM\Column(type="timestamp")
     * @ORM\Column(name="last_seen")
     */
    private $lastSeen;

    /**
     * @ORM\ManyToMany(targetEntity="Feed\Entity\Feed", inversedBy="likedFeedsAccounts")
     * @ORM\JoinTable(name="accounts_feeds_liked",
     *      joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="account_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")}
     *      )
     */
    private $likedFeeds;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=128)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="Feed\Entity\Queue", mappedBy="account")
     */
    private $queues;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="register_date")
     */
    private $registerDate;

    /**
     * @ORM\OneToMany(targetEntity="Account\Entity\AccountsSocials", mappedBy="account")
     */
    private $socials;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=15)
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity="Account\Entity\AccountsHistory", mappedBy="account")
     */
    private $watchedFeeds;

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
     * @param Account $account
     * @param string $password
     * @return bool
     */
    public static function hashPassword($account, $password)
    {
        return ($account->getPassword() === crypt($password . 'leetfeedpenbour', $account->getPassword()));
    }

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
        $this->likedFeeds = new ArrayCollection();
        $this->queues = new ArrayCollection();
        $this->watchedFeeds = new ArrayCollection();
    }

    public function hasInteractedWithFeed(Feed $feed)
    {
        return (in_array($feed->getVideoId(), $this->getInteractedFeeds(true, null, true)));
    }

    public function hasFavorite($feedId)
    {
        $feedFavoriteArray = $this->getInteractedFeeds(false, 'favorites');
        return in_array($feedId, $feedFavoriteArray);
    }

    public function hasLiked($feedId)
    {
        $feedFavoriteArray = $this->getInteractedFeeds(false, 'liked');
        return in_array($feedId, $feedFavoriteArray);
    }

    /**
     * Returns a merged array containing all the feeds or a specific type of feed the user has interacted with.
     *
     * @param bool $merged If true, it will return all the feeds merged into one array.
     * @param string $type If $merged is false, it will return an array with feeds related to the $type
     * @param bool $includeIds If true, it will return an array with the feed ids instead if the whole feed.
     * @param bool $useVideoUrls If true and $includeIds is true, it will use the video ids instead.
     * @return array|ArrayCollection|null
     */
    public function getInteractedFeeds($merged = false, $type = "history", $includeIds = false, $useVideoUrls = false)
    {
        if ($merged) {
            $feeds = array_merge(
                $this->watchedFeeds->toArray(),
                $this->favoriteFeeds->toArray(),
                $this->likedFeeds->toArray(),
                $this->deletedFeeds->toArray()
            );
        } else {
            if (!in_array($type, Feed::$feedTypes)) return null;
            switch ($type) {
                case "history":
                    $feeds = $this->watchedFeeds->toArray();
                    break;
                case "favorites":
                    $feeds = $this->favoriteFeeds->toArray();
                    break;
                case "leet":
                    $feeds = $this->likedFeeds->toArray();
                    break;
                case "deleted":
                    $feeds = $this->deletedFeeds->toArray();
                    break;
                default:
                    return null;
            }
        }
        if ($includeIds) {
            /**
             * @var Feed $feed
             */
            $feedIds = array();
            foreach ($feeds as $feed) {
                $feedIds[] = ($useVideoUrls) ? $feed->getVideoId() : $feed->getFeedId();
            }
            return $feedIds;
        }
        return $feeds;
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
            $avatarName = 'users/' . $this->accountId . '/user-default-' . $dimensionX . 'x' . $dimensionY . '.' . $extension['extension'];
            return $avatarName;
        }
    }

    /**
     * Set the account's id.
     *
     * @param int $accountId
     * @return Account
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * Get the account's id.
     *
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
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
     * Gets the account's liked feeds.
     *
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
     * Set the account's queues.
     *
     * @param \Feed\Entity\Queue $queues
     * @return $this
     */
    public function setQueues($queues){
        $this->queues[] = $queues;
        return $this;
    }

    /**
     * Get the account's queues.
     *
     * @return ArrayCollection
     */
    public function getQueues(){
        return $this->queues;
    }

    /**
     * Add queue(s) to the account's existing ones.
     *
     * @param array|\Feed\Entity\Queue $queues
     */
    public function addQueues($queues){
        if(is_array($queues)){
            foreach($queues as $queue){
                $this->queues->add($queue);
            }
        }else{
            $this->queues->add($queues);
        }
    }

    /**
     * Remove queue(s) from the account's existing ones.
     *
     * @param array|\Feed\Entity\Queue $queues
     */
    public function removeQueues($queues){
        if(is_array($queues)){
            foreach($queues as $queue){
                $this->queues->removeElement($queue);
            }
        }else{
            $this->queues->removeElement($queues);
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
