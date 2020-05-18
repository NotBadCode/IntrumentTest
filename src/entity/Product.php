<?php

namespace src\entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @ORM\Entity(repositoryClass="src\repository\ProductRepository")
 * @ORM\Table(name="product")
 *
 * Class Product
 * @package src\entity
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @ORM\Column(type="decimal")
     *
     * @var number
     */
    protected $price;

    /**
     * @ORM\ManyToMany(targetEntity="Order", mappedBy="orders")
     */
    protected $orders;


    /**
     * Default constructor, initializes collections
     */
    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    /**
     * @param Order $order
     */
    public function addOrder(Order $order)
    {
        if ($this->orders->contains($order)) {
            return;
        }

        $this->orders->add($order);
    }

    /**
     * @param Order $order
     */
    public function removeUser(Order $order)
    {
        if (!$this->orders->contains($order)) {
            return;
        }

        $this->orders->removeElement($order);
        $order->removeProduct($this);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return number
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param number $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }
}