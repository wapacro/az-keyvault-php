<?php


namespace AzKeyVault\Responses\Key;

use AzKeyVault\Contracts\EntityInterface;
use AzKeyVault\Contracts\AttributeInterface;

class KeyVersionEntity implements EntityInterface {
    /** @var string */
    public $name;

    /** @var string */
    public $id;

    /** @var string */
    public $url;

    /** @var KeyAttributeEntity */
    public $attributes;

    public function __construct(string $name, string $id, string $url, KeyAttributeEntity $attributes) {
        $this->name = $name;
        $this->id = $id;
        $this->url = $url;
        $this->attributes = $attributes;
    }

    public function getAttributes(): AttributeInterface {
        return $this->attributes;
    }
}
