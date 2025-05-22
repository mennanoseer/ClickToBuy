<!-- Admin Notifications -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Notifications</h6>
        <a href="#" class="mark-all-read">Mark All as Read</a>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush notification-list">
            @forelse($notifications as $notification)
                <a href="{{ $notification->data['link'] ?? '#' }}" class="list-group-item list-group-item-action notification-item {{ $notification->read_at ? '' : 'unread' }}" data-id="{{ $notification->id }}">
                    <div class="d-flex w-100 align-items-center">
                        <div class="notification-icon {{ $notification->data['icon_class'] ?? 'bg-primary' }} mr-3">
                            <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }}"></i>
                        </div>
                        <div class="notification-content flex-grow-1">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $notification->data['title'] }}</h6>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $notification->data['message'] }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="list-group-item">
                    <p class="mb-0 text-center py-3">No new notifications</p>
                </div>
            @endforelse
        </div>
    </div>
    <div class="card-footer text-center">
        <a href="{{ route('admin.notifications.index') }}">View All Notifications</a>
    </div>
</div>
