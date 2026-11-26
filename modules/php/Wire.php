<?php

declare(strict_types=1);

namespace Bga\Games\bombbusters;

use Bga\GameFramework\Components\ItemManager\Item;
use Bga\GameFramework\Components\ItemManager\ItemField;
use Bga\GameFramework\Components\ItemManager\ItemFieldKind;

#[Item('bombbusters_wire')]
class Wire
{
    #[ItemField(kind: ItemFieldKind::ID)]
    public int $id;

    #[ItemField(kind: ItemFieldKind::LOCATION, locationIndex: 0)]
    public string $location;

    #[ItemField(kind: ItemFieldKind::LOCATION, locationIndex: 1)]
    public ?int $locationArg;

    #[ItemField(kind: ItemFieldKind::ORDER)]
    public int $order;

    #[ItemField]
    public string $color;

    #[ItemField]
    public float $sortValue;

    #[ItemField]
    public int $stand = 0;

    #[ItemField]
    public int $position = 0;

    #[ItemField]
    public bool $infoRevealed = false;

    public function getValue(): string
    {
        return match ($this->color) {
            'blue' => (string)(int)$this->sortValue,
            'yellow' => 'yellow',
            'red' => 'red',
        };
    }
}
