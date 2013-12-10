<?php
/**
 * User: Josh
 * Date: 9/12/2013
 * Time: 5:05 μμ
 */

namespace Feed\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Account\Entity\Account;
use Game\Entity\Game;

/**
 * Class Queue
 * @package Feed\Entity
 * @ORM\Entity
 * @ORM\Table(name="queues")
 */
class Queue {

    /**
     * @ORM\ManyToOne(targetEntity="Account\Entity\Account", inversedBy="queues")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id")
     */
    private $account;

    /**
     * @ORM\ManyToMany(targetEntity="Feed", inversedBy="referencedQueues")
     * @ORM\JoinTable(name="queues_feeds",
     *      joinColumns={@ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="queue_id", referencedColumnName="queue_id")}
     *      )
     */
    private $feeds;

    /**
     * @ORM\ManyToOne(targetEntity="Game\Entity\Game", inversedBy="queues")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="game_id")
     */
    private $game;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     * @ORM\Column(name="last_index")
     */
    private $lastIndex;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\Column(name="last_updated_time")
     */
    private $lastUpdatedTime;

    /**
     * @ORM\Id
     * @ORM\Column(type="BigInt")
     * @ORM\Column(length=20)
     * @ORM\Column(name="queue_id")
     */
    private $queueId;

    public function __construct(){
        $this->feeds = new ArrayCollection();
    }

    /**
     * Sets the queue's Account.
     *
     * @param Account $account
     * @return Queue
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Gets the queue's Account
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Sets the queue's feeds.
     *
     * @param Feed $feeds
     * @return Queue
     */
    public function setFeeds($feeds){
        $this->feeds[] = $feeds;
        return $this;
    }

    /**
     * Gets the queue's feeds.
     *
     * @return ArrayCollection
     */
    public function getFeeds(){
        return $this->feeds;
    }

    /**
     * Adds feed(s) to the queue's existing ones.
     *
     * @param array|Feed $feeds
     */
    public function addFeeds($feeds){
        if(is_array($feeds)){
            foreach($feeds as $feed)
                $this->feeds->add($feed);
        }else{
            $this->feeds->add($feeds);
        }
    }

    /**
     * Removes feed(s) from the queue's existing ones
     *
     * @param array|Feed $feeds
     */
    public function removeFeeds($feeds){
        if(is_array($feeds)){
            foreach($feeds as $feed)
                $this->feeds->removeElement($feed);
        }else{
            $this->feeds->removeElement($feeds);
        }
    }

    /**
     * Sets the queue's game.
     *
     * @param Game $game
     * @return Queue
     */
    public function setGame($game)
    {
        $this->game = $game;
        return $this;
    }

    /**
     * Gets the queue's game.
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Sets the queue's last searched index.
     *
     * @param int $lastIndex
     * @return Queue
     */
    public function setLastIndex($lastIndex)
    {
        $this->lastIndex = $lastIndex;
        return $this;
    }

    /**
     * Gets the queue's last searched index.
     *
     * @return int
     */
    public function getLastIndex()
    {
        return $this->lastIndex;
    }

    /**
     * Sets the queue's last update time.
     *
     * @param string $lastUpdatedTime
     * @return Queue
     */
    public function setLastUpdatedTime($lastUpdatedTime)
    {
        $this->lastUpdatedTime = $lastUpdatedTime;
        return $this;
    }

    /**
     * Gets the queue's last update time.
     *
     * @return string
     */
    public function getLastUpdatedTime()
    {
        return $this->lastUpdatedTime;
    }

    /**
     * Sets the queue's unique id.
     *
     * @param int $queueId
     * @return Queue
     */
    public function setQueueId($queueId)
    {
        $this->queueId = $queueId;
        return $this;
    }

    /**
     * Gets the queue's unique id.
     *
     * @return int
     */
    public function getQueueId()
    {
        return $this->queueId;
    }


}