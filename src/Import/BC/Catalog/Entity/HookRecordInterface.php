<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog\Entity;

interface HookRecordInterface
{
    public const TYPE_CREATED = 0;
    public const TYPE_UPDATED = 1;
    public const TYPE_DELETED = 2;

    public function setType(int $type);

    public function getResourceType(): int;

    public function setResourceId(int $id);

    public function getResourceId(): int;

    public function makeProcessed();

    public function setDateCreated(\DateTime $dateCreated);

    public function isCreated(): bool;

    public function isUpdated(): bool;
}