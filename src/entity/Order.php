<?php

namespace src\entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @ORM\Entity(repositoryClass="src\repository\OrderRepository")
 * @ORM\Table(name="`order`")
 *
 * Class Order
 * @package src\entity
 */
class Order
{
    public const STATUS_NEW   = 'new';
    public const STATUS_PAYED = 'payed';

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
    protected $status;

    /**
     *
     * @ORM\Column(type="decimal")
     *
     * @var number
     */
    protected $sumTotal;

    /**
     * @ORM\ManyToMany(targetEntity="Product")
     * @JoinTable(name="order_product")
     */
    protected $products;

    /**
     * Default constructor, initializes collections
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * @param Product $product
     */
    public function addProduct(Product $product)
    {
        if ($this->products->contains($product)) {
            return;
        }

        $this->products->add($product);
    }

    /**
     * @param Product $product
     */
    public function removeProduct(Product $product)
    {
        if (!$this->products->contains($product)) {
            return;
        }

        $this->products->removeElement($product);
        $product->removeUser($this);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return number
     */
    public function getSumTotal()
    {
        return $this->sumTotal;
    }

    /**
     * @param number $sumTotal
     */
    public function setSumTotal($sumTotal)
    {
        $this->sumTotal = $sumTotal;
    }

}