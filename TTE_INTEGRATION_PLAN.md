# TTE (Tanda Tangan Elektronik) Integration Plan

## Current Implementation (Dummy/Placeholder)

-   Status approval: `Telah Ditandatangani Dekan`
-   Status rejection: `Ditolak Dekan`
-   Komentar penolakan disimpan di `data_spesifik` JSON column

## Future Integration: QR Code Digital Signature

### 1. Database Schema Additions

Tambahkan kolom di tabel `Tugas_Surat`:

```sql
ALTER TABLE Tugas_Surat ADD COLUMN signature_qr_data TEXT NULL COMMENT 'JSON data untuk QR signature';
ALTER TABLE Tugas_Surat ADD COLUMN qr_image_path VARCHAR(255) NULL COMMENT 'Path ke file QR code image';
```

### 2. QR Code Generation (Library Options)

**Option A: SimpleSoftwareIO/simple-qrcode (Recommended)**

```bash
composer require simplesoftwareio/simple-qrcode
```

**Option B: External API**

-   QRServer API: `https://api.qrserver.com/v1/create-qr-code/`
-   GoQR API
-   QR Code Monkey API

### 3. QR Signature Data Structure

```json
{
    "qr_token": "unique-32-char-token",
    "signed_by_id": 123,
    "signed_by_name": "Prof. Dr. Nama Dekan",
    "signed_by_role": "Dekan",
    "signed_at": "2025-11-15T10:30:00Z",
    "document_id": "Id_Tugas_Surat",
    "document_hash": "sha256-hash-of-file",
    "qr_image_url": "/storage/signatures/qr_xxxxx.png",
    "verification_url": "https://domain.com/verify-signature/token"
}
```

### 4. Implementation Steps

#### Step 1: Generate QR on Approval

```php
// In DetailSuratController@approve()
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

$qrToken = Str::random(32);
$verificationUrl = route('signature.verify', $qrToken);

// Generate QR image
$qrImage = QrCode::format('png')
    ->size(200)
    ->errorCorrection('H')
    ->generate($verificationUrl);

// Save QR to storage
$qrPath = "signatures/qr_{$tugasSurat->Id_Tugas_Surat}_{$qrToken}.png";
Storage::disk('public')->put($qrPath, $qrImage);

// Save signature data
$tugasSurat->signature_qr_data = json_encode([
    'qr_token' => $qrToken,
    'signed_by_id' => $user->Id_User,
    'signed_by_name' => $user->Name_User,
    'signed_by_role' => $user->role->Name_Role,
    'signed_at' => Carbon::now()->toIso8601String(),
    'document_id' => $tugasSurat->Id_Tugas_Surat,
    'qr_image_url' => $qrPath,
    'verification_url' => $verificationUrl,
]);
$tugasSurat->qr_image_path = $qrPath;
```

#### Step 2: Public Verification Route

```php
// routes/web.php (outside auth middleware)
Route::get('/verify-signature/{token}', [App\Http\Controllers\SignatureVerificationController::class, 'verify'])
    ->name('signature.verify');
```

#### Step 3: Verification Controller

```php
// app/Http/Controllers/SignatureVerificationController.php
public function verify($token)
{
    $surat = TugasSurat::whereRaw("JSON_EXTRACT(signature_qr_data, '$.qr_token') = ?", [$token])->first();

    if (!$surat) {
        abort(404, 'Signature not found or invalid');
    }

    $signatureData = json_decode($surat->signature_qr_data, true);

    return view('public.verify_signature', [
        'surat' => $surat,
        'signature' => $signatureData,
        'isValid' => true, // Add hash verification logic
    ]);
}
```

#### Step 4: Public Verification View

```blade
{{-- resources/views/public/verify_signature.blade.php --}}
<div class="container py-5">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4><i class="fas fa-check-circle"></i> Valid Digital Signature</h4>
        </div>
        <div class="card-body">
            <h5>Document Information</h5>
            <p><strong>Document ID:</strong> {{ $surat->Id_Tugas_Surat }}</p>
            <p><strong>Document Type:</strong> {{ $surat->jenisSurat->Nama_Surat }}</p>

            <hr>

            <h5>Signature Details</h5>
            <p><strong>Signed By:</strong> {{ $signature['signed_by_name'] }}</p>
            <p><strong>Position:</strong> {{ $signature['signed_by_role'] }}</p>
            <p><strong>Signed At:</strong> {{ Carbon::parse($signature['signed_at'])->format('d M Y H:i') }}</p>

            <div class="alert alert-success mt-3">
                This signature is valid and verified.
            </div>
        </div>
    </div>
</div>
```

#### Step 5: Embed QR in PDF Preview

Di halaman admin preview surat (nanti):

```blade
{{-- Show QR in signature section --}}
@if($surat->qr_image_path)
    <div class="signature-section">
        <p>Ditandatangani secara elektronik oleh:</p>
        <p><strong>{{ $signatureData['signed_by_name'] }}</strong></p>
        <p>{{ Carbon::parse($signatureData['signed_at'])->format('d M Y H:i') }}</p>
        <img src="{{ asset('storage/' . $surat->qr_image_path) }}" alt="QR Signature" width="150">
        <p><small>Scan QR untuk verifikasi</small></p>
    </div>
@endif
```

### 5. Security Considerations

-   Store document hash (SHA-256 of PDF) in signature data
-   Implement signature expiration/revocation
-   Log all signature verifications
-   Rate limit verification endpoint
-   Consider using asymmetric cryptography for extra security

### 6. Testing Checklist

-   [ ] QR generation works on approval
-   [ ] QR image stored correctly
-   [ ] Verification page loads correctly
-   [ ] Verification shows correct signer info
-   [ ] QR displays in PDF preview
-   [ ] Mobile scan redirects properly
-   [ ] Invalid/tampered tokens return 404

### 7. Libraries & Resources

-   **QR Generation**: simplesoftwareio/simple-qrcode
-   **PDF Generation**: barryvdh/laravel-dompdf or barryvdh/laravel-snappy
-   **Hashing**: Laravel built-in Hash facade
-   **QR Scanner Testing**: Use any mobile QR scanner app

---

**Status:** Placeholder implemented, ready for QR integration
**Priority:** Medium (implement after core approval flow is stable)
**Estimated Time:** 4-6 hours for full integration
