<style>
    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #000000;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        margin: 0 auto;
    }

    .notifications-dropdown {
        max-height: 400px;
        /* Fixed height for dropdown */
        overflow-y: auto;
        /* Enable vertical scrolling */
        width: 600px;
        /* Fixed width for consistency */
    }

    .notification-item.unread {
        background-color: #f8f9fa;
        /* Highlight unread notifications */
    }

    .delete-all-btn {
        width: 100%;
        text-align: center;
        padding: 8px;
        margin-top: 8px;
        border-top: 1px solid #e9ecef;
    }

    .dropdown-item {
    width: 100%; /* pastikan full lebar */
    display: block; /* jaga block-level untuk penuh lebar */
    white-space: normal; /* izinkan teks wrapping */
    word-wrap: break-word; /* putus kata jika perlu */
}
.notification-item h6 {
    white-space: normal;
    word-break: break-word; /* cegah teks panjang overflow */
}

</style>

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">{{ $slot }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center"></div>
            <ul class="navbar-nav justify-content-end">
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li>
                <li class="nav-item dropdown pe-2 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                        <i class="fa fa-bell text-lg mx-3 cursor-pointer"></i>
                        @if (!empty($notifications) && is_array($notifications) && count($notifications) > 0)
                            <span class="badge badge-sm badge-circle position-absolute"
                                style="top: 5px; right: 45px; background-color: #f44336; color: #fff;"
                                id="notification-badge">
                                {{ count(array_filter($notifications, fn($n) => !$n['is_read'])) }}
                            </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-3 px-3 me-sm-n4 notifications-dropdown">
                        <li class="px-3 pb-2 d-flex justify-content-between align-items-center">
                            <span class="text-dark fw-bold">Notifications</span>
                        @if (!empty($notifications) && is_array($notifications) && count($notifications) > 0)

                            <button class="btn btn-sm btn-link text-danger p-0" onclick="deleteAllNotifications()"
                                style="text-decoration: none;">
                                Delete All
                            </button>
                            @endif
                        </li>
                        <div id="notifications-list">
                            @if (!empty($notifications) && is_array($notifications) && count($notifications) > 0)
                                @foreach ($notifications as $notification)
                                    <li class="mb-2">
                                        <a class="dropdown-item border-radius-md notification-item {{ $notification['is_read'] ? '' : 'unread' }}"
                                            href="javascript:;" data-id="{{ $notification['sub_id'] }}">
                                            <div class="d-flex py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="text-sm font-weight-normal mb-1 text-wrap">
                                                        {{ $notification['message'] }}</h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        <i class="fa fa-clock me-1"></i>
                                                        {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="mb-2">
                                    <a class="dropdown-item border-radius-md" href="javascript:;">
                                        <div class="d-flex py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="text-sm font-weight-normal mb-1">No new notifications</h6>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endif
                        </div>
                        {{-- @if (!empty($notifications) && is_array($notifications) && count($notifications) > 0)
                            <li class="delete-all-btn">
                                <button class="btn btn-sm btn-danger" onclick="deleteAllNotifications()">Delete
                                    All</button>
                            </li>
                        @endif --}}
                    </ul>
                </li>
                <li class="nav-item dropdown pe-2 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body font-weight-bold px-0" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false" title="Profile">
                        <i class="fa fa-user text-lg me-sm-1"></i>
                        <span class="d-sm-inline d-none">{{ session('name') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                        <div class="row">
                            <div class="col-12 text-center mb-3 mt-2">
                                <div class="avatar">
                                    <i class="fa fa-user"></i>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <b>{{ session('sect') }}</b> <br> <em>{{ session('department') }}</em>
                            </div>
                            <hr class="mt-4">
                            <a class="col-12 text-center mt-1" href="{{ route('login') }}">
                                <i class="fa-solid fa-door-open text-danger"></i>
                                <span class="text-danger mx-2">Log Out</span>
                            </a>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initial binding of click events
        rebindClickEvents();

        // Poll for new notifications every 30 seconds
        setInterval(fetchNotifications, 30000);
        fetchNotifications(); // Initial fetch
    });

    function fetchNotifications() {
        fetch('/notifications')
            .then(response => response.json())
            .then(data => {
                const notificationsList = document.getElementById('notifications-list');
                const deleteAllBtn = document.querySelector('.delete-all-btn');
                if (data.length > 0) {
                    notificationsList.innerHTML = data.map(notification => `
                    <li class="mb-2">
                        <a class="dropdown-item border-radius-md notification-item ${notification.is_read ? '' : 'unread'}" href="javascript:;" data-id="${notification.sub_id}">
                            <div class="d-flex py-1">
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="text-sm font-weight-normal mb-1">${notification.message}</h6>
                                    <p class="text-xs text-secondary mb-0">
                                        <i class="fa fa-clock me-1"></i>
                                        ${new Date(notification.created_at).toLocaleString()}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </li>
                `).join('');
                    deleteAllBtn.style.display = 'block';
                } else {
                    notificationsList.innerHTML = `
                    <li class="mb-2">
                        <a class="dropdown-item border-radius-md" href="javascript:;">
                            <div class="d-flex py-1">
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="text-sm font-weight-normal mb-1">No new notifications</h6>
                                </div>
                            </div>
                        </a>
                    </li>
                `;
                    deleteAllBtn.style.display = 'none';
                }
                updateBadgeCount();
                rebindClickEvents();
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    // function deleteAllNotifications() {
    //     if (confirm('Are you sure you want to delete all notifications?')) {
    //         fetch('/notifications/delete-all', {
    //                 method: 'POST',
    //                 headers: {
    //                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    //                     'Content-Type': 'application/json'
    //                 }
    //             })
    //             .then(response => response.json())
    //             .then(data => {
    //                 if (data.success) {
    //                     fetchNotifications(); // Refresh notifications
    //                 } else {
    //                     alert('Failed to delete notifications.');
    //                 }
    //             })
    //             .catch(error => console.error('Error deleting notifications:', error));
    //     }
    // }

    function deleteAllNotifications() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete all notifications permanently!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete all!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/notifications/delete-all', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Deleted!', 'All notifications have been deleted.', 'success');
                            fetchNotifications();
                        } else {
                            Swal.fire('Failed', 'Failed to delete notifications.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting notifications:', error);
                        Swal.fire('Error', 'Something went wrong.', 'error');
                    });
            }
        });
    }

    function updateBadgeCount() {
        const unreadCount = document.querySelectorAll('.notification-item.unread').length;
        const badge = document.getElementById('notification-badge');
        if (badge) {
            badge.textContent = unreadCount;
            badge.style.display = unreadCount > 0 ? 'flex' : 'none';
        }
    }

    function rebindClickEvents() {
        document.querySelectorAll('.notification-item').forEach(item => {
            item.removeEventListener('click', handleNotificationClick); // Remove existing listeners
            item.addEventListener('click', handleNotificationClick);
        });
    }

    function handleNotificationClick() {
        const notificationId = this.getAttribute('data-id');
        if (this.classList.contains('unread')) {
            fetch(`/notifications/mark-as-read/${notificationId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.classList.remove('unread');
                        updateBadgeCount();
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
        }
    }
</script>
