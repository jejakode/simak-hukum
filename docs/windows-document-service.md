# Windows Document Worker Service (DOCX/PDF) for Linux Core App

## 1) Tujuan

Dokumen ini mendefinisikan arsitektur dan kontrak implementasi untuk memindahkan proses manipulasi DOCX/PDF dari server Linux utama ke service terpisah di Windows.

Target utama:
- Aplikasi utama tetap berjalan di Linux.
- Proses dokumen yang butuh kompatibilitas Windows dijalankan di VM Windows (Proxmox).
- Integrasi antar sistem dilakukan melalui API internal yang stabil, aman, dan bisa di-scale.

## 2) Scope

In scope:
- Generate dokumen dari template DOCX.
- Konversi DOCX ke PDF.
- Operasi PDF dasar (merge/split/watermark opsional, fase lanjut).
- API job-based (asynchronous) + status tracking.
- Integrasi dengan object storage (MinIO/S3-compatible).

Out of scope (fase berikutnya):
- OCR.
- Digital signing tersertifikasi.
- Workflow approval bisnis.

## 3) Arsitektur Target

Komponen:
- `Linux Core App` (Laravel): submit job + polling status.
- `Redis/RabbitMQ` (opsional, direkomendasikan): antrean job.
- `Windows Document Worker API` (.NET 8): terima request, validasi, proses dokumen.
- `Object Storage` (MinIO/S3): simpan input/output file.
- `Metadata DB` (PostgreSQL/MySQL/SQL Server): simpan status job, error, audit.

Alur tinggi:
1. Linux upload template/input ke object storage.
2. Linux memanggil `POST /v1/jobs` ke Windows service (hanya metadata + pointer file).
3. Service membentuk job `QUEUED`.
4. Worker memproses `QUEUED -> PROCESSING -> SUCCEEDED/FAILED`.
5. Linux cek `GET /v1/jobs/{jobId}` lalu ambil hasil lewat `result_url` atau endpoint result.

## 4) Keputusan Teknis

- Runtime: `.NET 8` ASP.NET Core Web API.
- DOCX templating: `Open XML SDK`.
- Konversi DOCX->PDF:
  - Rekomendasi produksi: `Aspose.Words` (berbayar, fidelity tinggi).
  - Alternatif: LibreOffice headless (jika menerima gap format).
- PDF opsional: `iText7`/`PdfSharp`.
- Host mode:
  - Jalankan sebagai `Windows Service` (Kestrel + reverse proxy opsional).
  - Hindari Microsoft Office COM Interop di server produksi.

## 5) API Contract (Ringkas)

Spesifikasi detail: `docs/openapi/document-worker-api.yaml`.

Endpoint minimal:
- `POST /v1/jobs`
- `GET /v1/jobs/{jobId}`
- `GET /v1/jobs/{jobId}/result`
- `GET /health/live`
- `GET /health/ready`

Header penting:
- `Authorization: Bearer <token-internal>`
- `Idempotency-Key: <uuid>` pada create job.
- `X-Correlation-Id: <trace-id>` untuk observability.

Status job:
- `QUEUED`
- `PROCESSING`
- `SUCCEEDED`
- `FAILED`
- `CANCELED` (opsional fase 2)

## 6) Request/Response Konseptual

Contoh `POST /v1/jobs`:

```json
{
  "job_type": "docx_to_pdf",
  "priority": "normal",
  "input": {
    "template_url": "s3://bucket/templates/sk-template-001.docx",
    "payload": {
      "nama": "Budi",
      "nomor_perkara": "123/ABC/2026"
    }
  },
  "output": {
    "format": "pdf",
    "target_path": "s3://bucket/output/2026/04/"
  },
  "callback": {
    "url": "https://linux-app.internal/api/document-jobs/callback",
    "auth_token": "optional-shared-secret"
  },
  "metadata": {
    "requested_by": "system",
    "source_app": "simak-hukum"
  }
}
```

Contoh respons:

```json
{
  "job_id": "01HSZ9WYX8QFX4QJ7MQET7WQ8J",
  "status": "QUEUED",
  "created_at": "2026-04-08T07:40:10Z"
}
```

## 7) Mekanisme Reliability

- Idempotency:
  - Kombinasi `(client_id, idempotency_key)` unik.
  - Request ulang dengan key sama harus mengembalikan job yang sama.
- Retry:
  - Retry transient (network/storage timeout) max 3 dengan exponential backoff.
  - Error template/data invalid -> no retry otomatis.
- Timeout:
  - Per job default 120 detik (sesuaikan ukuran dokumen).
- Dead-letter:
  - Job gagal berulang dipindah ke status `FAILED` + reason code.

## 8) Security Baseline

