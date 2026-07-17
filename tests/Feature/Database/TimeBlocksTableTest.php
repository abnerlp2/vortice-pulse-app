<?php

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('has time_blocks table with the correct schema', function () {
    // Assert the table exists
    expect(Schema::hasTable('time_blocks'))->toBeTrue('The time_blocks table does not exist.');

    // Assert the table has the required columns
    $expectedColumns = [
        'id',           // String/UUID primary key
        'start_time',   // Datetime
        'end_time',     // Datetime
        'created_at',   // Timestamps
        'updated_at'
    ];

    foreach ($expectedColumns as $column) {
        expect(Schema::hasColumn('time_blocks', $column))
            ->toBeTrue("The time_blocks table is missing the '{$column}' column.");
    }
});
