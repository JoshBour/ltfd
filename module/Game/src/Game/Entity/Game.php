<?php
namespace Game\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
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
     */
    private $id;

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
     */
    private $company;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=75)
     */
    private $website;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="followers")
     */
    private $followersCount;

    /**
     * @ORM\ManyToMany(targetEntity="Account\Entity\Account")
     * @ORM\JoinTable(name="games_followers",
     *      joinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="follower_id", referencedColumnName="id")}
     *      )
     */
    private $followers;

    /**
     * @ORM\OneToMany(targetEntity="Feed\Entity\Feed", mappedBy="game")
     */
    private $feeds;

    public function __construct()
    {
        $this->followers = new ArrayCollection();
        $this->feeds = new ArrayCollection();
    }

    /**
     * Returns the path to the game's image.
     *
     * @return string
     */
    public function getAvatar()
    {
        return 'games/' . strtolower(implode('', preg_split("/[\s,\:\-\!]+/", $this->name)));
    }

    /**
     * Sets the game's company name.
     *
     * @param string $company
     * @return Game
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Returns the game's company name.
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
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
     * @param int $id
     * @return Game
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the game's id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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