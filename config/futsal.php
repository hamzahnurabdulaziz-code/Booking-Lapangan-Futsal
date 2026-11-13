<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Futsal Venue Configuration
    |--------------------------------------------------------------------------
    |
    | Basic configuration for FutsalKita MVP: single venue, Asia/Jakarta time,
    | operating hours, booking duration limits, and advance booking window.
    |
    */

    'venue_name' => 'FutsalKita Arena',
    'venue_address' => 'Jl. Olahraga No. 10, Jakarta Selatan',
    'timezone' => 'Asia/Jakarta',

    // Operating hours (08:00 to 23:00 WIB daily)
    'opening_hour' => 8,
    'closing_hour' => 23,

    // Booking duration in hours
    'min_duration_hours' => 1,
    'max_duration_hours' => 3,

    // Maximum days in advance a customer can book (0 = today only, 30 = up to 30 days ahead)
    'max_advance_days' => 30,

    // Maximum characters
    'max_team_name_length' => 100,
    'max_notes_length' => 500,
    'max_cancellation_reason_length' => 500,
];
