<?php

namespace App\DTOs;

class ServiceStats
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly int $eventsCount = 0,
        public readonly float $rating = 0.0,
        public readonly float $profits = 0.0
    ) {}
}