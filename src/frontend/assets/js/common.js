const API_BASE_PATIENT = 'http://localhost/DO_AN_1/code_doan1/src/backend/api/patient';
const API_BASE_AUTH = 'http://localhost/DO_AN_1/code_doan1/src/backend/api/auth';

// Load Header và Footer vào trang
async function loadComponents() {
    try {
        // Load Header
        const headerResponse = await fetch('/frontend/components/header.html');
        const headerHTML = await headerResponse.text();
        document.getElementById('header-placeholder').innerHTML = headerHTML;

        // Load Footer
        const footerResponse = await fetch('/frontend/components/footer.html');
        const footerHTML = await footerResponse.text();
        document.getElementById('footer-placeholder').innerHTML = footerHTML;

        // Init sau khi load xong
        initAfterLoad();
    } catch (error) {
        console.error('Error loading components:', error);
        hidePageLoader();
    }
}

// Initialize sau khi load header/footer
function initAfterLoad() {
    checkLoginStatus();
    setupMobileMenu();
    setupScrollEffects();
    setActiveNavLink();
    
    // Event listeners
    document.getElementById('btnDesktopLogout')?.addEventListener('click', logout);
    document.getElementById('btnMobileLogout')?.addEventListener('click', logout);
}

// Check Login Status
async function checkLoginStatus() {
    try {
        const response = await fetch(`${API_BASE_AUTH}/get-current-user.php`, {
            method: 'GET',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' }
        });

        const data = await response.json();

        // Desktop elements
        const authButtons = document.getElementById('authButtons');
        const logoutHeader = document.getElementById('logoutHeader');
        const navLoggedInItems = document.querySelectorAll('.nav-item-loggedin');
        const userAvatar = document.getElementById('userAvatar');
        const headerUserName = document.getElementById('headerUserName');
        const notificationIconWrapper = document.getElementById('notificationIconWrapper');

        // Mobile elements
        const mobileAuthButtons = document.getElementById('mobileAuthButtons');
        const mobileUserInfo = document.getElementById('mobileUserInfo');
        const mobileMenuLoggedIn = document.querySelectorAll('.mobile-menu-loggedin');
        const mobileUserAvatar = document.getElementById('mobileUserAvatar');
        const mobileUserName = document.getElementById('mobileUserName');

        if (data.success) {
            const fullName = data.data.fullName;
            const firstLetter = fullName.charAt(0).toUpperCase();

            // Desktop
            if (authButtons) authButtons.style.display = 'none';
            if (logoutHeader) logoutHeader.classList.add('active');
            navLoggedInItems.forEach(item => item.style.display = 'block');
            
            if (userAvatar) userAvatar.textContent = firstLetter;
            if (headerUserName) headerUserName.textContent = fullName;

            // Show notification icon (chỉ cho bệnh nhân)
            if (data.data.role === 'benhnhan') {
                if (notificationIconWrapper) notificationIconWrapper.style.display = 'block';
                initNotifications();
            }

            // Mobile
            if (mobileAuthButtons) mobileAuthButtons.style.display = 'none';
            if (mobileUserInfo) mobileUserInfo.classList.add('active');
            mobileMenuLoggedIn.forEach(item => item.style.display = 'block');
            
            if (mobileUserAvatar) mobileUserAvatar.textContent = firstLetter;
            if (mobileUserName) mobileUserName.textContent = fullName;

            updateProfileLink(data.data.role);

        } else {
            // Not logged in
            if (authButtons) authButtons.style.display = 'flex';
            if (logoutHeader) logoutHeader.classList.remove('active');
            navLoggedInItems.forEach(item => item.style.display = 'none');
            if (notificationIconWrapper) notificationIconWrapper.style.display = 'none';

            if (mobileAuthButtons) mobileAuthButtons.style.display = 'flex';
            if (mobileUserInfo) mobileUserInfo.classList.remove('active');
            mobileMenuLoggedIn.forEach(item => item.style.display = 'none');
        }

    } catch (error) {
        console.error('Error checking login:', error);
    } finally {
        setTimeout(() => {
            const loader = document.getElementById('pageLoader');
            if (loader) {
                loader.style.display = 'none'; // Ẩn loader
            }
            document.body.classList.add('loaded'); // Hiện nội dung trang (opacity 1)
        }, 10);
        hidePageLoader();        
    }
}

