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
     * @param mixed $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $feed
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
    }

    /**
     * @return mixed
     */
    public function getFeed()
    {
        return $this->feed;
    }


}