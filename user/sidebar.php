<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php">
                    <i class="fas fa-user"></i>
                    Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="create-post.php">
                    <i class="fas fa-edit"></i>
                    Create Post
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="post-list.php">
                    <i class="fas fa-list"></i>
                    My Posts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="chat.php">
                    <i class="fas fa-comments"></i>
                    Messages
                </a>
            </li>
        </ul>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Account</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="edit-profile.php">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
    /* Sidebar Mobile & Tablet Responsive Styles */
.sidebar {
    transition: all 0.3s ease;
}

/* Mobile First Approach */
@media (max-width: 767.98px) {
    .sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        width: 280px;
        height: 100vh;
        z-index: 1040;
        background: white;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        transition: left 0.3s ease;
    }

    .sidebar.show {
        left: 0;
    }

    .sidebar-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1039;
        display: none;
    }

    .sidebar-backdrop.show {
        display: block;
    }

    .sidebar .nav-link {
        padding: 12px 20px !important;
        border-radius: 0;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .sidebar .nav-link.active {
        border-left-color: #4f46e5;
        background-color: rgba(79, 70, 229, 0.1);
    }

    .sidebar .nav-link:hover {
        background-color: #f8f9fa;
        border-left-color: #6b7280;
    }

    .sidebar-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #6c757d;
        cursor: pointer;
        z-index: 1041;
    }

    .sidebar-close:hover {
        color: #495057;
    }
}

/* Tablet Styles */
@media (min-width: 768px) and (max-width: 991.98px) {
    .sidebar {
        width: 220px;
    }

    .sidebar .nav-link {
        padding: 10px 15px !important;
        font-size: 0.9rem;
    }

    .sidebar .nav-link i {
        width: 20px;
        margin-right: 8px;
    }

    .sidebar-heading {
        font-size: 0.8rem;
    }
}

/* Ensure main content adjusts when sidebar is open on mobile */
@media (max-width: 767.98px) {
    .container-fluid {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }

    main.col-md-9 {
        margin-left: 0 !important;
        width: 100% !important;
    }
}

/* Smooth transitions for all states */
.sidebar .nav-item {
    margin-bottom: 2px;
}

.sidebar .nav-link {
    display: flex;
    align-items: center;
    color: #495057;
    text-decoration: none;
    transition: all 0.2s ease;
}

.sidebar .nav-link i {
    width: 24px;
    margin-right: 10px;
    text-align: center;
}

.sidebar .nav-link:hover {
    color: #4f46e5;
    background-color: rgba(79, 70, 229, 0.05);
}

.sidebar .nav-link.active {
    color: #4f46e5;
    background-color: rgba(79, 70, 229, 0.1);
    font-weight: 500;
}

/* Scrollbar styling for mobile */
.sidebar::-webkit-scrollbar {
    width: 4px;
}

.sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>

// Sidebar Mobile Responsive Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Create backdrop element
    const backdrop = document.createElement('div');
    backdrop.className = 'sidebar-backdrop';
    document.body.appendChild(backdrop);

    // Get sidebar element
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    // Create close button for mobile
    const closeButton = document.createElement('button');
    closeButton.className = 'sidebar-close';
    closeButton.innerHTML = '&times;';
    sidebar.appendChild(closeButton);

    // Function to open sidebar
    function openSidebar() {
        sidebar.classList.add('show');
        backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    // Function to close sidebar
    function closeSidebar() {
        sidebar.classList.remove('show');
        backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    // Mobile menu toggle (you'll need to add this button to your navbar)
    const mobileMenuToggle = document.createElement('button');
    mobileMenuToggle.className = 'btn btn-primary d-md-none mobile-menu-toggle';
    mobileMenuToggle.innerHTML = '<i class="fas fa-bars"></i>';
    mobileMenuToggle.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1030;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;

    // Add toggle button to page
    document.body.appendChild(mobileMenuToggle);

    // Event listeners
    mobileMenuToggle.addEventListener('click', openSidebar);
    closeButton.addEventListener('click', closeSidebar);
    backdrop.addEventListener('click', closeSidebar);

    // Close sidebar when clicking on nav links (mobile)
    sidebar.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                closeSidebar();
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            closeSidebar();
        }
    });

    // Add active state based on current page
    function setActiveNavItem() {
        const currentPage = window.location.pathname.split('/').pop();
        sidebar.querySelectorAll('.nav-link').forEach(link => {
            const linkPage = link.getAttribute('href');
            if (linkPage === currentPage || 
                (currentPage === '' && linkPage === 'index.php') ||
                (linkPage.includes(currentPage) && currentPage !== '')) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }

    setActiveNavItem();

    // Keyboard accessibility
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            closeSidebar();
        }
    });
});

// Add this to your existing navbar.php to show current page in sidebar
// You'll need to update your navbar to include a mobile menu button
</script>
