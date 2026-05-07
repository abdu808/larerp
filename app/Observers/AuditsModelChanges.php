<?php

namespace App\Observers;

use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;

class AuditsModelChanges
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function created(Model $model): void
    {
        $this->auditLogger->logModelEvent(
            model: $model,
            event: 'created',
            newValues: $this->auditableAttributes($model->getAttributes()),
        );
    }

    public function updated(Model $model): void
    {
        $newValues = $this->auditableAttributes($model->getChanges());

        if ($newValues === []) {
            return;
        }

        $oldValues = [];

        foreach (array_keys($newValues) as $key) {
            $oldValues[$key] = $model->getOriginal($key);
        }

        $this->auditLogger->logModelEvent(
            model: $model,
            event: 'updated',
            oldValues: $oldValues,
            newValues: $newValues,
        );
    }

    public function deleted(Model $model): void
    {
        $this->auditLogger->logModelEvent(
            model: $model,
            event: 'deleted',
            oldValues: $this->auditableAttributes($model->getOriginal()),
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function auditableAttributes(array $attributes): array
    {
        unset($attributes['created_at'], $attributes['updated_at']);

        return $attributes;
    }
}
