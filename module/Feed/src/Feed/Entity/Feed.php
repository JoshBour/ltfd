<?php
namespace Feed\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="feeds")
 */
class Feed
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Account\Entity\Account")
     * @ORM\JoinColumn(name="uploader_id", referencedColumnName="id")
     */
    private $uploader;

    /**
     * @ORM\OneToOne(targetEntity="Game\Entity\Game")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;

    /**
     * @ORM\OneToOne(targetEntity="Game\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="post_time")
     */
    private $postTime;

    /**
     * @ORM\OneToMany(targetEntity="Feed\Entity\Rating", mappedBy="feed")
     */
    private $ratings;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
    }

    /**
     * @param mixed $ratings
     */
    public function setRatings($ratings)
    {
        $this->ratings[] = $ratings;
    }

    /**
     * @return mixed
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    public function addRatings($ratings)
    {
        foreach ($ratings as $rating)
            $this->ratings->add($rating);
    }

    public function removeRatings($ratings)
    {
        foreach ($ratings as $rating)
            $this->ratings->removeElement($rating);
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }

    /**
     * @return mixed
     */
    public function getGame()
    {
        return $this->game;
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
     * @param mixed $postTime
     */
    public function setPostTime($postTime)
    {
        $this->postTime = $postTime;
    }

    /**
     * @return mixed
     */
    public function getPostTime()
    {
        return $this->postTime;
    }

    /**
     * @param mixed $uploader
     */
    public function setUploader($uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @return mixed
     */
    public function getUploader()
    {
        return $this->uploader;
    }


}