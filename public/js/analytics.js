// Analytics page functionality

document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars
    animateProgressBars();

    // Animate metric counters
    animateMetrics();
});

function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-fill');

    progressBars.forEach(function(bar) {
        const targetWidth = bar.getAttribute('data-width');
        setTimeout(function() {
            bar.style.width = targetWidth + '%';
        }, 100);
    });
}

function animateMetrics() {
    const metrics = document.querySelectorAll('.metric-animate');

    metrics.forEach(function(metric) {
        const finalValue = parseInt(metric.textContent);
        if (isNaN(finalValue)) return;

        let currentValue = 0;
        const increment = Math.ceil(finalValue / 30);
        const duration = 1000;
        const stepTime = duration / (finalValue / increment);

        const timer = setInterval(function() {
            currentValue += increment;
            if (currentValue >= finalValue) {
                currentValue = finalValue;
                clearInterval(timer);
            }
            metric.textContent = currentValue;
        }, stepTime);
    });
}

// Format numbers with commas
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Calculate percentage
function calculatePercentage(value, total) {
    if (total === 0) return 0;
    return Math.round((value / total) * 100);
}

// Export data functionality
function exportAnalytics() {
    const data = {
        totalTickets: document.querySelector('[data-metric="total"]').textContent,
        pendingTickets: document.querySelector('[data-metric="pending"]').textContent,
        inProgressTickets: document.querySelector('[data-metric="inprogress"]').textContent,
        resolvedTickets: document.querySelector('[data-metric="resolved"]').textContent,
        timestamp: new Date().toISOString()
    };

    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'analytics-' + new Date().toISOString().split('T')[0] + '.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
