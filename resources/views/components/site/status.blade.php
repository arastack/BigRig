@props(['summary' => true])
{{-- Inline status and validation summary for auth forms --}}
@if (session('status'))
    <div class="mb-6 flex gap-3 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900" role="status"><x-site.icon name="check-circle" class="h-5 w-5 shrink-0" />{{ session('status') }}</div>
@endif
@if ($errors->any() && ($summary ?? true))
    <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">
        <p class="font-semibold">{{ page_text('auth_pages.errors') }}</p>
        <ul class="mt-1 list-inside list-disc space-y-0.5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
