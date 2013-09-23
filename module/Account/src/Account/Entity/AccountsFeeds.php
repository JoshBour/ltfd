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
     * @param mixed $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
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


}
