<?php


namespace AzKeyVault\Responses;

use AzKeyVault\Contracts\EntityInterface;
use AzKeyVault\Contracts\AttributeInterface;
use AzKeyVault\Responses\Key\KeyAttributeEntity;
use AzKeyVault\Responses\Secret\SecretAttributeEntity;

class IdEntity implements EntityInterface {
    /** @var string */
    public $id;

    /** @var KeyAttributeEntity | SecretAttributeEntity */
    public $attributes;

    public function __construct(string $id, $attributes, string $content = null) {
        $this->id = $id;
        $this->attributes = $attributes;
        $this->content = $content;
    }

    public function __toString(): string {
        return $this->id;
    }

    public function getAttributes(): AttributeInterface {
        return $this->attributes;
    }
}
