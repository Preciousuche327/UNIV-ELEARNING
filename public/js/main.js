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
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const sidebarLinks = document.querySelectorAll('#sidebar-wrapper a');

    const setSidebarOpen = (isOpen) => {
        if (!wrapper) return;
        wrapper.classList.toggle('toggled', isOpen);
        if (toggleBtn) {
            toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }
    };

    const isMobileSidebar = () => window.matchMedia('(max-width: 767.98px)').matches;

    if (toggleBtn && wrapper) {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            setSidebarOpen(!wrapper.classList.contains('toggled'));
        });
    }

    if (sidebarOverlay && wrapper) {
        sidebarOverlay.addEventListener('click', () => {
            setSidebarOpen(false);
        });
    }

    if (closeBtn && wrapper) {
        closeBtn.addEventListener('click', () => {
            setSidebarOpen(false);
        });
    }

    sidebarLinks.forEach((link) => {
        link.addEventListener('click', () => {
            if (isMobileSidebar()) {
                setSidebarOpen(false);
            }
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            setSidebarOpen(false);
        }
    });

    window.addEventListener('resize', () => {
        if (!isMobileSidebar()) {
            setSidebarOpen(false);
        }
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