function hidePageLoader() {
    setTimeout(() => {
        const loader = document.getElementById('pageLoader');
        if (loader) {
            loader.style.opacity = '0';
            loader.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }
    }, 300); // Delay 300ms cho mượt
}

function updateProfileLink(role) {
    const profileLinks = document.querySelectorAll('a[href="taikhoan.html"]');
    if (role === 'bacsi') {
        profileLinks.forEach(link => link.href = '../doctor/dashboard.html');
    } else if (role === 'quantri') {
        profileLinks.forEach(link => link.href = '../admin/dashboard.html');
    }
}

// Logout
async function logout() {
    try {
        const response = await fetch(`${API_BASE_AUTH}/logout.php`, {
            method: 'POST',
            credentials: 'include'
        });

        const data = await response.json();
        if (data.success) {
            localStorage.clear();
            window.location.reload();
        }
    } catch (error) {
        console.error('Logout error:', error);
        alert('Có lỗi xảy ra khi đăng xuất');
    }
}

// ========================================
// NOTIFICATION SYSTEM
// ========================================

let allNotifications = [];

function initNotifications() {
    const notificationBell = document.getElementById('notificationBell');
    const notificationPanel = document.getElementById('notificationPanel');
    const notificationOverlay = document.getElementById('notificationOverlay');
    const closeNotificationPanel = document.getElementById('closeNotificationPanel');
    const btnMarkAllRead = document.getElementById('btnMarkAllRead');

    // Open panel
    notificationBell?.addEventListener('click', (e) => {
        e.stopPropagation();
        openNotificationPanel();
    });

    // Close panel
    closeNotificationPanel?.addEventListener('click', closeNotificationPanelFn);
    notificationOverlay?.addEventListener('click', closeNotificationPanelFn);

    // Mark all read
    btnMarkAllRead?.addEventListener('click', markAllNotificationsRead);

    // Load notifications
    loadNotifications();
    loadUnreadCount();

    // Auto refresh count every 30s
    setInterval(loadUnreadCount, 30000);
}

function openNotificationPanel() {
    const panel = document.getElementById('notificationPanel');
    const overlay = document.getElementById('notificationOverlay');
    
    panel?.classList.add('active');
    overlay?.classList.add('active');
    document.body.style.overflow = 'hidden';

    loadNotifications(); // Refresh when open
}

function closeNotificationPanelFn() {
    const panel = document.getElementById('notificationPanel');
    const overlay = document.getElementById('notificationOverlay');
    
    panel?.classList.remove('active');
    overlay?.classList.remove('active');
    document.body.style.overflow = '';
}

async function loadNotifications() {
    const loading = document.getElementById('notificationLoading');
    const empty = document.getElementById('notificationEmpty');
    const list = document.getElementById('notificationList');

    loading.style.display = 'flex';
    empty.style.display = 'none';
    list.innerHTML = '';

    try {
        const response = await fetch(`${API_BASE_PATIENT}/get-patient-notifications.php`, {
            credentials: 'include'
        });
        const data = await response.json();

        if (data.success && data.data.length > 0) {
            allNotifications = data.data;
            renderNotifications(data.data);
            loading.style.display = 'none';
        } else {
            loading.style.display = 'none';
            empty.style.display = 'flex';
        }
    } catch (error) {
        console.error('Error loading notifications:', error);
        loading.style.display = 'none';
        empty.style.display = 'flex';
    }
}

