<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSkDataReady
{
    public function handle(Request $request, Closure $next): Response
    {
        $data = session()->get('sk_data', []);

        if (!$this->isReady($data)) {
            return redirect()
                ->route('sk.create', ['edit' => 1])
                ->withErrors([
                    'draft' => 'Data SK belum lengkap. Lengkapi form terlebih dahulu sebelum membuka preview.',
                ]);
        }

        return $next($request);
    }

    private function isReady(array $data): bool
    {
        if (trim((string) ($data['nomor_surat'] ?? '')) === '') {
            return false;
        }

        if (trim((string) ($data['sk_title'] ?? '')) === '') {
            return false;
        }

        if (trim((string) ($data['menetapkan'] ?? '')) === '') {
            return false;
        }

        if (trim((string) ($data['pada_tanggal'] ?? '')) === '') {
            return false;
        }

        if (!$this->hasNonEmptyItems($data['menimbang'] ?? [])) {
            return false;
        }

        if (!$this->hasNonEmptyItems($data['mengingat'] ?? [])) {
            return false;
        }

        if (!$this->hasNonEmptyItems($data['diktum'] ?? [])) {
            return false;
        }

        return true;
    }

    private function hasNonEmptyItems(mixed $items): bool
    {
        if (!is_array($items)) {
            return false;
        }

        foreach ($items as $item) {
            if (is_string($item) && trim($item) !== '') {
                return true;
            }
        }

        return false;
    }
}
