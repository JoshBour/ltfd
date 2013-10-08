<?php
namespace Feed\Entity;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineModule\Paginator\Adapter\Collection as CollectionAdapter;
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
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     * @ORM\Column(nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     * @ORM\Column(nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     */
    private $rating;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="video_id")
     */
    private $videoId;

    /**
     * @ORM\ManyToOne(targetEntity="Account\Entity\Account", inversedBy="feeds")
     * @ORM\JoinColumn(name="uploader_id", referencedColumnName="id")
     */
    private $uploader;

    /**
     * @ORM\ManyToOne(targetEntity="Game\Entity\Game", inversedBy="feeds")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="Game\Entity\Category", inversedBy="feeds")
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

    /**
     * @ORM\OneToMany(targetEntity="\Account\Entity\AccountsFeeds", mappedBy="feed")
     */
    private $categorizedFeeds;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="feed")
     * @ORM\OrderBy({"postTime" = "DESC"})
     */
    private $comments;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @param $entity \Feed\Entity\Feed
     * @param $user \Account\Entity\Account
     * @param $videoUrl int
     * @return Feed
     * @throws \Doctrine\Common\Proxy\Exception\InvalidArgumentException
     */
    public static function create($entity,$user,$videoUrl){
        if($entity instanceof Feed){
            parse_str( parse_url( $videoUrl, PHP_URL_QUERY ), $varArray );
            $entity->setUploader($user);
            $entity->setPostTime(date('Y-m-d H:i:s'));
            $entity->setVideoId($varArray['v']);
            $entity->setRating(0);
            $title = $entity->getTitle();
            $description = $entity->getDescription();
            if(empty($title)){
                $entity->setTitle(null);
            }
            if(empty($description)){
                $entity->setDescription(null);
            }
            return $entity;
        }else{
            throw new InvalidArgumentException('The provided arguments are invalid');
        }
    }

    public function getYoutubeEntry(){
        $video = new Youtube();
        $adapter = new \Zend\Http\Client\Adapter\Curl();
        $adapter = $adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST,false);
        $adapter = $adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER,false);
        $httpClient = new \ZendGData\HttpClient();
        $httpClient->setAdapter($adapter);
        $video->setHttpClient($httpClient);
        return $video->getVideoEntry($this->videoId);
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }


    public function getTotalRating(){
        $ratingArray = $this->getRatingArray();
        $sum = $ratingArray['up'] - $ratingArray['down'];

        if(abs($sum) > 1000){
            $k = substr($sum,0,1);
            $decimal = substr($sum,1,1);
            $sum = $k . ',' . $decimal . 'k';
        }
        return $sum;
    }

    public function getRatingArray(){
        $ratings = array('up' => 0,'down' => 0);
        foreach($this->ratings as $rating){
            if($rating->getRating() == '1'){
                $ratings['up']++;
            }else{
                $ratings['down']++;
            }
        }
        return $ratings;
    }

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
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * @param mixed $categorizedFeeds
     */
    public function setCategorizedFeeds($categorizedFeeds)
    {
        $this->categorizedFeeds = $categorizedFeeds;
    }

    /**
     * @return mixed
     */
    public function getCategorizedFeeds()
    {
        return $this->categorizedFeeds;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments[] = $comments;
    }

    /**
     * @return Paginator
     */
    public function getComments()
    {
        $adapter = new CollectionAdapter($this->comments);
        return new Paginator($adapter);
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
     * @param mixed $videoId
     */
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
    }

    /**
     * @return mixed
     */
    public function getVideoId()
    {
        return $this->videoId;
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