<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ActivityLogObserver
{
    public function created(Model $model): void
    {
        $this->write($model, 'created', array_keys($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $fields = array_values(array_diff(array_keys($model->getChanges()), ['updated_at']));
        if ($fields !== []) {
            $this->write($model, 'updated', $fields);
        }
    }

    public function deleted(Model $model): void
    {
        $this->write($model, 'deleted', []);
    }

    private function write(Model $model, string $action, array $fields): void
    {
        if (!Schema::hasTable('activity_logs')) {
            return;
        }

        $actor = Auth::user();
        if ($actor === null && $model instanceof User && $action === 'created') {
            $actor = $model;
        }
        $username = $actor?->username
            ?? $model->getAttribute('approved_by')
            ?? $model->getAttribute('created_by')
            ?? 'system';

        ActivityLog::create([
            'actor_username' => $username,
            'actor_name' => $actor?->name ?? ($username === 'system' ? 'Sistem' : null),
            'action' => $action,
            'subject_type' => class_basename($model),
            'subject_id' => (string) $model->getKey(),
            'changed_fields' => array_values(array_diff($fields, ['created_at', 'updated_at', 'password', 'remember_token'])),
        ]);
    }
}
