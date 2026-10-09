@props(['name', 'class' => 'h-6 w-6'])
@php
    $n = strtolower($name);
    $icon = match (true) {
        str_contains($n, 'forklift') => 'forklift',
        str_contains($n, 'bus') || str_contains($n, 'brt') => 'bus',
        str_contains($n, 'defensive') => 'shield',
        str_contains($n, 'refresher') => 'refresh',
        str_contains($n, 'inspection') || str_contains($n, 'test') => 'clipboard',
        str_contains($n, 'private') || str_contains($n, 'regular') || str_contains($n, 'car') => 'car',
        str_contains($n, 'corporate') => 'building',
        str_contains($n, 'job') || str_contains($n, 'placement') => 'briefcase',
        str_contains($n, 'licens') => 'id-card',
        str_contains($n, 'heavy') || str_contains($n, 'equipment') => 'hard-hat',
        default => 'truck',
    };
@endphp
<x-site.icon :name="$icon" :class="$class" />
