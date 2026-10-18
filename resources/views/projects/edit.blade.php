<x-dashboard-layout>
    <div class="max-w-2xl">
        <a href="{{ route('projects.show', $project) }}" class="text-sm text-slate-500 hover:text-blue-600 inline-flex items-center gap-1 mb-4 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
            بازگشت به پروژه
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mb-1">ویرایش پروژه</h1>
        <p class="text-sm text-slate-500 mb-6">جزئیات پروژه را به‌روز کنید</p>

        <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">نام پروژه</label>
                <input id="name" type="text" name="name" value="{{ old('name', $project->name) }}" required autofocus
                    class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition" />
                @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">توضیحات</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition resize-none">{{ old('description', $project->description) }}</textarea>
                @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-1.5">وضعیت</label>
                    <select id="status" name="status"
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition bg-white">
                        <option value="planning" {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>برنامه‌ریزی</option>
                        <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>فعال</option>
                        <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>تکمیل‌شده</option>
                        <option value="archived" {{ old('status', $project->status) == 'archived' ? 'selected' : '' }}>آرشیو</option>
                    </select>
                    @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-slate-700 mb-1.5">تاریخ پایان</label>
                    <input id="end_date" type="date" name="end_date"
                        value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}"
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition" />
                    @error('end_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('projects.show', $project) }}" class="px-5 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-900 transition">انصراف</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                    ذخیره تغییرات
                </button>
            </div>
        </form>
    </div>
</x-dashboard-layout>