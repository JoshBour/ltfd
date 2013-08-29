<?php
namespace Account\Entity;

use Doctrine\ORM\Mapping as ORM;
use Account\Entity\Account;

/**
 * @ORM\Entity
 * @ORM\Table(name="accounts_socials")
 */
class AccountSocial
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

    /**
     * @param mixed $social
     */
    public function setSocial($social)
    {
        $this->social = $social;
    }

    /**
     * @return mixed
     */
    public function getSocial()
    {
        return $this->social;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }




}
