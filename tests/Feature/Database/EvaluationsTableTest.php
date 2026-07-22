<?php

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;

uses(TestCase::class, RefreshDatabase::class);

it('has evaluations table with the correct schema and constraints', function () {
    expect(Schema::hasTable('evaluations'))->toBeTrue('The evaluations table does not exist.');

    $expectedColumns = [
        'id',                  // Primary key
        'talk_id',             // Foreign key
        'rating',              // Integer (1-5)
        'liked_aspects',       // Nullable string/text (FR-003, T027)
        'improvement_aspects', // Nullable string/text (FR-003, T027)
        'device_signature',    // String hash
        'created_at',
        'updated_at'
    ];

    foreach ($expectedColumns as $column) {
        expect(Schema::hasColumn('evaluations', $column))
            ->toBeTrue("The evaluations table is missing the '{$column}' column.");
    }
    
    // Assert composite unique index exists to prevent duplicate votes
    $indexes = Schema::getIndexes('evaluations');
    
    $hasCompositeUnique = collect($indexes)->contains(function ($index) {
        return $index['unique'] === true && 
               in_array('talk_id', $index['columns']) && 
               in_array('device_signature', $index['columns']);
    });
    
    expect($hasCompositeUnique)->toBeTrue('The evaluations table is missing the unique composite index for talk_id and device_signature.');
});
