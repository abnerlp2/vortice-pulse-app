<?php

namespace App\Core\Evaluation\Contracts;

interface EvaluationRepositoryInterface
{
    /**
     * Clears all existing agenda data (talks and time blocks) from persistence.
     * 
     * @return void
     */
    public function clearAgenda(): void;

    /**
     * Persists an array of time blocks into the database.
     * 
     * @param array<int, array{id:string, start_time:string, end_time:string}> $timeBlocks
     * @return void
     */
    public function saveTimeBlocks(array $timeBlocks): void;

    /**
     * Persists an array of talks into the database.
     * 
     * @param array<int, array{id:string, title:string, speaker:string, time_block_id:string, start_time:string, end_time:string}> $talks
     * @return void
     */
    public function saveTalks(array $talks): void;

    /**
     * Retrieves a time block by its ID.
     *
     * @param string $id
     * @return object|null
     */
    public function getTimeBlockById(string $id): ?object;

    /**
     * Retrieves a talk by its ID.
     *
     * @param string $id
     * @return object|null
     */
    public function getTalkById(string $id): ?object;

    /**
     * Retrieves all ratings for a given talk.
     *
     * @param string $talkId
     * @return array<int>
     */
    public function getTalkRatings(string $talkId): array;

    /**
     * Checks if an evaluation exists for a given talk and device signature.
     *
     * @param string $talkId
     * @param string $deviceSignature
     * @return bool
     */
    public function hasEvaluation(string $talkId, string $deviceSignature): bool;

    /**
     * Persists a new evaluation ensuring unique constraints.
     *
     * @param array{talk_id:string, rating:int, device_signature:string, liked_aspects?:string|null, improvement_aspects?:string|null} $data
     * @return bool
     */
    public function saveEvaluation(array $data): bool;
}
