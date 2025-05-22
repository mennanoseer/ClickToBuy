// Admin Notification System
document.addEventListener('DOMContentLoaded', function() {
    // Check for new notifications every minute
    function checkNotifications() {
        fetch('/admin/api/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                const notificationBadge = document.querySelector('.notification-dropdown .badge-counter');
                
                if (data.count > 0) {
                    // If the badge doesn't exist, create it
                    if (!notificationBadge) {
                        const badge = document.createElement('span');
                        badge.className = 'badge badge-danger badge-counter';
                        badge.textContent = data.count;
                        document.querySelector('.notification-dropdown > a').appendChild(badge);
                    } else {
                        // Update existing badge
                        notificationBadge.textContent = data.count;
                    }
                } else if (notificationBadge) {
                    // Remove badge if count is 0
                    notificationBadge.remove();
                }
            })
            .catch(error => console.error('Error checking notifications:', error));
    }
    
    // Mark notification as read when clicked
    document.addEventListener('click', function(e) {
        const notificationItem = e.target.closest('.notification-item');
        if (notificationItem && notificationItem.classList.contains('unread')) {
            const notificationId = notificationItem.dataset.id;
            if (notificationId) {
                fetch(`/admin/notifications/${notificationId}/mark-read`)
                    .then(response => {
                        if (response.ok) {
                            notificationItem.classList.remove('unread');
                            checkNotifications();
                        }
                    })
                    .catch(error => console.error('Error marking notification as read:', error));
            }
        }
    });
    
    // Mark all notifications as read
    const markAllReadLink = document.querySelector('.mark-all-read');
    if (markAllReadLink) {
        markAllReadLink.addEventListener('click', function(e) {
            e.preventDefault();
            fetch('/admin/notifications/mark-all-read')
                .then(response => {
                    if (response.ok) {
                        document.querySelectorAll('.notification-item.unread').forEach(item => {
                            item.classList.remove('unread');
                        });
                        checkNotifications();
                    }
                })
                .catch(error => console.error('Error marking all notifications as read:', error));
        });
    }
    
    // Check for notifications on page load and every minute
    checkNotifications();
    setInterval(checkNotifications, 60000); // 60000 ms = 1 minute
});