function renderNotifications(notifications) {
    const list = document.getElementById('notificationList');
    
    list.innerHTML = notifications.map(notif => {
        const isUnread = !notif.daXem;
        const timeAgo = getTimeAgo(notif.thoiGian);
        const iconClass = notif.loai === 'Hệ thống' ? 'type-hethong' : 'type-lichkham';
        const icon = notif.loai === 'Hệ thống' ? 'fa-info-circle' : 'fa-calendar-check';

        return `
            <div class="notification-item ${isUnread ? 'unread' : ''}" 
                 data-id="${notif.maThongBao}" 
                 onclick="handleNotificationClick(${notif.maThongBao})">
                <div class="notification-header">
                    <div class="notification-icon ${iconClass}">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div class="notification-title">${notif.tieuDe}</div>
                    <div class="notification-time">${timeAgo}</div>
                </div>
                <div class="notification-content collapsed" id="content-${notif.maThongBao}">
                    ${notif.noiDung}
                </div>
            </div>
        `;
    }).join('');
}

async function handleNotificationClick(maThongBao) {
    const item = document.querySelector(`.notification-item[data-id="${maThongBao}"]`);
    const content = document.getElementById(`content-${maThongBao}`);

    // Expand/collapse content
    if (content.classList.contains('collapsed')) {
        content.classList.remove('collapsed');
        
        // Mark as read if unread
        if (item.classList.contains('unread')) {
            await markNotificationRead(maThongBao);
            item.classList.remove('unread');
            loadUnreadCount(); // Refresh count
        }
    } else {
        content.classList.add('collapsed');
    }
}

async function markNotificationRead(maThongBao) {
    try {
        await fetch(`${API_BASE_PATIENT}/mark-notification-read.php`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ maThongBao })
        });
    } catch (error) {
        console.error('Error marking notification read:', error);
    }
}

async function markAllNotificationsRead() {
    try {
        const response = await fetch(`${API_BASE_PATIENT}/mark-all-notifications-read.php`, {
            method: 'POST',
            credentials: 'include'
        });
        const data = await response.json();

        if (data.success) {
            // Update UI
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            loadUnreadCount();
        }
    } catch (error) {
        console.error('Error marking all read:', error);
    }
}

async function loadUnreadCount() {
    try {
        const response = await fetch(`${API_BASE_PATIENT}/get-unread-notifications-count.php`, {
            credentials: 'include'
        });
        const data = await response.json();

        const badge = document.getElementById('notificationBadge');
        if (data.success && data.count > 0) {
            badge.textContent = data.count;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading unread count:', error);
    }
}

function getTimeAgo(dateString) {
    const now = new Date();
    const past = new Date(dateString);
    const diffMs = now - past;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Vừa xong';
    if (diffMins < 60) return `${diffMins} phút trước`;
    if (diffHours < 24) return `${diffHours} giờ trước`;
    if (diffDays === 1) return 'Hôm qua';
    if (diffDays < 7) return `${diffDays} ngày trước`;
    
    return past.toLocaleDateString('vi-VN');
}

// ========================================
// MOBILE MENU
// ========================================

function setupMobileMenu() {
    const menuToggle = document.getElementById('menuToggle');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const closeSidebar = document.getElementById('closeSidebar');

    menuToggle?.addEventListener('click', () => {
        mobileSidebar.classList.add('active');
        mobileOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    function closeMobileSidebar() {
        mobileSidebar.classList.remove('active');
        mobileOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    closeSidebar?.addEventListener('click', closeMobileSidebar);
    mobileOverlay?.addEventListener('click', closeMobileSidebar);

    document.querySelectorAll('.mobile-menu a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 992) closeMobileSidebar();
        });
    });
}

// ========================================
// SCROLL EFFECTS
// ========================================

function setupScrollEffects() {
    const scrollBtn = document.getElementById('scrollTopBtn');
    
    window.addEventListener('scroll', () => {
        if (scrollBtn) {
            scrollBtn.style.display = window.scrollY > 300 ? 'flex' : 'none';
        }
    });
}

// ========================================
// SET ACTIVE NAV LINK
// ========================================

function setActiveNavLink() {
    const currentPage = window.location.pathname.split('/').pop();
    
    document.querySelectorAll('.nav-link-header, .mobile-menu a').forEach(link => {
        const linkPage = link.getAttribute('data-page');
        if (linkPage && currentPage.includes(linkPage)) {
            link.classList.add('active');
        }
    });
}

// ========================================
// INIT ON PAGE LOAD
// ========================================

document.addEventListener('DOMContentLoaded', loadComponents);