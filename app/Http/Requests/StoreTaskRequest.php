<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Project;
class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        if ($project instanceof Project) {
            return $this->user()->can('create', $project);
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'priority' => [
                'required',
                'in:low,medium,high',
            ],

            'status' => [
                'required',
                'in:todo,in_progress,review,done',
            ],

            'due_date' => [
                'nullable',
                'date',
            ],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'title.max' => 'Title cannot be longer than 255 characters.',

            'description.max' => 'Description cannot be longer than 5000 characters.',

            'priority.required' => 'Please select a priority.',
            'priority.in' => 'Invalid priority selected.',

            'status.required' => 'Please select a status.',
            'status.in' => 'Invalid status selected.',

            'due_date.date' => 'Please enter a valid due date.',
        ];
    }
}
