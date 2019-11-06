<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="prices",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="prices_type_place_unique", columns={"type", "place_id"})}
 * )
 */
class Price
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @Assert\Choice(choices={"less_than_12", "for_all"})
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @Assert\NotBlank
     * @Assert\Type("numeric")
     * @Assert\GreaterThanOrEqual(
     *     value = 0
     * )
     * @ORM\Column(type="float")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Place", inversedBy="prices")
     */
    private $place;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }
}
