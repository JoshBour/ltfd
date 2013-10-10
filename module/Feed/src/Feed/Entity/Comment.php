<?php
namespace Feed\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="comments")
 */
class Comment
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
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @ORM\OneToOne(targetEntity="Feed\Entity\Feed")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id")
     */
    private $feed;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="post_time")
     */
    private $postTime;

    /**
     * Get the time difference between the comment's post time and now.
     *
     * @return string
     */
    public function getTimeAgo(){
        $time = strtotime($this->postTime);
        $time = time() - $time; // to get the time since that moment

        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }
        return null;
    }

    /**
     * Set the comment's author account.
     *
     * @param \Account\Entity\Account $author
     * @return Comment
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Get the comment's author account.
     *
     * @return \Account\Entity\Account
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set the comment's content.
     *
     * @param string $content
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get the comment's content.
     *
     * @return string mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the comment's related feed.
     *
     * @param Feed $feed
     * @return Comment
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
        return $this;
    }

    /**
     * Get the comment's related feed.
     *
     * @return Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Set the comment's id.
     *
     * @param int $id
     * @return Comment
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the comment's id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the comment's post time.
     * Accepted format is Y-m-d H:i:s.
     *
     * @param string $postTime
     * @return Comment
     */
    public function setPostTime($postTime)
    {
        $this->postTime = $postTime;
        return $this;
    }

    /**
     * Get the comment's post time.
     * Format is Y-m-d H:i:s.
     *
     * @return string
     */
    public function getPostTime()
    {
        return $this->postTime;
    }



}