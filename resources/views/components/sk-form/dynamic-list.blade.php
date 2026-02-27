@props([
    'id',
    'label',
    'buttonText' => 'Tambah Poin',
    'buttonAction',
    'hint' => null,
    'optional' => false,
    'containerClass' => 'space-y-3',
])

@php
    $listError = $errors->first($id) ?: $errors->first($id . '.*');
@endphp

<div>
    <div class="mb-3 flex items-center justify-between gap-3">
        <label class="block text-sm font-medium text-slate-900 dark:text-slate-100">
            {{ $label }}
            @if($optional)
                <span class="font-normal text-slate-500 dark:text-slate-400">(Opsional)</span>
            @endif
        </label>
        <button
            type="button"
            onclick="{{ $buttonAction }}"
            class="inline-flex cursor-pointer items-center gap-1 text-sm font-semibold text-blue-700 transition hover:text-blue-800 dark:text-cyan-300 dark:hover:text-cyan-200"
        >
            <x-heroicon-o-plus class="h-4 w-4" />
            {{ $buttonText }}
        </button>
    </div>
    <div id="{{ $id }}-container" class="{{ $containerClass }}"></div>
    @if($listError)
        <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $listError }}</p>
    @endif
    @if($hint)
        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif
</div>
