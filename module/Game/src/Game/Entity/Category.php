<?php
namespace Game\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class Category
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
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Feed\Entity\Feed", mappedBy="category")
     */
    private $feeds;

    public function __construct(){
        $this->feeds = new ArrayCollection();
    }

    /**
     * @param mixed $feeds
     */
    public function setFeeds($feeds)
    {
        $this->feeds[] = $feeds;
    }

    /**
     * @return mixed
     */
    public function getFeeds()
    {
        return $this->feeds;
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
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


}