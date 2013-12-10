<?php
namespace Game\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Game
 * @package Game\Entity
 * @ORM\Entity(repositoryClass="\Game\Repository\GameRepository")
 * @ORM\Table(name="games")
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="game_id")
     */
    private $gameId;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=75)
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     * @ORM\Column(name="url_name")
     */
    private $urlName;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=75)
     * @ORM\Column(name="company_name")
     */
    private $companyName;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=100)
     */
    private $website;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="BigInt")
     * @ORM\Column(length=20)
     * @ORM\Column(name="followers")
     */
    private $followersCount;

    /**
     * @ORM\ManyToMany(targetEntity="Account\Entity\Account", mappedBy="games")
     */
    private $followers;

    /**
     * @ORM\OneToMany(targetEntity="Feed\Entity\Feed", mappedBy="game")
     */
    private $feeds;

    /**
     * @ORM\OneToMany(targetEntity="Feed\Entity\Queue", mappedBy="game")
     */
    private $queues;

    public function __construct()
    {
        $this->followers = new ArrayCollection();
        $this->feeds = new ArrayCollection();
        $this->queues = new ArrayCollection();
    }

    /**
     * Returns the path to the game's image.
     *
     * @return string
     */
    public function getAvatar()
    {
        return 'games/' . strtolower(implode('', preg_split("/[\s,\'\:\-\!]+/", $this->name)));
    }

    /**
     * Sets the game's company name.
     *
     * @param string $companyName
     * @return Game
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
        return $this;
    }

    /**
     * Returns the game's company name.
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Sets the game's description.
     *
     * @param string $description
     * @return Game
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Returns the game's description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the game's feeds.
     *
     * @param array $feeds
     * @return Game
     */
    public function setFeeds($feeds)
    {
        $this->feeds[] = $feeds;
        return $this;
    }

    /**
     * Returns the game's feeds.
     *
     * @return ArrayCollection
     */
    public function getFeeds()
    {
        return $this->feeds;
    }

    /**
     * Adds feed(s) to the game's existing ones.
     * After insertion you need to persist the entity in order
     * for the changes to be saved to the database.
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
     * Removes feed(s) from the game's existing ones.
     * After removal you need to persist the entity in order
     * for the changes to be saved to the database.
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
     * Sets the game's followers.
     *
     * @param array $followers
     * @return Game
     */
    public function setFollowers($followers)
    {
        $this->followers[] = $followers;
        return $this;
    }

    /**
     * Returns the game's followers.
     *
     * @return ArrayCollection
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Adds follower(s) to the game's existing ones.
     * After insertion you need to persist the entity in order
     * for the changes to be saved to the database.
     *
     * @param $followers
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
     * Removes follower(s) from the game's existing ones.
     * After removal you need to persist the entity in order
     * for the changes to be saved to the database.
     *
     * @param array|\Account\Entity\Account $followers
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
     * Sets how many people are following the game.
     *
     * @param int $followersCount
     * @return Game
     */
    public function setFollowersCount($followersCount)
    {
        $this->followersCount = $followersCount;
        return $this;
    }

    /**
     * Returns how many people are following the game.
     *
     * @return int
     */
    public function getFollowersCount()
    {
        return $this->followersCount;
    }

    /**
     * Sets the game's id.
     *
     * @param int $gameId
     * @return Game
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
        return $this;
    }

    /**
     * Returns the game's id.
     *
     * @return int
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * Sets the game's name.
     *
     * @param string $name
     * @return Game
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the game's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the queues in which the game is referenced.
     *
     * @param \Feed\Entity\Queue $queues
     * @return Game
     */
    public function setQueues($queues)
    {
        $this->queues[] = $queues;
        return $this;
    }

    /**
     * Gets the queues in which the game is referenced.
     *
     * @return ArrayCollection
     */
    public function getQueues()
    {
        return $this->queues;
    }

    /**
     * Adds queue(s) to the ones in which the game is referenced.
     *
     * @param array|\Feed\Entity\Queue $queues
     */
    public function addQueues($queues)
    {
        if (is_array($queues)) {
            foreach ($queues as $queue)
                $this->queues->add($queue);
        }else{
            $this->queues->add($queues);
        }
    }

    /**
     * Removes queue(s) to the ones in which the game is referenced.
     *
     * @param array|\Feed\Entity\Queue $queues
     */
    public function deleteQueues($queues){
        if (is_array($queues)) {
            foreach ($queues as $queue)
                $this->queues->removeElement($queue);
        }else{
            $this->queues->removeElement($queues);
        }
    }

    /**
     * Sets the game's url name.
     *
     * @param string $urlName
     * @return Game
     */
    public function setUrlName($urlName)
    {
        $this->urlName = $urlName;
        return $this;
    }

    /**
     * Returns the game's url name.
     *
     * @return string
     */
    public function getUrlName()
    {
        return $this->urlName;
    }

    /**
     * Sets the game's website.
     *
     * @param string $website
     * @return Game
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * Returns the game's website.
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }


}