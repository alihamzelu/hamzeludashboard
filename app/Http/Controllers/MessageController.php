<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        return view('messages.index', [
            'conversations' => $this->userConversations(),
            'contacts'      => $this->contacts(),
            'conversation'  => null,
            'messages'      => collect(),
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $this->authorize('view', $conversation);

        $conversation->users()->updateExistingPivot(auth()->id(), [
            'last_read_at' => now(),
        ]);

        auth()->user()
            ->unreadNotifications
            ->where('data.type', 'new_message')
            ->where('data.conversation_id', $conversation->id)
            ->each
            ->markAsRead();

        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('messages.index', [
            'conversations' => $this->userConversations(),
            'contacts'      => $this->contacts(),
            'conversation'  => $conversation->load('users'),
            'messages'      => $messages,
        ]);
    }

    public function storeConversation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'    => ['required', 'exists:users,id'],
            'body'       => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:51200', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,webm'],
        ]);

        $recipient = User::findOrFail($validated['user_id']);

        $this->authorize('start', [Conversation::class, $recipient]);

        if (empty($validated['body']) && !$request->hasFile('attachment')) {
            return back()->withErrors([
                'body' => 'برای ارسال پیام، متن یا فایل ضمیمه را وارد کنید.',
            ]);
        }

        $conversation = $this->findOrCreateDirectConversation($recipient->id);

        $data = [
            'conversation_id' => $conversation->id,
            'user_id'         => auth()->id(),
            'body'            => $validated['body'] ?? '',
            'type'            => 'text',
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $mime = $file->getMimeType();
            $isVideo = str_starts_with((string) $mime, 'video/');

            $path = $file->store('messages/' . $conversation->id, 'public');

            $data['type'] = $isVideo ? 'video' : 'image';
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $file->getClientOriginalName();
            $data['attachment_mime'] = $mime;
            $data['attachment_size'] = $file->getSize();
        }

        $message = Message::create($data);
        $message->setRelation('user', auth()->user());

        $conversation->update([
            'last_message_at' => now(),
        ]);

        $this->notifyParticipants($conversation, $message);

        return redirect()->route('messages.show', $conversation);
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorize('sendMessage', $conversation);

        $validated = $request->validate([
            'body'       => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:51200', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,webm'],
        ]);

        if (empty($validated['body']) && !$request->hasFile('attachment')) {
            return back()->withErrors([
                'body' => 'Write a message or attach a file.',
            ]);
        }

        $data = [
            'conversation_id' => $conversation->id,
            'user_id'         => auth()->id(),
            'body'            => $validated['body'] ?? '',
            'type'            => 'text',
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $mime = $file->getMimeType();
            $isVideo = str_starts_with((string) $mime, 'video/');

            $path = $file->store('messages/' . $conversation->id, 'public');

            $data['type'] = $isVideo ? 'video' : 'image';
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $file->getClientOriginalName();
            $data['attachment_mime'] = $mime;
            $data['attachment_size'] = $file->getSize();
        }

        $message = Message::create($data);
        $message->setRelation('user', auth()->user());

        $conversation->update([
            'last_message_at' => now(),
        ]);

        $this->notifyParticipants($conversation, $message);

        return back();
    }

    protected function userConversations()
    {
        return auth()->user()
            ->conversations()
            ->with([
                'users',
                'messages' => fn($q) => $q->latest()->limit(1),
            ])
            ->orderByDesc('last_message_at')
            ->get();
    }

    protected function contacts()
    {
        return User::query()
            ->where('id', '!=', auth()->id())
            ->whereHas('projects', function ($query) {
                $query->where(function ($q) {
                    $q->where('projects.owner_id', auth()->id())
                        ->orWhereHas('members', fn($q) => $q->where('users.id', auth()->id()));
                });
            })
            ->orderBy('name')
            ->get();
    }

    protected function findOrCreateDirectConversation(int $otherUserId): Conversation
    {
        $existing = Conversation::query()
            ->whereNull('task_id')
            ->whereHas('users', fn($q) => $q->where('users.id', auth()->id()))
            ->whereHas('users', fn($q) => $q->where('users.id', $otherUserId))
            ->withCount('users')
            ->get()
            ->first(fn($c) => $c->users_count === 2);

        if ($existing) {
            return $existing;
        }

        $conversation = Conversation::create([
            'last_message_at' => now(),
        ]);

        $conversation->users()->attach([
            auth()->id(),
            $otherUserId,
        ]);

        return $conversation;
    }

    protected function notifyParticipants(Conversation $conversation, Message $message): void
    {
        $conversation->loadMissing('users');

        foreach ($conversation->users as $user) {
            if ((int) $user->id === (int) auth()->id()) {
                continue;
            }

            $user->notify(new NewMessageNotification($message, $conversation));
        }
    }
}