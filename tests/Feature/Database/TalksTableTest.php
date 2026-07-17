<?php

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('has talks table with the correct schema', function () {
    expect(Schema::hasTable('talks'))->toBeTrue('The talks table does not exist.');

    $expectedColumns = [
        'id',             // String/UUID primary key
        'time_block_id',  // Foreign key
        'title',          // String
        'speaker',        // String
        'start_time',     // Datetime
        'end_time',       // Datetime
        'created_at',
        'updated_at'
    ];

    foreach ($expectedColumns as $column) {
        expect(Schema::hasColumn('talks', $column))
            ->toBeTrue("The talks table is missing the '{$column}' column.");
    }
});
