<?php
namespace Feed\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="feeds_ratings")
 */
class Rating
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Feed\Entity\Feed")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id")
     */
    private $feed;

    /**
     * @ORM\OneToOne(targetEntity="Account\Entity\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="smallint")
     */
    private $rating;

    public function __construct($user, $feed, $rating){
        $this->user = $user;
        $this->feed = $feed;
        $this->rating = $rating;
    }

    /**
     * Set the rating's related feed.
     *
     * @param Feed $feed
     * @return Rating
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
        return $this;
    }

    /**
     * Get the rating's related feed.
     *
     * @return Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Set the rating's id.
     *
     * @param int $id
     * @return Rating
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the rating's id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the rating's value.
     * 1 stands for like and 0 for dislike.
     *
     * @param int $rating
     * @return Rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * Get the rating's value.
     * 1 stands for like and 0 for dislike.
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set the rating's author account.
     *
     * @param \Account\Entity\Account $user
     * @return Rating
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get the rating's author account.
     *
     * @return \Account\Entity\Account
     */
    public function getUser()
    {
        return $this->user;
    }


}