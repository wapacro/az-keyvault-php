<?php


namespace AzKeyVault\Responses\Secret;

use AzKeyVault\Contracts\EntityInterface;
use AzKeyVault\Contracts\AttributeInterface;

class SecretVersionEntity implements EntityInterface {
    /** @var string */
    public $name;

    /** @var string */
    public $id;

    /** @var string */
    public $content;

    /** @var string */
    public $url;

    /** @var SecretAttributeEntity */
    public $attributes;

    public function __construct(string $name, string $id, string $url, SecretAttributeEntity $attributes, string $content = null) {
        $this->name = $name;
        $this->id = $id;
        $this->url = $url;
        $this->attributes = $attributes;
        $this->content = $content;
    }

    public function getAttributes(): AttributeInterface {
        return $this->attributes;
    }
}
