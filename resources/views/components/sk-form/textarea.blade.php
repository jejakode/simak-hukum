@props([
    'id',
    'name',
    'label',
    'rows' => 3,
    'placeholder' => '',
    'help' => null,
    'value' => '',
])

@php
    $resolvedValue = old($name, $value);
@endphp

<div>
    <label for="{{ $id }}" class="mb-2 block text-sm font-medium text-slate-900 dark:text-slate-100">{{ $label }}</label>
    <textarea
        name="{{ $name }}"
        id="{{ $id }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20']) }}
    >{{ $resolvedValue }}</textarea>
    @error($name)
        <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
    @if($help)
        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $help }}</p>
    @endif
</div>
