{{-- Small auto-submitting upload control --}}
<form method="POST" action="{{ route('application.documents.store') }}" enctype="multipart/form-data" x-data="{ sending: false }">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">
    <label class="btn-outline btn-sm cursor-pointer" :class="sending && 'pointer-events-none opacity-60'">
        <x-site.icon name="download" class="h-4 w-4 rotate-180" />
        <span x-text="sending ? @js(page_text('application.documents.uploading')) : @js($label)">{{ $label }}</span>
        <input type="file" name="file" accept="{{ $accept }}" class="sr-only" @change="sending = true; $el.form.submit()">
    </label>
</form>
