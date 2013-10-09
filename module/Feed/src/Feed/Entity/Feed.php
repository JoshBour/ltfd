<?php
namespace Feed\Entity;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="feed")
     * @ORM\OrderBy({"postTime" = "DESC"})
     */
    private $comments;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->watchedFeeds = new ArrayCollection();
        $this->favoriteFeeds = new ArrayCollection();
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

    /**
     * Get the youtube video entry of the feed.
     *
     * @return YouTube\VideoEntry
     */
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
     * Get the total rating in a 'k' format.
     *
     * @return string
     */
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

    /**
     * Returns an array containing the down and up votes.
     * Up votes : $ratings['up']
     * Down votes : $ratings['down']
     *
     * @return array
     */
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

    /**
     * Get the time difference from now.
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
     * Set the feed's category.
     *
     * @param $category
     * @return Feed
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get the feed's category.
     *
     * @return \Game\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the feed's comments.
     *
     * @param $comments
     * @return Feed
     */
    public function setComments($comments)
    {
        $this->comments[] = $comments;
        return $this;
    }

    /**
     * Get the feed's comments.
     *
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add comment(s) to the existing ones.
     *
     * @param array|Comment $comments
     */
    public function addComments($comments){
        if (is_array($comments)) {
            foreach ($comments as $comment)
                $this->comments->add($comment);
        } else {
            $this->comments->add($comments);
        }
    }

    /**
     * Remove comment(s) from the existing ones.
     *
     * @param array|Comment $comments
     */
    public function removeComments($comments){
        if (is_array($comments)) {
            foreach ($comments as $comment)
                $this->comments->removeElement($comment);
        } else {
            $this->comments->removeElement($comments);
        }
    }

    /**
     * Set the feed's description.
     *
     * @param string $description
     * @return Feed
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the feed's description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the feed's game.
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
     * Get the feed's game.
     *
     * @return \Game\Entity\Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set the feed's id.
     *
     * @param int $id
     * @return Feed
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the feed's id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the feed's post time.
     * Accepted format is Y-m-d H:i:s
     *
     * @param string $postTime
     * @return Feed
     */
    public function setPostTime($postTime)
    {
        $this->postTime = $postTime;
        return $this;
    }

    /**
     * Get the feed's post time.
     * The format is Y-m-d H:i:s
     *
     * @return string
     */
    public function getPostTime()
    {
        return $this->postTime;
    }

    /**
     * Set the feed's total rating.
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
     * Get the feed's total rating.
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set the feed's ratings.
     *
     * @param $ratings
     * @return Feed
     */
    public function setRatings($ratings)
    {
        $this->ratings[] = $ratings;
        return $this;
    }

    /**
     * Get the feed's ratings.
     *
     * @return ArrayCollection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * Add rating(s) to the existing ones.
     *
     * @param array|Rating $ratings
     */
    public function addRatings($ratings){
        if (is_array($ratings)) {
            foreach ($ratings as $rating)
                $this->ratings->add($rating);
        } else {
            $this->ratings->add($ratings);
        }
    }

    /**
     * Remove rating(s) from the existing ones.
     *
     * @param array|Rating $ratings
     */
    public function removeRatings($ratings){
        if (is_array($ratings)) {
            foreach ($ratings as $rating)
                $this->ratings->removeElement($rating);
        } else {
            $this->ratings->removeElement($ratings);
        }
    }

    /**
     * Set the feed's title.
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
     * Get the feed's title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the feed's uploader account.
     *
     * @param \Account\Entity\Account $uploader
     * @return Feed
     */
    public function setUploader($uploader)
    {
        $this->uploader = $uploader;
        return $this;
    }

    /**
     * Get the feed's uploader account.
     *
     * @return \Account\Entity\Account
     */
    public function getUploader()
    {
        return $this->uploader;
    }

    /**
     * Set the video's youtube id.
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
     * Get the video's youtube id.
     *
     * @return string
     */
    public function getVideoId()
    {
        return $this->videoId;
    }


}