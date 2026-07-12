<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    public function log(
        string $action,
        ?string $description = null,
        ?Model $entity = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entity ? get_class($entity) : null,
            'entity_id' => $entity?->getKey(),
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
        ]);
    }

    public function logModelUpdate(Model $entity, array $original, array $changes): AuditLog
    {
        $dirty = [];
        foreach ($changes as $field => $newValue) {
            if (array_key_exists($field, $original) && $original[$field] !== $newValue) {
                $dirty[$field] = ['old' => $original[$field], 'new' => $newValue];
            }
        }

        if (empty($dirty)) {
            $dirty = $changes;
        }

        return $this->log(
            action: "updated_{$this->entityName($entity)}",
            description: "Updated " . class_basename($entity) . " #{$entity->getKey()}",
            entity: $entity,
            oldValues: $original,
            newValues: $changes,
        );
    }

    public function logModelCreation(Model $entity): AuditLog
    {
        return $this->log(
            action: "created_{$this->entityName($entity)}",
            description: "Created " . class_basename($entity) . " #{$entity->getKey()}",
            entity: $entity,
            newValues: $entity->toArray(),
        );
    }

    public function logModelDeletion(Model $entity): AuditLog
    {
        return $this->log(
            action: "deleted_{$this->entityName($entity)}",
            description: "Deleted " . class_basename($entity) . " #{$entity->getKey()}",
            entity: $entity,
            oldValues: $entity->toArray(),
        );
    }

    private function entityName(Model $entity): string
    {
        return strtolower(class_basename($entity));
    }
}
