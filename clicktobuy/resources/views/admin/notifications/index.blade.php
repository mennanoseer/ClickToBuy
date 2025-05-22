@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Notifications</h1>
        <a href="{{ route('admin.notifications.markAllAsRead') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-check fa-sm text-white-50"></i> Mark All as Read
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="list-group list-group-flush notification-list">
                @forelse($notifications as $notification)
                    <div class="list-group-item {{ $notification->read_at ? '' : 'unread' }}">
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
                                <div class="mt-2">
                                    <a href="{{ $notification->data['link'] ?? '#' }}" class="btn btn-sm btn-info">
                                        View Details
                                    </a>
                                    @if(!$notification->read_at)
                                        <a href="{{ route('admin.notifications.markAsRead', $notification->id) }}" class="btn btn-sm btn-secondary">
                                            Mark as Read
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item">
                        <p class="mb-0 text-center py-5">No notifications found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-center">
        {{ $notifications->links() }}
    </div>
</div>
@endsection

@section('styles')
<style>
    .notification-list .unread {
        background-color: rgba(78, 115, 223, 0.05);
        border-left: 4px solid #4e73df;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .bg-primary {
        background-color: #4e73df;
    }
    
    .bg-success {
        background-color: #1cc88a;
    }
    
    .bg-warning {
        background-color: #f6c23e;
    }
    
    .bg-danger {
        background-color: #e74a3b;
    }
    
    .bg-info {
        background-color: #36b9cc;
    }
</style>
@endsection
