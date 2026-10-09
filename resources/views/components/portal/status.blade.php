@props(['status'])
@php
    [$class, $label] = match ($status) {
        'awaiting_payment', 'pending' => ['badge-yellow', $status === 'pending' ? 'Pending' : 'Awaiting payment'],
        'completed' => ['badge-green', 'Paid'],
        'enrolled', 'approved', 'active' => ['badge-green', ucfirst($status)],
        'in_training' => ['badge-green', 'In training'],
        'suspended', 'failed', 'rejected' => ['badge-red', ucfirst($status)],
        default => ['badge-grey', ucfirst(str_replace('_', ' ', (string) $status))],
    };
@endphp
<span {{ $attributes->merge(['class' => $class]) }}>{{ $label }}</span>
