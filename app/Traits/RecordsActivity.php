<?php

namespace App\Traits;

use App\Models\Activity;

trait RecordsActivity
{
    public static function bootRecordsActivity()
    {
        if (auth()->guest()) return;

        foreach (static::getRecordableEvents() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        // Deletes the activities associated with the model record if it is deleted
        static::deleting(function ($model) {
            $model->activity()->delete();
        });
    }

    public function recordActivity($event)
    {
        Activity::create([
            'user_id' => $this->getAuthenticatedUser(),
            'subject_id' => $this->id,
            'subject_type' => get_class($this),
            'name' => $this->getActivityName($this, $event),
        ]);
        return true;
    }

    protected function getActivityName($model, $action)
    {
        $name = strtolower((new \ReflectionClass($model))->getShortName());

        return "{$action}_{$name}";
    }

    protected static function getRecordableEvents()
    {
        if (isset(static::$recordableEvents)) {
            return static::$recordableEvents;
        }

        return ['created', 'updated', 'deleted'];
    }

    protected function getAuthenticatedUser()
    {
        return auth()->user()->id ?: \App\Models\User::all()->first()->id; // Fallback for seeding and testing to prevent errors
    }
}