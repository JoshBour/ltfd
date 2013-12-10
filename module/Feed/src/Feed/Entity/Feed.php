<?php
namespace Feed\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
    public static $feedTypes = array('feeds', 'favorites', 'history', 'leet', 'deleted');

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="creation_time")
     */
    private $creationTime;

    /**
     * @ORM\ManyToMany(targetEntity="Account\Entity\Account", mappedBy="deletedFeeds")
     */
    private $deletedFeedsAccounts;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=200)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Account\Entity\Account", mappedBy="favoriteFeeds")
     */
    private $favoritedFeedsAccounts;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="BigInt")
     * @ORM\Column(length=20)
     * @ORM\Column(name="feed_id")
     */
    private $feedId;

    /**
     * @ORM\ManyToOne(targetEntity="Game\Entity\Game", inversedBy="feeds")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="game_id")
     */
    private $game;

    /**
     * @ORM\ManyToMany(targetEntity="Account\Entity\Account", mappedBy="likedFeeds")
     */
    private $likedFeedsAccounts;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     */
    private $rating;

    /**
     * @ORM\ManyToMany(targetEntity="Queue", mappedBy="feeds")
     */
    private $referencedQueues;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=100)
     * @ORM\Column(name="uploader_name")
     */
    private $uploaderName;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     */
    private $views;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=80)
     * @ORM\Column(name="video_id")
     */
    private $videoId;

    /**
     * @ORM\OneToMany(targetEntity="Account\Entity\AccountsHistory", mappedBy="feed")
     */
    private $watchedHistory;

    public static function createFromEntry($entry, $game)
    {
        $ytEntry = new \Feed\Model\YoutubeEntry($entry);
        $feed = new Feed();
        $feed->setUploaderName($ytEntry->getAuthor())
            ->setGame($game)
            ->setDescription(substr(self::filterData($ytEntry->getDescription()), 0, 160))
            ->setVideoId($ytEntry->getVideoId())
            ->setTitle(substr(self::filterData($ytEntry->getTitle()), 0, 40))
            ->setViews(0)
            ->setRating(0)
            ->setCreationTime(date("Y-m-d H:i:s", time()));
        return $feed;
    }

    private static function filterData($data)
    {
        $entities = new \Zend\Filter\HtmlEntities(array('quotestyle' => ENT_QUOTES));
        $tags = new \Zend\Filter\StripTags(array('quotestyle' => ENT_QUOTES));
        $newLine = new \Zend\Filter\StripNewlines(array('quotestyle' => ENT_QUOTES));
        $filters = array($entities, $tags, $newLine);
        foreach ($filters as $filter) {
            $data = $filter->filter($data);
        }
        return $data;
    }

    public function __construct()
    {
        $this->deletedFeedsAccounts = new ArrayCollection();
        $this->favoritedFeedsAccounts = new ArrayCollection();
        $this->watchedHistory = new ArrayCollection();
        $this->likedFeedsAccounts = new ArrayCollection();
        $this->referencedQueues = new ArrayCollection();
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
        $time = strtotime($this->creationTime);
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
     * Sets the feed uploader's name.
     *
     * @param String $uploaderName
     * @return Feed
     */
    public function setUploaderName($uploaderName)
    {
        $this->uploaderName = $uploaderName;
        return $this;
    }

    /**
     * Gets the feed uploader's name
     *
     * @return String
     */
    public function getUploaderName()
    {
        return $this->uploaderName;
    }

    /**
     * Sets the owners of the deleted feeds.
     *
     * @param ArrayCollection $deletedFeedsAccounts
     * @return Feed
     */
    public function setDeletedFeedsAccounts($deletedFeedsAccounts)
    {
        $this->deletedFeedsAccounts[] = $deletedFeedsAccounts;
        return $this;
    }

    /**
     * Returns the accounts of all the deleted feeds.
     *
     * @return mixed
     */
    public function getDeletedFeedsAccounts()
    {
        return $this->deletedFeedsAccounts;
    }

    /**
     * Sets the feed's description.
     *
     * @param String $description
     * @return Feed
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Gets the feed's description.
     *
     * @return String
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the owners of the favorited feeds.
     *
     * @param mixed $favoritedFeedsAccounts
     * @return Feed
     */
    public function setFavoritedFeedsAccounts($favoritedFeedsAccounts)
    {
        $this->favoritedFeedsAccounts[] = $favoritedFeedsAccounts;
        return $this;
    }

    /**
     * Returns the owners of the favorited feeds.
     *
     * @return mixed
     */
    public function getFavoritedFeedsAccounts()
    {
        return $this->favoritedFeedsAccounts;
    }

    /**
     * Sets the feed's game.
     *
     * @param \Game\Entity\Game $game
     * @return Feed
     */
    public function setGame($game)
    {
        $this->game = $game;
        return $this;
    }

    /**
     * Gets the feed's game.
     *
     * @return \Game\Entity\Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Sets the feed's unique id.
     *
     * @param int $feedId
     * @return Feed
     */
    public function setFeedId($feedId)
    {
        $this->feedId = $feedId;
        return $this;
    }

    /**
     * Gets the feed's unique id.
     *
     * @return int
     */
    public function getFeedId()
    {
        return $this->feedId;
    }

    /**
     * Sets the owners of the liked feeds.
     *
     * @param mixed $likedFeedsAccounts
     * @return Feed
     */
    public function setLikedFeedsAccounts($likedFeedsAccounts)
    {
        $this->likedFeedsAccounts[] = $likedFeedsAccounts;
        return $this;
    }

    /**
     * Gets the owners of the liked feeds.
     *
     * @return mixed
     */
    public function getLikedFeedsAccounts()
    {
        return $this->likedFeedsAccounts;
    }

    /**
     * Sets the feed's creation time.
     *
     * @param string $creationTime
     * @return Feed
     */
    public function setCreationTime($creationTime)
    {
        $this->creationTime = $creationTime;
        return $this;
    }

    /**
     * Gets the feed's creation time.
     *
     * @return string
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * Sets the queues in which the feed is referenced.
     *
     * @param mixed $referencedQueues
     * @return Feed
     */
    public function setReferencedQueues($referencedQueues)
    {
        $this->referencedQueues[] = $referencedQueues;
        return $this;
    }

    /**
     * Gets the queues in which the feed is referenced.
     *
     * @return ArrayCollection
     */
    public function getReferencedQueues()
    {
        return $this->referencedQueues;
    }

    /**
     * Sets the feed's rating.
     *
     * @param int $rating
     * @return Feed
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * Gets the feed's rating.
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Sets the feed's title.
     *
     * @param string $title
     * @return Feed
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Gets the feed's title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the feed's video id.
     *
     * @param string $videoId
     * @return Feed
     */
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
        return $this;
    }

    /**
     * Gets the feed's video id.
     *
     * @return string
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * Sets the feed's views.
     *
     * @param int $views
     * @return Feed
     */
    public function setViews($views)
    {
        $this->views = $views;
        return $this;
    }

    /**
     * Gets the feed's views.
     *
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Sets the owners of the watched feeds.
     *
     * @param mixed $watchedHistory
     * @return Feed
     */
    public function setWatchedHistory($watchedHistory)
    {
        $this->watchedHistory[] = $watchedHistory;
        return $this;
    }

    /**
     * Gets the owners of the watched feeds.
     *
     * @return mixed
     */
    public function getWatchedHistory()
    {
        return $this->watchedHistory;
    }


}