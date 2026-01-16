@extends('layouts.' . $layout)

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Notifikasi</h1>
        <div class="btn-group">
            @if($unreadCount > 0)
            <form action="{{ route('notifikasi.markAllRead') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-check-double me-1"></i>Tandai Semua Sudah Dibaca
                </button>
            </form>
            @endif
            @if($notifikasis->total() > 0)
            <form action="{{ route('notifikasi.deleteAll') }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Hapus semua notifikasi Anda? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash-alt me-1"></i>Hapus Semua
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            @forelse($notifikasis as $notif)
            <div class="d-flex align-items-start mb-3 pb-3 border-bottom {{ !$notif->Is_Read ? 'bg-light p-3 rounded' : '' }} position-relative" style="cursor: pointer;" onclick="window.location.href='{{ route('notifikasi.markReadRedirect', $notif->Id_Notifikasi) }}'">
                <div class="me-3">
                    @if($notif->Tipe_Notifikasi == 'surat')
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-envelope fa-lg"></i>
                        </div>
                    @elseif($notif->Tipe_Notifikasi == 'approval')
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                    @elseif($notif->Tipe_Notifikasi == 'rejection')
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-times-circle fa-lg"></i>
                        </div>
                    @else
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-bell fa-lg"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="w-100">
                            <p class="mb-1 {{ !$notif->Is_Read ? 'fw-bold' : '' }}">{{ $notif->Pesan }}</p>
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>
                                Dari: {{ $notif->sourceUser->Name_User ?? 'Sistem' }}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $notif->created_at ? $notif->created_at->diffForHumans() : 'Baru saja' }}
                            </small>
                            
                            {{-- Action buttons untuk invitation --}}
                            @if(strtolower($notif->Tipe_Notifikasi) == 'invitation')
                                @php
                                    $dataTambahan = is_array($notif->Data_Tambahan) ? (object)$notif->Data_Tambahan : json_decode($notif->Data_Tambahan ?? '{}');
                                    $invitation = isset($dataTambahan->invitation_id) ? \App\Models\SuratMagangInvitation::find($dataTambahan->invitation_id) : null;
                                @endphp
                                
                                @if($invitation && $invitation->status === 'pending')
                                    <div class="mt-2" onclick="event.stopPropagation();">
                                        <form action="{{ route('mahasiswa.invitation.accept', $invitation->id_invitation) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check me-1"></i>Terima
                                            </button>
                                        </form>
                                        
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $invitation->id_invitation }}">
                                            <i class="fas fa-times me-1"></i>Tolak
                                        </button>
                                    </div>
                                    
                                    {{-- Modal untuk reject --}}
                                    <div class="modal fade" id="rejectModal{{ $invitation->id_invitation }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('mahasiswa.invitation.reject', $invitation->id_invitation) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Undangan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Alasan menolak <span class="text-danger">*</span></label>
                                                            <textarea name="keterangan" class="form-control" rows="3" required placeholder="Minimal 5 karakter"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak Undangan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($invitation)
                                    <span class="badge bg-{{ $invitation->status === 'accepted' ? 'success' : 'danger' }} mt-2">
                                        {{ $invitation->status === 'accepted' ? 'Diterima' : 'Ditolak' }}
                                    </span>
                                @endif
                            @endif
                        </div>
                        <div class="btn-group" onclick="event.stopPropagation();">
                            <form action="{{ route('notifikasi.delete', $notif->Id_Notifikasi) }}" method="POST" class="d-inline" onsubmit="event.stopPropagation(); return confirm('Hapus notifikasi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada notifikasi</p>
            </div>
            @endforelse

            @if($notifikasis->hasPages())
            <div class="mt-3">
                {{ $notifikasis->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
