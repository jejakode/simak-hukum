@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-100 text-slate-900 transition-colors dark:bg-slate-950 dark:text-slate-100">
        <main class="pt-28 py-10">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8 text-center">
                    {{-- <span class="mb-2 inline-flex items-center rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-semibold tracking-wide text-blue-700 dark:border-cyan-700/40 dark:bg-cyan-900/30 dark:text-cyan-200">DRAFT</span> --}}
                    <h1 class="mb-2 text-3xl font-bold text-slate-900 dark:text-slate-100">Form Surat Keputusan</h1>
                    <p class="text-base text-slate-600 dark:text-slate-300">Buat draf dokumen resmi sesuai standar tata naskah dinas.</p>
                </div>

                <div id="autosave-toast" class="pointer-events-none fixed right-4 top-24 z-50 rounded-lg border border-slate-200 bg-white/95 px-3 py-2 text-xs text-slate-600 opacity-0 shadow-sm transition-opacity duration-200 dark:border-slate-700 dark:bg-slate-900/95 dark:text-slate-300">
                    Draft tersimpan
                </div>

                <form id="sk-form" action="{{ route('sk.handle') }}" method="POST" enctype="multipart/form-data" class="mx-auto max-w-5xl space-y-8">
                    @csrf

                    @if($errors->any())
                        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200">
                            <p class="font-semibold">Form belum valid. Periksa field yang ditandai.</p>
                        </div>
                    @endif

                    @include('pages.sk-form.sections.title')
                    @include('pages.sk-form.sections.konsiderans')
                    @include('pages.sk-form.sections.diktum')
                    @include('pages.sk-form.sections.penutup')
                    @include('pages.sk-form.sections.lampiran')
                    @include('pages.sk-form.sections.actions')
                </form>
            </div>
        </main>
    </div>
@endsection

@php
    $draft = $draft ?? [];
    $initialDynamicValues = [
        'menimbang' => old('menimbang', $draft['menimbang'] ?? []),
        'mengingat' => old('mengingat', $draft['mengingat'] ?? []),
        'memperhatikan' => old('memperhatikan', $draft['memperhatikan'] ?? []),
        'diktum' => old('diktum', $draft['diktum'] ?? []),
    ];
@endphp

