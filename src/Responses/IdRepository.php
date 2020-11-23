<?php


namespace AzKeyVault\Responses;


use AzKeyVault\Abstracts\Repository;

class IdRepository extends Repository {
	private ?string $nextLink = null;

	public function setNextLink(?string $nextLink) {
		$this->nextLink = $nextLink;
	}

	public function getNextLink(): ?string {
		return $this->nextLink;
	}

}
