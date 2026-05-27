/**
 * public/js/main.js
 * Core JS for Univ E-Learning UI interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Enable Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Form Validation logic
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Dynamic search filter for course catalog
    const searchInput = document.getElementById('courseSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();
            const courseCards = document.querySelectorAll('.course-card-container');
            let visibleCount = 0;
            
            courseCards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const desc = card.querySelector('.card-text').textContent.toLowerCase();
                if (title.includes(term) || desc.includes(term)) {
                    card.style.display = 'block';
                    // add animation class for smooth re-entry
                    card.classList.add('animate__animated', 'animate__fadeIn');
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                    card.classList.remove('animate__animated', 'animate__fadeIn');
                }
            });

            // Handle empty state
            const emptyState = document.getElementById('emptySearchState');
            if (emptyState) {
                if (visibleCount === 0 && courseCards.length > 0) {
                    emptyState.style.display = 'block';
                } else {
                    emptyState.style.display = 'none';
                }
            }
        });
    }

    // Quiz Option selection highlighting
    const quizOptions = document.querySelectorAll('.quiz-option');
    quizOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Find parent question group
            const parent = this.closest('.question-group');
            // Remove selected class from all siblings
            const siblings = parent.querySelectorAll('.quiz-option');
            siblings.forEach(sib => sib.classList.remove('selected'));
            
            // Add to current
            this.classList.add('selected');
            
            // check the radio input
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });
    });

    // Sidebar toggler
    const toggleBtn = document.getElementById('menu-toggle');
    const closeBtn = document.getElementById('sidebar-close');
    const wrapper = document.getElementById('wrapper');
    const sidebar = document.getElementById('sidebar-wrapper');
    const sidebarLinks = document.querySelectorAll('#sidebar-wrapper a');

    const isMobileSidebar = () => window.matchMedia('(max-width: 991.98px)').matches;

    const syncToggleButton = () => {
        if (!toggleBtn || !wrapper) return;

        const isToggled = wrapper.classList.contains('toggled');
        const isExpanded = isMobileSidebar() ? isToggled : !isToggled;
        toggleBtn.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
        toggleBtn.setAttribute('aria-label', isExpanded ? 'Collapse navigation' : 'Expand navigation');
    };

    const setSidebarToggled = (isToggled) => {
        if (!wrapper) return;
        wrapper.classList.toggle('toggled', isToggled);
        syncToggleButton();
    };

    if (toggleBtn && wrapper) {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            setSidebarToggled(!wrapper.classList.contains('toggled'));
        });
        syncToggleButton();
    }

    if (closeBtn && wrapper) {
        closeBtn.addEventListener('click', () => {
            setSidebarToggled(false);
        });
    }

    sidebarLinks.forEach((link) => {
        link.addEventListener('click', () => {
            if (isMobileSidebar()) {
                setSidebarToggled(false);
            }
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            setSidebarToggled(false);
        }
    });

    document.addEventListener('click', (e) => {
        if (!wrapper || !sidebar || !isMobileSidebar() || !wrapper.classList.contains('toggled')) {
            return;
        }

        if (sidebar.contains(e.target) || (toggleBtn && toggleBtn.contains(e.target))) {
            return;
        }

        setSidebarToggled(false);
    });

    window.addEventListener('resize', () => {
        if (!isMobileSidebar()) {
            setSidebarToggled(false);
        } else {
            syncToggleButton();
        }
    });

    window.addEventListener('orientationchange', () => {
        setSidebarToggled(false);
    });
});

// Helper for displaying SweetAlert notifications
window.showNotification = function(title, text, icon = 'success') {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'colored-toast'
            }
        });
    } else {
        alert(title + ": " + text);
    }
};

// Confirmation modal helper
window.confirmAction = function(e, title = "Are you sure?", text = "You won't be able to revert this!") {
    e.preventDefault();
    const target = e.currentTarget;
    const href = target.getAttribute('href');
    const form = target.closest('form');
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                if (form) {
                    form.submit();
                } else if (href) {
                    window.location.href = href;
                }
            }
        });
    } else {
        if (confirm(title + '\n' + text)) {
            if (form) {
                form.submit();
            } else if (href) {
                window.location.href = href;
            }
        }
    }
};
