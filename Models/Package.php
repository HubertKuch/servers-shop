<?php

namespace Servers\Models;

use Avocado\ORM\Field;
use Avocado\ORM\Id;
use Avocado\ORM\Table;

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
}
