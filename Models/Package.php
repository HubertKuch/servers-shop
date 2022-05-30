<?php

namespace Servers\Models;

use Avocado\ORM\Attributes\Field;
use Avocado\ORM\Attributes\Table;
use Avocado\ORM\Attributes\Id;

#[Table('package')]
class Package {
    #[Id]
    private int $id;
    #[Field]
    private string $name;
    #[Field("ram_size")]
    private int $ramSize;
    #[Field("disk_size")]
    private int $diskSize;
    #[Field("processor_power")]
    private int $processorPower;
    #[Field]
    private float $cost;
    #[Field("image_src")]
    private string $imageSrc;

    public function __construct(string $name, int $ramSize, int $diskSize, int $processorPower, float $cost, string $imageSrc) {
        $this->name = $name;
        $this->ramSize = $ramSize;
        $this->diskSize = $diskSize;
        $this->processorPower = $processorPower;
        $this->cost = $cost;
        $this->imageSrc = $imageSrc;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getRamSize(): int {
        return $this->ramSize;
    }

    /**
     * @return int
     */
    public function getDiskSize(): int {
        return $this->diskSize;
    }

    /**
     * @return int
     */
    public function getProcessorPower(): int {
        return $this->processorPower;
    }

    /**
     * @return float
     */
    public function getCost(): float {
        return $this->cost;
    }

    /**
     * @return string
     */
    public function getImageSrc(): string {
        return $this->imageSrc;
    }
}
