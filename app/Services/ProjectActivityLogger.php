<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectActivity;
use Illuminate\Database\Eloquent\Model;

class ProjectActivityLogger
{
    public static function log(
        Project $project,
        string $type,
        string $description,
        ?Model $subject = null,
        array $properties = []
    ): ProjectActivity {
        return ProjectActivity::create([
            'project_id'   => $project->id,
            'user_id'      => auth()->id(),
            'type'         => $type,
            'description'  => $description,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id'   => $subject?->getKey(),
            'properties'   => $properties ?: null,
        ]);
    }
}