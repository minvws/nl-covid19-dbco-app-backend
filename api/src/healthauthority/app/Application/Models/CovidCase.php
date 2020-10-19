<?php
namespace App\Application\Models;

/**
 * CovidCase.
 */
class CovidCase
{
    /**
     * Date of symptom onset
     *
     * @var $dateOfSymptomOnset
     */
    public string $dateOfSymptomOnset;
     
    /**
     * Tasks.
     *
     * @var Task[]
     */
    public array $tasks = [];
}