<?php

namespace App\Enums;

enum UserType: string
{
    case Promoter = 'promoter';
    case Artist = 'artist';
    case Designer = 'designer';
    case Venue = 'venue';
    case Photographer = 'photographer';
    case Videographer = 'videographer';

    public static function fromString(string $value): self
    {
        return match (strtolower($value)) {
            'promoter' => self::Promoter,
            'venue' => self::Venue,
            'artist' => self::Artist,
            'designer' => self::Designer,
            'photographer' => self::Photographer,
            'videographer' => self::Videographer,
            'standard' => self::StandardUser,
            default => throw new \ValueError("Invalid user type: {$value}")
        };
    }
}