// Dashboard functionality

document.addEventListener('DOMContentLoaded', function() {
    // Animate stats on load
    animateStats();

    // Auto-refresh activity every 30 seconds
    setInterval(function() {
        refreshRecentActivity();
    }, 30000);
});

function animateStats() {
    const statElements = document.querySelectorAll('.stat-value');

    statElements.forEach(function(element) {
        const finalValue = parseInt(element.textContent);
        let currentValue = 0;
        const increment = Math.ceil(finalValue / 20);
        const duration = 1000;
        const stepTime = duration / (finalValue / increment);

        const timer = setInterval(function() {
            currentValue += increment;
            if (currentValue >= finalValue) {
                currentValue = finalValue;
                clearInterval(timer);
            }
            element.textContent = currentValue;
        }, stepTime);
    });
}

function refreshRecentActivity() {
    // Only refresh if user is on dashboard
    if (window.location.pathname === '/dashboard') {
        fetch('/dashboard/activity')
            .then(response => response.json())
            .then(data => {
                updateActivityList(data);
            })
            .catch(error => console.log('Activity refresh failed:', error));
    }
}

function updateActivityList(activities) {
    const activityContainer = document.getElementById('activity-list');
    if (!activityContainer) return;

    // Update activity list if there are new items
    if (activities.length > 0) {
        activityContainer.innerHTML = activities.map(activity => `
            <div class="activity-item flex items-start p-3 rounded-lg">
                <div class="activity-icon activity-icon-${activity.type}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        ${getActivityIcon(activity.type)}
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">${activity.description}</p>
                    <p class="text-xs text-gray-500 mt-1">${activity.time}</p>
                </div>
            </div>
        `).join('');
    }
}

function getActivityIcon(type) {
    const icons = {
        create: '<path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>',
        update: '<path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>',
        delete: '<path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>',
        assign: '<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>'
    };
    return icons[type] || icons.create;
}
