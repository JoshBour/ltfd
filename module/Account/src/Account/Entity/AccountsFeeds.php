<?php
namespace Account\Entity;

use Doctrine\ORM\Mapping as ORM;
use Account\Entity\Account;

/**
 * @ORM\Entity
 * @ORM\Table(name="accounts_feeds")
 */
class AccountsFeeds
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="categorizedFeeds")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     **/
    private $account;

    /**
     * @ORM\ManyToOne(targetEntity="Feed\Entity\Feed", inversedBy="categorizedFeeds")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id")
     **/
    private $feed;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=50)
     */
    private $category;

    public function __construct($account,$feed,$category){
        $this->account = $account;
        $this->feed = $feed;
        $this->category = $category;
    }

    /**
     * Set the feed's owner account.
     *
     * @param Account $account
     * @return AccountsFeeds
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Get the feed's owner account.
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set the feed's category.
     *
     * @param \Game\Entity\Category $category
     * @return AccountsFeeds
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
     * Set the corresponding feed.
     *
     * @param \Feed\Entity\Feed $feed
     * @return AccountsFeeds
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
        return $this;
    }

    /**
     * Get the corresponding feed.
     *
     * @return \Feed\Entity\Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Set the join table's entry id.
     *
     * @param int $id
     * @return AccountsFeeds
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the join table's entry id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


}
