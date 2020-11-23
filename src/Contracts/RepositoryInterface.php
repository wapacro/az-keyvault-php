<?php


namespace AzKeyVault\Contracts;

interface RepositoryInterface {
    public function add(EntityInterface $entity);

    public function all();
}
