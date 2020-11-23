<?php


namespace AzKeyVault\Responses;

use AzKeyVault\Abstracts\Repository;

class IdRepository extends Repository {
    /** @var string | null */
    private $nextLink;

    public function setNextLink(?string $nextLink): void {
        $this->nextLink = $nextLink;
    }

    public function getNextLink(): ?string {
        return $this->nextLink;
    }
}
