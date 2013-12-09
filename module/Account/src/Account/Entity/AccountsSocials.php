<?php
namespace Account\Entity;

use Doctrine\ORM\Mapping as ORM;
use Account\Entity\Account;

/**
 * Class AccountsSocials
 * @package Account\Entity
 * @ORM\Entity
 * @ORM\Table(name="accounts_socials")
 */
class AccountsSocials
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="socials")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     **/
    private $account;

    /**
     * @ORM\Id
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
     * Sets the join entry's account.
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
     * Gets the join entry's account.
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Sets the join entry's social.
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
     * Gets the join entry's social.
     *
     * @return \Application\Entity\Social
     */
    public function getSocial()
    {
        return $this->social;
    }

    /**
     * Sets the join entry's value.
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
     * Gets the join entry's value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }




}