@push('scripts')
    <script>
        const DIKTUM_LABELS = [
            'KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA',
            'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH'
        ];
        const FORM_STORAGE_KEY = 'simak_hukum_sk_form_draft';
        const IS_FRESH_MODE = @json($fresh ?? false);
        const HAS_SERVER_DRAFT = @json($hasServerDraft ?? false);
        const HAS_OLD_INPUT = @json($errors->any());
        const INITIAL_DYNAMIC_VALUES = {!! json_encode($initialDynamicValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};

        const DYNAMIC_TEXTAREA_CLASSES = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 pr-12 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 sm:pl-24 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20';
        let autosaveTimer;
        let lastSavedPayload = '';
        let toastTimer;
        let isInitializing = true;
        let dynamicValuesSource = { ...INITIAL_DYNAMIC_VALUES };

        document.addEventListener('DOMContentLoaded', () => {
            if (IS_FRESH_MODE) {
                localStorage.removeItem(FORM_STORAGE_KEY);
            }

            tryRestoreDraftForCreate();

            initializeList('menimbang', 'bahwa dalam rangka...');
            initializeList('mengingat', 'Undang-Undang Nomor ...');
            initializeList('memperhatikan');
            initializeDiktumList();
            bindLampiranRemoveButtons();
            bindPreviewDraftStorage();
            bindAutosave();
            isInitializing = false;
        });

        function updateRemoveButtons(container) {
            const groups = container.querySelectorAll('.group');
            groups.forEach(group => {
                const btn = group.querySelector('button');
                if (btn) {
                    btn.style.display = groups.length > 1 ? '' : 'none';
                }
            });
        }

        function createInputGroup(type, placeholder, value = '') {
            const container = document.getElementById(`${type}-container`);
            if (!container) return;
            const nextIndex = container.querySelectorAll('.group').length;

            const wrapper = document.createElement('div');
            wrapper.className = 'relative group';
            wrapper.dataset.type = type;

            const labelAnchor = document.createElement('div');
            labelAnchor.className = 'mb-2 sm:pointer-events-none sm:absolute sm:left-0 sm:top-2 sm:mb-0 sm:flex sm:w-24 sm:justify-end sm:pr-1';

            const label = document.createElement('span');
            label.dataset.itemLabel = 'true';
            label.className = 'inline-flex items-center justify-end rounded-md bg-slate-100 px-2 py-1 text-right text-xs font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-100';
            label.textContent = getItemLabel(type, nextIndex);
            labelAnchor.appendChild(label);

            const textarea = document.createElement('textarea');
            textarea.name = `${type}[]`;
            textarea.rows = 3;
            textarea.placeholder = placeholder;
            textarea.value = value;
            textarea.className = DYNAMIC_TEXTAREA_CLASSES;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'absolute right-2 top-2 z-10 cursor-pointer rounded-md bg-white/90 p-1.5 text-slate-400 opacity-100 transition hover:bg-red-50 hover:text-red-600 sm:opacity-0 sm:focus:opacity-100 sm:group-hover:opacity-100 dark:bg-slate-900/90 dark:text-slate-500 dark:hover:bg-red-900/20 dark:hover:text-red-400';
            removeBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12m-10.5 0V6a1.5 1.5 0 011.5-1.5h6A1.5 1.5 0 0116.5 6v1.5m-9 0v10.125A2.625 2.625 0 0010.125 20.25h3.75A2.625 2.625 0 0016.5 17.625V7.5m-6 3v6m3-6v6" />
                </svg>
            `;

            removeBtn.onclick = () => {
                wrapper.remove();
                refreshDynamicLabels(type);
                scheduleAutosave(!isInitializing);
            };

            wrapper.appendChild(labelAnchor);
            wrapper.appendChild(textarea);
            wrapper.appendChild(removeBtn);
            container.appendChild(wrapper);

            refreshDynamicLabels(type);
        }

        function addInput(type, placeholder = '') {
            createInputGroup(type, placeholder || `Isi poin ${type}...`);
            scheduleAutosave(!isInitializing);
        }

        function addDiktum() {
            const container = document.getElementById('diktum-container');
            createInputGroup('diktum', 'Isi amar putusan...');
            scheduleAutosave(!isInitializing);
        }

        function initializeList(type, defaultPlaceholder = '') {
            const values = Array.isArray(dynamicValuesSource[type])
                ? dynamicValuesSource[type].filter(value => typeof value === 'string' && value.trim() !== '')
                : [];

            if (values.length === 0) {
                if (type !== 'memperhatikan') {
                    createInputGroup(type, defaultPlaceholder || `Isi poin ${type}...`);
                }
                return;
            }

            values.forEach(value => {
                createInputGroup(type, defaultPlaceholder || `Isi poin ${type}...`, value);
            });

            refreshDynamicLabels(type);
        }

        function initializeDiktumList() {
            const values = Array.isArray(dynamicValuesSource.diktum)
                ? dynamicValuesSource.diktum.filter(value => typeof value === 'string' && value.trim() !== '')
                : [];

            if (values.length === 0) {
                const label = DIKTUM_LABELS[0] || 'Diktum Ke-1';
                createInputGroup('diktum', `Isi amar putusan...`);
                return;
            }

            values.forEach((value, index) => {
                createInputGroup('diktum', 'Isi amar putusan...', value);
            });

            refreshDynamicLabels('diktum');
        }

        function refreshDynamicLabels(type) {
            const container = document.getElementById(`${type}-container`);
            if (!container) {
                return;
            }

            const groups = container.querySelectorAll('.group');
            groups.forEach((group, index) => {
                const label = group.querySelector('[data-item-label]');
                if (label) {
                    label.textContent = getItemLabel(type, index);
                }
            });

            updateRemoveButtons(container);
        }

        function getItemLabel(type, index) {
            if (type === 'menimbang') {
                return `${toAlphabetIndex(index)}.`;
            }

            if (type === 'mengingat' || type === 'memperhatikan') {
                return `${index + 1}.`;
            }

            if (type === 'diktum') {
                return DIKTUM_LABELS[index] || `DIKTUM ${index + 1}`;
            }

            return `${index + 1}.`;
        }

        function toAlphabetIndex(index) {
            let current = index;
            let result = '';

            do {
                result = String.fromCharCode(97 + (current % 26)) + result;
                current = Math.floor(current / 26) - 1;
            } while (current >= 0);

            return result;
        }

        function bindPreviewDraftStorage() {
            const form = document.getElementById('sk-form');
            if (!form) {
                return;
            }

            form.addEventListener('submit', event => {
                const submitter = event.submitter || document.activeElement;
                if (!submitter || submitter.name !== 'action' || submitter.value !== 'preview') {
                    return;
                }

                saveDraftToStorage(false);
            });
        }

        function tryRestoreDraftForCreate() {
            if (IS_FRESH_MODE || HAS_SERVER_DRAFT || HAS_OLD_INPUT) {
                return;
            }

            const rawDraft = localStorage.getItem(FORM_STORAGE_KEY);
            if (!rawDraft) {
                return;
            }

            try {
                const parsedDraft = JSON.parse(rawDraft);
                if (!parsedDraft || typeof parsedDraft !== 'object') {
                    return;
                }

                applyStaticDraftValues(parsedDraft);
                dynamicValuesSource = {
                    menimbang: sanitizeArray(parsedDraft.menimbang),
                    mengingat: sanitizeArray(parsedDraft.mengingat),
                    memperhatikan: sanitizeArray(parsedDraft.memperhatikan),
                    diktum: sanitizeArray(parsedDraft.diktum),
                };
            } catch (error) {
                // Abaikan draft rusak agar form tetap bisa dipakai normal.
            }
        }

        function applyStaticDraftValues(draft) {
            setInputValueIfEditable('nomor_surat', draft.nomor_surat);
            setInputValueIfEditable('sk_title', draft.sk_title);
            setInputValueIfEditable('menetapkan', draft.menetapkan);
        }

        function setInputValueIfEditable(id, value) {
            const element = document.getElementById(id);
            if (!element || element.hasAttribute('readonly') || element.disabled) {
                return;
            }

            if (typeof value === 'string' && value.trim() !== '' && element.value.trim() === '') {
                element.value = value;
            }
        }

        function sanitizeArray(value) {
            if (!Array.isArray(value)) {
                return [];
            }

            return value.filter(item => typeof item === 'string' && item.trim() !== '');
        }

        function bindAutosave() {
            const form = document.getElementById('sk-form');
            if (!form) {
                return;
            }

            form.addEventListener('input', () => scheduleAutosave());
            form.addEventListener('change', () => scheduleAutosave());
            form.addEventListener('focusout', () => scheduleAutosave(true), true);
        }

        function scheduleAutosave(force = false) {
            clearTimeout(autosaveTimer);
            autosaveTimer = setTimeout(() => {
                saveDraftToStorage(force);
            }, force ? 50 : 500);
        }

        function saveDraftToStorage(forceToast = true) {
            const form = document.getElementById('sk-form');
            if (!form) {
                return;
            }

            const payload = collectDraftPayload(form);
            const serialized = JSON.stringify(payload);

            if (serialized === lastSavedPayload) {
                return;
            }

            localStorage.setItem(FORM_STORAGE_KEY, serialized);
            lastSavedPayload = serialized;

            if (forceToast) {
                showAutosaveToast();
            }
        }

        function collectDraftPayload(form) {
            const formData = new FormData(form);
            return {
                saved_at: new Date().toISOString(),
                nomor_surat: formData.get('nomor_surat') || '',
                sk_title: formData.get('sk_title') || '',
                menimbang: formData.getAll('menimbang[]'),
                mengingat: formData.getAll('mengingat[]'),
                memperhatikan: formData.getAll('memperhatikan[]'),
                menetapkan: formData.get('menetapkan') || '',
                diktum: formData.getAll('diktum[]'),
                ditetapkan_di: formData.get('ditetapkan_di') || '',
                jabatan_penandatangan: formData.get('jabatan_penandatangan') || '',
                nama_penandatangan: formData.get('nama_penandatangan') || '',
            };
        }

        function showAutosaveToast() {
            const toast = document.getElementById('autosave-toast');
            if (!toast) {
                return;
            }

            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');

            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
            }, 1000);
        }

        function bindLampiranRemoveButtons() {
            document.addEventListener('click', event => {
                const trigger = event.target.closest('[data-remove-lampiran=\"true\"]');
                if (!trigger) {
                    return;
                }

                const lampiranPath = trigger.getAttribute('data-lampiran-path');
                if (!lampiranPath) {
                    return;
                }

                const hiddenContainer = document.getElementById('remove-lampiran-inputs');
                if (!hiddenContainer) {
                    return;
                }

                const exists = Array.from(hiddenContainer.querySelectorAll('input[name=\"remove_lampiran[]\"]'))
                    .some(input => input.value === lampiranPath);
                if (!exists) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'remove_lampiran[]';
                    input.value = lampiranPath;
                    hiddenContainer.appendChild(input);
                }

                const row = trigger.closest('li');
                if (row) {
                    row.remove();
                }

                scheduleAutosave(!isInitializing);
            });
        }
    </script>
@endpush
