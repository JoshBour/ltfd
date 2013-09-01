<?php
namespace Game\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="\Game\Repository\GameRepository")
 * @ORM\Table(name="games")
 */
class Game
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
     * @ORM\Column(length=75)
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=75)
     */
    private $company;

    /**
     * @ORM\Column(type="string")
     * @ORM\Column(length=75)
     */
    private $website;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Account\Entity\Account")
     * @ORM\JoinTable(name="games_followers",
     *      joinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="follower_id", referencedColumnName="id")}
     *      )
     */
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity="Game\Entity\Category")
     * @ORM\JoinTable(name="games_categories",
     *      joinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     */
    private $categories;

    public function __construct(){
        $this->followers = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getAvatar(){
        return '/games/' . strtolower(implode('',preg_split("/[\s,\:\-\!]+/", $this->name)));
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories)
    {
        $this->categories[] = $categories;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    public function addCategories($categories){
        foreach($categories as $category)
            $this->categories->add($category);
    }

    public function removeCategories($categories){
        foreach($categories as $category)
            $this->categories->removeElement($category);
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $followers
     */
    public function setFollowers($followers)
    {
        $this->followers[] = $followers;
    }

    /**
     * @return mixed
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    public function addFollowers($followers){
        foreach($followers as $follower)
            $this->followers->add($follower);
    }

    public function removeFollowers($followers){
        foreach($followers as $follower)
            $this->followers->removeElement($follower);
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

    /**
     * @param mixed $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }


}