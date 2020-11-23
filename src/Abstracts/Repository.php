<?php


namespace AzKeyVault\Abstracts;

use AzKeyVault\Contracts\EntityInterface;
use AzKeyVault\Contracts\RepositoryInterface;

abstract class Repository implements RepositoryInterface {
    protected $values = [];

    public function add(EntityInterface $entity): void {
        $this->values[] = $entity;
    }

    public function enabled() {
        return array_filter($this->all(), function (EntityInterface $entity) {
            return $entity->getAttributes()->isEnabled();
        });
    }

    public function all() {
        return $this->values;
    }
}
