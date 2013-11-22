<?php
namespace Feed\Entity;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;
use Zend\Paginator\Paginator;
use ZendGData\YouTube;

/**
 * @ORM\Entity(repositoryClass="\Feed\Repository\FeedRepository")
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
     * @ORM\Column(type="integer")
     * @ORM\Column(length=80)
     * @ORM\Column(name="video_id")
     */
    private $videoId;

    /**
     * @ORM\ManyToOne(targetEntity="Game\Entity\Game", inversedBy="feeds")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=200)
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=100)
     */
    private $author;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     */
    private $views;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     */
    private $rating;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="post_time")
     */
    private $postTime;

    public static function createFromEntry($entry,$game){
        $ytEntry = new \Feed\Model\YoutubeEntry($entry);
        $feed = new Feed();
        $filter = new \Zend\Filter\HtmlEntities(array('quotestyle' => ENT_QUOTES));
        $feed->setAuthor($ytEntry->getAuthor())
            ->setGame($game)
            ->setDescription(substr(self::filterData($ytEntry->getDescription()), 0, 160))
            ->setVideoId($ytEntry->getVideoId())
            ->setTitle(substr(self::filterData($ytEntry->getTitle()),0,40))
            ->setViews(0)
            ->setRating(0)
            ->setPostTime(date("Y-m-d H:i:s", time()));
        return $feed;
    }

    private static function filterData($data){
        $entities = new \Zend\Filter\HtmlEntities(array('quotestyle' => ENT_QUOTES));
        $tags = new \Zend\Filter\StripTags(array('quotestyle' => ENT_QUOTES));
        $newLine = new \Zend\Filter\StripNewlines(array('quotestyle' => ENT_QUOTES));
        $filters = array($entities,$tags,$newLine);
        foreach($filters as $filter){
            $data = $filter->filter($data);
        }
        return $data;
    }

    /**
     * Get the youtube video entry of the feed.
     *
     * @return YouTube\VideoEntry
     */
    public function getYoutubeEntry()
    {
        $video = new Youtube();
        $adapter = new \Zend\Http\Client\Adapter\Curl();
        $adapter = $adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST, false);
        $adapter = $adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
        $httpClient = new \ZendGData\HttpClient();
        $httpClient->setAdapter($adapter);
        $video->setHttpClient($httpClient);
        return new \Feed\Model\YoutubeEntry($video->getVideoEntry($this->videoId));
    }

    /**
     * Get the time difference between the feed's post time and now.
     *
     * @return string
     */
    public function getTimeAgo()
    {
        $time = strtotime($this->postTime);
        $time = time() - $time; // to get the time since that moment

        $tokens = array(
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
            return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
        }
        return null;
    }

    /**
     * @param string $author
     * @return Feed
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $description
     * @return Feed
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \Game\Entity\Game $game
     * @return Feed
     */
    public function setGame($game)
    {
        $this->game = $game;
        return $this;
    }

    /**
     * @return \Game\Entity\Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param int $id
     * @return Feed
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $postTime
     * @return Feed
     */
    public function setPostTime($postTime)
    {
        $this->postTime = $postTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostTime()
    {
        return $this->postTime;
    }

    /**
     * @param int $rating
     * @return Feed
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param string $title
     * @return Feed
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $videoId
     * @return Feed
     */
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
        return $this;
    }

    /**
     * @return string
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * @param int $views
     * @return Feed
     */
    public function setViews($views)
    {
        $this->views = $views;
        return $this;
    }

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }


}