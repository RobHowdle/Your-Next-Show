<?php

namespace App;

trait TrackChanges
{
    protected $oldAttributes = [];
    protected static function bootTracksChanges()
    {
        static::updating(function ($model) {
            $model->oldAttributes = $model->getOriginal();
        });

        static::updated(function ($model) {
            $changes = [];
            foreach ($model->getChanges() as $attribute => $newValue) {
                if (isset($model->oldAttributes[$attribute])) {
                    $changes[$attribute] = [
                        'from' => $model->oldAttributes[$attribute],
                        'to' => $newValue
                    ];
                }
            }

            if (!empty($changes)) {
                \Log::info('Model Updated: ' . class_basename($model), [
                    'model_id' => $model->id,
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name,
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                    'changes' => $changes
                ]);
            }
        });
    }
}