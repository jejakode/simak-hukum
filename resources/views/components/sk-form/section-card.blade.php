@props([
    'number' => null,
    'title',
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900']) }}>
    <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-950/60">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
            @if($number)
                {{ $number }}.
            @endif
            {{ $title }}
        </h2>
        @if($description)
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $description }}</p>
        @endif
    </div>
    <div class="p-6">
        {{ $slot }}
    </div>
</section>
