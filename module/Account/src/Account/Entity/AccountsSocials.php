<?php
namespace Account\Entity;

use Doctrine\ORM\Mapping as ORM;
use Account\Entity\Account;

/**
 * @ORM\Entity
 * @ORM\Table(name="accounts_socials")
 */
class AccountsSocials
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @ORM\Column(length=11)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="socials")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     **/
    private $account;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Social", inversedBy="accounts")
     * @ORM\JoinColumn(name="social_id", referencedColumnName="id")
     **/
    private $social;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=100)
     */
    private $value;

    /**
     * Set the join entry's account.
     *
     * @param Account $account
     * @return AccountsSocials
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Get the join entry's account.
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set the join entry's id.
     *
     * @param int $id
     * @return AccountsSocials
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the join entry's id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the join entry's social.
     *
     * @param \Application\Entity\Social $social
     * @return AccountsSocials
     */
    public function setSocial($social)
    {
        $this->social = $social;
        return $this;
    }

    /**
     * Get the join entry's social.
     *
     * @return \Application\Entity\Social
     */
    public function getSocial()
    {
        return $this->social;
    }

    /**
     * Set the join entry's value.
     *
     * @param string $value
     * @return AccountsSocials
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get the join entry's value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }




}
