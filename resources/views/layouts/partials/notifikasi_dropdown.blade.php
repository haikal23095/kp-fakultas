{{-- Dropdown Notifikasi --}}
<div class="dropdown me-3">
    <a href="#" class="text-white text-decoration-none position-relative" id="dropdownNotif" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell fa-lg"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notif-badge" id="notifBadge" style="display: none; font-size: 0.65rem;">
            0
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" aria-labelledby="dropdownNotif" style="width: 350px; max-height: 500px; overflow-y: auto;">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
            <span class="fw-bold">Notifikasi</span>
            <a href="{{ route('notifikasi.index') }}" class="text-white text-decoration-none small">Lihat Semua</a>
        </div>
        <div class="dropdown-divider"></div>
        <div id="notifDropdownContent">
            <div class="text-center py-3">
                <i class="fas fa-spinner fa-spin"></i>
                <p class="small mb-0 mt-2">Memuat notifikasi...</p>
            </div>
        </div>
    </div>
</div>

{{-- Script untuk load notifikasi --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load notifikasi saat dropdown dibuka
    const dropdownNotif = document.getElementById('dropdownNotif');
    
    if (dropdownNotif) {
        dropdownNotif.addEventListener('click', function(e) {
            loadNotifications();
        });
        
        // Load initial count
        loadNotifications();
    }
    
    function loadNotifications() {
        fetch('{{ route("notifikasi.recent") }}')
            .then(response => response.json())
            .then(data => {
                updateNotificationBadge(data.unread_count);
                updateNotificationDropdown(data.notifikasis);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
    }
    
    function updateNotificationBadge(count) {
        const badge = document.getElementById('notifBadge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    }
    
    function updateNotificationDropdown(notifikasis) {
        const content = document.getElementById('notifDropdownContent');
        if (!content) return;
        
        if (notifikasis.length === 0) {
            content.innerHTML = `
                <div class="text-center py-3">
                    <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                    <p class="small mb-0 text-muted">Tidak ada notifikasi</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        notifikasis.forEach(notif => {
            const isUnread = !notif.Is_Read;
            const bgClass = isUnread ? 'bg-secondary bg-opacity-25' : '';
            const boldClass = isUnread ? 'fw-bold' : '';
            const sourceName = notif.source_user ? notif.source_user.Name_User : 'Sistem';
            
            // Format waktu
            const createdAt = new Date(notif.created_at);
            const now = new Date();
            const diffMs = now - createdAt;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            let timeText = '';
            if (diffMins < 1) timeText = 'Baru saja';
            else if (diffMins < 60) timeText = diffMins + ' menit yang lalu';
            else if (diffHours < 24) timeText = diffHours + ' jam yang lalu';
            else timeText = diffDays + ' hari yang lalu';
            
            // Icon berdasarkan tipe
            let icon = 'fa-bell';
            let iconColor = 'text-info';
            if (notif.Tipe_Notifikasi === 'surat') {
                icon = 'fa-envelope';
                iconColor = 'text-primary';
            } else if (notif.Tipe_Notifikasi === 'approval') {
                icon = 'fa-check-circle';
                iconColor = 'text-success';
            } else if (notif.Tipe_Notifikasi === 'rejection') {
                icon = 'fa-times-circle';
                iconColor = 'text-danger';
            }
            
            html += `
                <a href="{{ route('notifikasi.index') }}" class="dropdown-item ${bgClass}" style="white-space: normal;">
                    <div class="d-flex align-items-start py-1">
                        <i class="fas ${icon} ${iconColor} me-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <p class="mb-1 small ${boldClass}">${notif.Pesan}</p>
                            <small class="text-muted d-block">
                                <i class="fas fa-user me-1"></i>${sourceName}
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-clock me-1"></i>${timeText}
                            </small>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
            `;
        });
        
        content.innerHTML = html;
    }
    
    // Reload notifikasi setiap 60 detik
    setInterval(loadNotifications, 60000);
});
</script>
