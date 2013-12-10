<?php
namespace Account\Entity;

use Doctrine\ORM\Mapping as ORM;
use Account\Entity\Account;

/**
 * Class AccountsHistory
 * @package Account\Entity
 * @ORM\Entity
 * @ORM\Table(name="accounts_feeds_history")
 */
class AccountsHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="BigInt")
     * @ORM\Column(length=20)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="watchedFeeds")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id")
     **/
    private $account;

    /**
     * @ORM\ManyToOne(targetEntity="Feed\Entity\Feed", inversedBy="watchedHistory")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="feed_id")
     **/
    private $feed;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=100)
     * @ORM\Column(name="view_time")
     */
    private $viewTime;

    /**
     * Sets the join entry's account.
     *
     * @param Account $account
     * @return AccountsHistory
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Gets the join entry's account.
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Sets the join entry's feed.
     *
     * @param \Feed\Entity\Feed $feed
     * @return AccountsHistory
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
        return $this;
    }

    /**
     * Gets the join entry's feed.
     *
     * @return \Feed\Entity\Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Sets the join entry's id.
     *
     * @param int $id
     * @return AccountsHistory
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Gets the join entry's id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the join entry's view time.
     *
     * @param string $viewTime
     * @return AccountsHistory
     */
    public function setViewTime($viewTime)
    {
        $this->viewTime = $viewTime;
        return $this;
    }

    /**
     * Gets the join entry's view time.
     *
     * @return string
     */
    public function getViewTime()
    {
        return $this->viewTime;
    }



}