- Akses API hanya jaringan internal (VLAN/private subnet).
- Wajib token service-to-service; idealnya mTLS.
- Whitelist IP Linux app ke Windows service.
- Object storage akses via credential scoped (least privilege).
- Jangan log payload sensitif penuh.
- Enkripsi in-transit (HTTPS/TLS internal CA) dan at-rest (disk/object storage).

## 9) Observability & Audit

Logging wajib (structured JSON):
- `timestamp`, `level`, `correlation_id`, `job_id`, `event`, `duration_ms`, `result`.

Metrics minimal:
- Total jobs per status.
- Durasi processing (p50/p95/p99).
- Error rate per `job_type`.
- Queue depth.

Audit trail:
- Requested by, sumber request, waktu submit, waktu selesai, hash output file.

## 10) Deployment di VM Windows (Proxmox)

Spesifikasi awal produksi kecil-menengah:
- vCPU: 4
- RAM: 8-16 GB
- Disk: 100 GB NVMe/SSD
- Network: virtio, private network + ACL

Software:
- Windows 10 (sementara lab) atau idealnya Windows Server 2022.
- .NET 8 Hosting Bundle/runtime.
- Service binary `DocumentWorker.Api`.

Direktori:
- `C:\Services\DocumentWorker\app\`
- `C:\Services\DocumentWorker\logs\`
- `C:\Services\DocumentWorker\temp\`

Set environment (contoh):
- `DOCWORKER__STORAGE__ENDPOINT`
- `DOCWORKER__STORAGE__ACCESSKEY`
- `DOCWORKER__STORAGE__SECRETKEY`
- `DOCWORKER__AUTH__TOKENS__0`
- `DOCWORKER__QUEUE__MODE=InMemory|Redis|RabbitMQ`

## 11) Integrasi di Linux (Laravel)

Tambahkan config:
- `DOCWORKER_BASE_URL`
- `DOCWORKER_TOKEN`
- `DOCWORKER_TIMEOUT`
- `DOCWORKER_RETRY`

Pola implementasi:
1. Upload input/template ke object storage.
2. Submit job ke Windows API dengan idempotency key.
3. Simpan `job_id` di tabel lokal (`document_jobs`).
4. Polling periodik via scheduler/queue worker.
5. Saat `SUCCEEDED`, tarik `result_url` dan update entitas bisnis.

## 12) Data Model Minimal (Metadata DB)

Tabel `document_jobs`:
- `id` (uuid/ulid)
- `source_app`
- `external_idempotency_key`
- `job_type`
- `status`
- `input_ref` (json)
- `output_ref` (json)
- `error_code` (nullable)
- `error_message` (nullable)
- `attempt` (int)
- `created_at`, `updated_at`, `started_at`, `finished_at`

Unique index:
- `(source_app, external_idempotency_key)`

## 13) Error Code Standar

- `INVALID_TEMPLATE`
- `INVALID_PAYLOAD`
- `STORAGE_UNREACHABLE`
- `CONVERSION_FAILED`
- `TIMEOUT_EXCEEDED`
- `UNAUTHORIZED`
- `INTERNAL_ERROR`

## 14) Non-Functional Requirements

- Availability target internal: 99.5%.
- P95 processing:
  - Template ringan < 10 detik.
  - Template kompleks < 45 detik.
- Maksimum ukuran file awal: 20 MB (fase awal).
- Concurrency awal: 5 worker paralel (tuning sesuai CPU/RAM).

## 15) Checklist Implementasi Untuk Agent

1. Scaffold project `DocumentWorker.Api` (.NET 8).
2. Implement auth middleware bearer token internal.
3. Implement endpoint health + jobs (create/get/result).
4. Implement persistence `document_jobs`.
5. Implement idempotency guard.
6. Implement worker processor async + retry policy.
7. Integrasi object storage adapter.
8. Implement DOCX templating + DOCX->PDF provider abstraction.
9. Tambah structured logging + metrics endpoint.
10. Tambah integration test minimal:
   - create job success.
   - duplicate idempotency key.
   - failed conversion path.
   - authorized vs unauthorized.

## 16) Rencana Fase

Fase 1 (MVP):
- `docx_to_pdf` + `template_fill`.
- Async job + polling.
- Bearer auth + internal network restriction.

Fase 2:
- Callback webhook.
- Cancel job.
- PDF utilities (merge/split/watermark).

Fase 3:
- Horizontal scaling multi-worker.
- Dashboard monitoring dan alerting.

## 17) Asumsi

- Linux app sudah memiliki object storage.
- Jaringan Linux -> Windows internal bisa diakses private.
- Tidak memakai Office COM automation.
- Build awal fokus stabilitas, bukan throughput tinggi.

