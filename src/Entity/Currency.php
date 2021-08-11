<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CurrencyRepository::class)
 */
class Currency
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $id_currency;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=ExchangeRate::class, mappedBy="id_currency", orphanRemoval=true)
     */
    private $exchangeRates;

    public function __construct()
    {
        $this->exchangeRates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCurrency(): ?string
    {
        return $this->id_currency;
    }

    public function setIdCurrency(string $id_currency): self
    {
        $this->id_currency = $id_currency;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|ExchangeRate[]
     */
    public function getExchangeRates(): Collection
    {
        return $this->exchangeRates;
    }

    public function addExchangeRate(ExchangeRate $exchangeRate): self
    {
        if (!$this->exchangeRates->contains($exchangeRate)) {
            $this->exchangeRates[] = $exchangeRate;
            $exchangeRate->setIdCurrency($this);
        }

        return $this;
    }

    public function removeExchangeRate(ExchangeRate $exchangeRate): self
    {
        if ($this->exchangeRates->removeElement($exchangeRate)) {
            // set the owning side to null (unless already changed)
            if ($exchangeRate->getIdCurrency() === $this) {
                $exchangeRate->setIdCurrency(null);
            }
        }

        return $this;
    }
}

