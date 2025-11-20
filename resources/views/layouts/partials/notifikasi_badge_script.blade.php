{{-- Script untuk update badge notifikasi --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load notification count
    loadNotificationCount();
    
    // Refresh every 60 seconds
    setInterval(loadNotificationCount, 60000);
    
    function loadNotificationCount() {
        fetch('{{ route("notifikasi.recent") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notifBadge');
                if (badge) {
                    const count = data.unread_count || 0;
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading notification count:', error);
            });
    }
});
</script>
