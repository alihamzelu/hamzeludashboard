<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Notifications\TaskMessageNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TaskMessageController extends Controller
{
    public function store(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('view', $task);

        
        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => [
                'bail',
                'file',
                'max:51200',
                'extensions:jpg,jpeg,png,gif,webp,mp4,mov,webm,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip,rar,7z',
            ],
        ], [
            'attachments.*.max' => 'حجم هر فایل نباید بیشتر از ۵۰ مگابایت باشد. لطفاً فایل را در یکی از سایت‌های آپلود فایل زیر بارگذاری کنید و لینک آن را در پیام بفرستید.',
        ]);


        $body = trim($validated['body'] ?? '');
        $files = $request->file('attachments', []);

        if ($body === '' && count($files) === 0) {
            throw ValidationException::withMessages([
                'body' => 'متن پیام را بنویسید یا حداقل یک فایل پیوست کنید.',
            ]);
        }

        $storedPaths = [];

        try {
            $message = DB::transaction(function () use (
                $task,
                $body,
                $files,
                &$storedPaths
            ) {
                $message = $task->messages()->create([
                    'user_id' => auth()->id(),
                    'body' => $body,
                    'type' => 'text',
                ]);

                foreach ($files as $file) {
                    $mime = $file->getMimeType()
                        ?? 'application/octet-stream';

                    $path = $file->store(
                        'task-messages/' . $task->id,
                        'public'
                    );

                    if ($path === false) {
                        throw new \RuntimeException(
                            'ذخیره فایل با خطا مواجه شد.'
                        );
                    }

                    $storedPaths[] = $path;

                    $message->attachments()->create([
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $mime,
                        'size' => $file->getSize(),
                    ]);
                }

                return $message;
            });
        } catch (\Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        $task->loadMissing([
            'project.members',
            'project.owner',
            'assignedUser',
        ]);

        $recipients = collect()
            ->push($task->project->owner)
            ->merge($task->project->members)
            ->when(
                $task->assignedUser,
                fn ($collection) => $collection->push($task->assignedUser)
            )
            ->filter()
            ->unique('id')
            ->reject(
                fn ($user) => (int) $user->id === (int) auth()->id()
            );

        foreach ($recipients as $user) {
            $user->notify(
                new TaskMessageNotification($task, $message)
            );
        }

        return back()->with('success', 'پیام با موفقیت ارسال شد.');
    }
}
