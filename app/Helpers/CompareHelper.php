<?php

namespace App\Helpers;

class CompareHelper
{
    public static function showChanges($existing, $new)
    {
        $changes = [];

        foreach ($new as $key => $value) {
            if (isset($existing->$key) && $existing->$key != $value) {
                $changes[$key] = [
                    'field' => $key,
                    'old' => $existing->$key,
                    'new' => $value
                ];
            }
        }

        return $changes;
    }

    public static function showEventChanges($existing, $new)
    {
        $changes = [];
        $fieldsToCompare = [
            'event_name',
            'event_date',
            'event_start_time',
            'event_end_time',
            'event_description',
            'facebook_event_url',
            'ticket_url',
            'on_the_door_ticket_price',
            'poster_url',
            'venue_id',
            'headliner',
            'headliner_id',
            'main_support',
            'main_support_id',
            'bands',
            'bands_ids',
            'opener',
            'opener_id',
            'promoter_ids'
        ];

        foreach ($fieldsToCompare as $field) {
            $oldValue = $existing->$field ?? null;
            $newValue = $new[$field] ?? null;

            // Handle special cases
            if ($field === 'event_date' && $oldValue instanceof Carbon) {
                $oldValue = $oldValue->format('d-m-Y');
            }

            if ($field === 'bands_ids' || $field === 'promoter_ids') {
                $oldValue = is_array($oldValue) ? $oldValue : explode(',', $oldValue);
                $newValue = is_array($newValue) ? $newValue : explode(',', $newValue);
                if (array_diff($oldValue, $newValue) || array_diff($newValue, $oldValue)) {
                    $changes[$field] = [
                        'field' => $field,
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
                continue;
            }

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'field' => $field,
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        return $changes;
    }
}

// $existingEvent = Event::find($id);
// $changes = CompareHelper::showChanges($existingEvent, $request->all());

// dd($changes);