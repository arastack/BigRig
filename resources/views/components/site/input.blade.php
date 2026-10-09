@props(['name', 'label', 'type' => 'text', 'required' => false, 'hint' => null, 'value' => null, 'errorKey' => null])
@php
    $id = $attributes->get('id', str_replace(['[', ']'], ['_', ''], $name));
    $key = $errorKey ?? $name;
@endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="form-label">{{ $label }}@if ($required) <span class="text-red-600" aria-hidden="true">*</span>@endif</label>
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" @if ($type !== 'password' && $type !== 'file') value="{{ old($name, $value) }}" @endif @required($required)
           {{ $attributes->except(['class', 'id'])->merge(['class' => 'form-field' . ($errors->has($key) ? ' border-red-400 ring-1 ring-red-200' : '')]) }}
           @if ($hint || $errors->has($key)) aria-describedby="{{ $id }}-help" @endif @if ($errors->has($key)) aria-invalid="true" @endif>
    @error($key)
        <p id="{{ $id }}-help" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
    @else
        @if ($hint)<p id="{{ $id }}-help" class="mt-1.5 text-xs text-ink-400">{{ $hint }}</p>@endif
    @enderror
</div>
