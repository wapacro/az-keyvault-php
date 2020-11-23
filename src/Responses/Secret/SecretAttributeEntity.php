<?php


namespace AzKeyVault\Responses\Secret;

use DateTime;
use AzKeyVault\Contracts\AttributeInterface;

class SecretAttributeEntity implements AttributeInterface {
    /** @var bool */
    public $enabled;

    /** @var DateTime */
    public $created;

    /** @var DateTime */
    public $updated;

    /** @var string */
    public $recoveryLevel;

    /** @var DateTime|null */
    public $expires;

    /** @var DateTime|null */
    public $notBefore;

    public function __construct(bool $enabled, int $created, int $updated, string $recoveryLevel, int $expires = null, int $notBefore = null) {
        $this->enabled = $enabled;
        $this->created = new DateTime('@' . $created);
        $this->updated = new DateTime('@' . $updated);
        $this->recoveryLevel = $recoveryLevel;
        $this->expires = $expires ? new DateTime('@' . $expires) : null;
        $this->notBefore = $notBefore ? new DateTime('@' . $notBefore) : null;
    }

    public function isEnabled() {
        return $this->enabled;
    }
}
