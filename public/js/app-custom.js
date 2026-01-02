/**
 * Golden TMS - Custom Application JavaScript
 * Consolidated JS file for reusable functions
 */

// ============================================
// PHONE NUMBER FORMATTING
// ============================================

/**
 * Initialize phone input formatting for Zimbabwe international format
 * @param {string} inputId - The ID of the phone input element
 */
function initPhoneInput(inputId) {
    const phoneInput = document.getElementById(inputId);
    if (!phoneInput) return;

    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value;

        // Remove all non-numeric characters
        value = value.replace(/\D/g, '');

        // If starts with 0, replace with 263 (Zimbabwe country code)
        if (value.startsWith('0')) {
            value = '263' + value.substring(1);
        }

        // If doesn't start with country code, add 263
        if (!value.startsWith('263') && value.length > 0) {
            value = '263' + value;
        }

        // Limit to 12 digits (263 + 9 digits)
        value = value.substring(0, 12);

        e.target.value = value;
    });

    phoneInput.addEventListener('blur', function(e) {
        let value = e.target.value.replace(/\D/g, '');

        if (value.length > 0) {
            if (value.startsWith('0')) {
                value = '263' + value.substring(1);
            } else if (!value.startsWith('263')) {
                value = '263' + value;
            }

            if (value.length < 12) {
                e.target.setCustomValidity('Phone number must be 12 digits (263 + 9 digits)');
            } else {
                e.target.setCustomValidity('');
            }

            e.target.value = value;
        }
    });

    phoneInput.addEventListener('input', function(e) {
        e.target.setCustomValidity('');
    });
}

// ============================================
// PARTIAL CLOSURE SECTION TOGGLE
// ============================================

/**
 * Initialize partial closure section visibility toggle
 * @param {string} statusSelectId - The ID of the status select element
 * @param {string} sectionId - The ID of the partial closure section
 * @param {string} pendingDeptId - The ID of the pending department select
 */
function initPartialClosureToggle(statusSelectId, sectionId, pendingDeptId) {
    const statusSelect = document.getElementById(statusSelectId);
    const partialClosedSection = document.getElementById(sectionId);
    const pendingDepartmentSelect = document.getElementById(pendingDeptId);

    if (!statusSelect || !partialClosedSection || !pendingDepartmentSelect) return;

    function togglePartialClosedSection() {
        if (statusSelect.value === 'partial_closed') {
            partialClosedSection.style.display = 'block';
            pendingDepartmentSelect.setAttribute('required', 'required');
        } else {
            partialClosedSection.style.display = 'none';
            pendingDepartmentSelect.removeAttribute('required');
        }
    }

    statusSelect.addEventListener('change', togglePartialClosedSection);
    togglePartialClosedSection(); // Initial check
}

// ============================================
// PASSWORD VISIBILITY TOGGLE
// ============================================

/**
 * Toggle password input visibility
 * @param {string} inputId - The ID of the password input element
 */
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    input.type = input.type === 'password' ? 'text' : 'password';
}

// ============================================
// NOTIFICATION FUNCTIONS
// ============================================

/**
 * Mark a notification as read and redirect
 * @param {string} notificationId - The notification ID
 * @param {string} url - The URL to redirect to
 */
function markAsRead(notificationId, url) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    }).then(() => {
        if (url && url !== '#') {
            window.location.href = url;
        } else {
            location.reload();
        }
    });
}

// ============================================
// TWILIO TEST CONNECTION
// ============================================

/**
 * Test Twilio WhatsApp connection
 */
function testConnection() {
    const phone = document.getElementById('test_phone').value;
    const btn = document.getElementById('testBtn');
    const resultDiv = document.getElementById('testResult');
    const resultContent = resultDiv.querySelector('div');

    if (!phone) {
        resultDiv.classList.remove('hidden');
        resultContent.className = 'p-3 rounded-lg text-sm bg-yellow-50 text-yellow-800 border border-yellow-200';
        resultContent.textContent = 'Please enter a phone number to test.';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sending...';

    fetch('/settings/twilio/test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ test_phone: phone })
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.classList.remove('hidden');
        if (data.success) {
            resultContent.className = 'p-3 rounded-lg text-sm bg-green-50 text-green-800 border border-green-200';
            resultContent.innerHTML = '<strong>✓ Success!</strong> ' + data.message;
        } else {
            resultContent.className = 'p-3 rounded-lg text-sm bg-red-50 text-red-800 border border-red-200';
            resultContent.innerHTML = '<strong>✗ Error:</strong> ' + data.message;
        }
    })
    .catch(error => {
        resultDiv.classList.remove('hidden');
        resultContent.className = 'p-3 rounded-lg text-sm bg-red-50 text-red-800 border border-red-200';
        resultContent.textContent = 'Network error. Please try again.';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Send Test';
    });
}

// ============================================
// REPORT WIZARD
// ============================================

/**
 * Alpine.js component for report wizard
 * @returns {Object} Alpine component data
 */
function reportWizard() {
    return {
        step: 1,
        reportType: 'tickets',
        format: 'view',
        filters: {
            date_from: '',
            date_to: '',
            branch_id: '',
            assigned_to: '',
            status: '',
            priority: '',
            department: '',
            employer_id: '',
            payment_method_id: ''
        },
        nextStep() {
            if (this.step < 3) {
                this.step++;
            }
        },
        previousStep() {
            if (this.step > 1) {
                this.step--;
            }
        }
    }
}

// ============================================
// AUTO-INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Auto-init phone input if exists
    if (document.getElementById('phone_number')) {
        initPhoneInput('phone_number');
    }

    // Auto-init partial closure toggle if elements exist
    if (document.getElementById('status') && document.getElementById('partial-closed-section')) {
        initPartialClosureToggle('status', 'partial-closed-section', 'pending_department');
    }
});
