<!-- Notification Toast Container -->
<div id="notification-container" class="fixed top-4 right-4 z-50 space-y-3"></div>

<script>
    // Notification system
    window.showNotification = function(message, type = 'success') {
        const container = document.getElementById('notification-container');
        const id = 'notif-' + Date.now();

        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };

        const icons = {
            success: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>`,
            error: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>`,
            warning: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>`,
            info: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>`
        };

        const notification = document.createElement('div');
        notification.id = id;
        notification.className = `flex items-center p-4 ${colors[type]} text-white rounded-apple-lg shadow-apple-lg transform transition-all duration-300 translate-x-full max-w-md`;
        notification.innerHTML = `
            <div class="flex-shrink-0 mr-3">
                ${icons[type]}
            </div>
            <div class="flex-1 text-sm font-medium">
                ${message}
            </div>
            <button onclick="dismissNotification('${id}')" class="ml-3 flex-shrink-0 hover:opacity-75">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        `;

        container.appendChild(notification);

        // Slide in animation
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            dismissNotification(id);
        }, 5000);
    };

    window.dismissNotification = function(id) {
        const notification = document.getElementById(id);
        if (notification) {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    };

    // Show Laravel session flash messages
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showNotification({!! json_encode(session('success')) !!}, 'success');
        @endif

        @if(session('error'))
            showNotification({!! json_encode(session('error')) !!}, 'error');
        @endif

        @if(session('warning'))
            showNotification({!! json_encode(session('warning')) !!}, 'warning');
        @endif

        @if(session('info'))
            showNotification({!! json_encode(session('info')) !!}, 'info');
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                showNotification({!! json_encode($error) !!}, 'error');
            @endforeach
        @endif
    });
</script>

