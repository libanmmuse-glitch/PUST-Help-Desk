/**
 * PUST Help Desk - Main JavaScript
 */

const PUST = {
  toastContainer: null,

  init() {
    this.initToasts();
    this.initSidebar();
    this.initMobileMenu();
    this.initModals();
    this.initTheme();
    this.initPageLoader();
    this.initForms();
    this.initDeleteConfirm();
  },

  initPageLoader() {
    const loader = document.getElementById('page-loader');
    if (loader) {
      window.addEventListener('load', () => {
        setTimeout(() => loader.classList.add('hidden'), 300);
      });
    }
  },

  initToasts() {
    this.toastContainer = document.querySelector('.toast-container');
    if (!this.toastContainer) {
      this.toastContainer = document.createElement('div');
      this.toastContainer.className = 'toast-container';
      document.body.appendChild(this.toastContainer);
    }
    document.querySelectorAll('[data-toast]').forEach(el => {
      this.toast(el.dataset.toast, el.dataset.toastType || 'info');
    });
  },

  toast(message, type = 'info', duration = 4000) {
    const typeClass = {
      success: 'toast--success',
      error: 'toast--error',
      warning: 'toast--warning',
      info: 'toast--info',
    };
    const toast = document.createElement('div');
    toast.className = `toast px-4 py-3 rounded-xl shadow-lg text-sm font-medium ${typeClass[type] || typeClass.info}`;
    toast.textContent = message;
    this.toastContainer.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity 0.3s';
      setTimeout(() => toast.remove(), 300);
    }, duration);
  },

  initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebar-toggle');
    const collapseBtn = document.getElementById('sidebar-collapse');
    const collapseHeader = document.getElementById('sidebar-collapse-header');
    const overlay = document.getElementById('sidebar-overlay');

    const toggleCollapse = () => {
      const collapsed = document.documentElement.classList.toggle('sidebar-collapsed');
      localStorage.setItem('pust-sidebar-collapsed', collapsed ? '1' : '0');
    };

    collapseBtn?.addEventListener('click', toggleCollapse);
    collapseHeader?.addEventListener('click', toggleCollapse);

    if (toggle && sidebar) {
      toggle.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay?.classList.toggle('hidden');
      });
    }

    overlay?.addEventListener('click', () => {
      sidebar?.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    });

    // Close mobile drawer when a nav link is clicked
    sidebar?.querySelectorAll('.sidebar-link').forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 1024) {
          sidebar.classList.add('-translate-x-full');
          overlay?.classList.add('hidden');
        }
      });
    });
  },

  isSidebarCollapsed() {
    return document.documentElement.classList.contains('sidebar-collapsed');
  },

  initMobileMenu() {
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    btn?.addEventListener('click', () => menu?.classList.toggle('hidden'));
  },

  initModals() {
    document.querySelectorAll('[data-modal-open]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.modalOpen;
        document.getElementById(id)?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      });
    });
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
      btn.addEventListener('click', () => this.closeModals());
    });
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
      backdrop.addEventListener('click', e => {
        if (e.target === backdrop) this.closeModals();
      });
    });
  },

  closeModals() {
    document.querySelectorAll('[id^="modal-"]').forEach(m => m.classList.add('hidden'));
    document.body.style.overflow = '';
  },

  initTheme() {
    let stored = localStorage.getItem('pust-theme');
    if (stored !== 'light' && stored !== 'dark') {
      stored = 'light';
      localStorage.setItem('pust-theme', 'light');
    }
    this.setTheme(stored, false);

    document.getElementById('theme-toggle')?.addEventListener('click', () => {
      const current = document.documentElement.getAttribute('data-theme') || 'light';
      this.setTheme(current === 'dark' ? 'light' : 'dark');
    });

    document.querySelectorAll('[data-set-theme]').forEach(btn => {
      btn.addEventListener('click', () => {
        this.setTheme(btn.dataset.setTheme);
      });
    });
  },

  setTheme(theme, save = true) {
    const normalized = theme === 'dark' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', normalized);
    document.documentElement.classList.toggle('dark', normalized === 'dark');

    if (save) {
      localStorage.setItem('pust-theme', normalized);
    }

    this.updateThemeUI(normalized);
  },

  updateThemeUI(theme) {
    const moon = document.getElementById('theme-icon-moon');
    const sun = document.getElementById('theme-icon-sun');
    const toggle = document.getElementById('theme-toggle');

    if (moon && sun) {
      moon.style.display = theme === 'dark' ? 'none' : 'block';
      sun.style.display = theme === 'dark' ? 'block' : 'none';
    }
    document.querySelectorAll('.theme-icon-moon').forEach(icon => {
      icon.style.display = theme === 'dark' ? 'none' : 'block';
    });
    document.querySelectorAll('.theme-icon-sun').forEach(icon => {
      icon.style.display = theme === 'dark' ? 'block' : 'none';
    });
    if (toggle) {
      toggle.setAttribute('title', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
      toggle.setAttribute('aria-label', toggle.getAttribute('title'));
    }

    document.querySelectorAll('[data-set-theme]').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.setTheme === theme);
    });
  },

  getTheme() {
    return document.documentElement.getAttribute('data-theme') || 'light';
  },

  initForms() {
    document.querySelectorAll('form[data-validate]').forEach(form => {
      form.addEventListener('submit', e => {
        if (!this.validateForm(form)) {
          e.preventDefault();
        } else {
          const btn = form.querySelector('[type="submit"]');
          if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner inline-block w-4 h-4 mr-2"></span> Processing...';
          }
        }
      });
    });
  },

  validateForm(form) {
    let valid = true;
    form.querySelectorAll('[required]').forEach(field => {
      if (field.disabled || field.closest('.hidden')) {
        return;
      }
      this.clearFieldError(field);
      if (!field.value.trim()) {
        this.setFieldError(field, 'This field is required.');
        valid = false;
      }
    });
    const email = form.querySelector('[type="email"]');
    if (email?.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
      this.setFieldError(email, 'Enter a valid email address.');
      valid = false;
    }
    const password = form.querySelector('[name="password"]');
    const confirm = form.querySelector('[name="password_confirm"]');
    if (password && confirm && password.value !== confirm.value) {
      this.setFieldError(confirm, 'Passwords do not match.');
      valid = false;
    }
    if (password?.dataset.strength === 'true' && password.value.length < 8) {
      this.setFieldError(password, 'Password must be at least 8 characters.');
      valid = false;
    }
    return valid;
  },

  setFieldError(field, message) {
    field.classList.add('border-red-500');
    let err = field.parentElement.querySelector('.field-error');
    if (!err) {
      err = document.createElement('p');
      err.className = 'field-error text-red-500 text-xs mt-1';
      field.parentElement.appendChild(err);
    }
    err.textContent = message;
  },

  clearFieldError(field) {
    field.classList.remove('border-red-500');
    field.parentElement.querySelector('.field-error')?.remove();
  },

  initDeleteConfirm() {
    document.querySelectorAll('[data-confirm]').forEach(el => {
      el.addEventListener('click', e => {
        if (!confirm(el.dataset.confirm || 'Are you sure?')) {
          e.preventDefault();
        }
      });
    });
  },

  async fetchNotifications() {
    const badge = document.getElementById('notif-badge');
    if (!badge) return;
    try {
      const res = await fetch(document.body.dataset.notifUrl || '../api/notifications-count.php');
      const data = await res.json();
      if (data.count > 0) {
        badge.textContent = data.count > 99 ? '99+' : data.count;
        badge.classList.remove('hidden');
      } else {
        badge.classList.add('hidden');
      }
    } catch (_) {}
  },

  filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;
    input.addEventListener('input', () => {
      const q = input.value.toLowerCase();
      table.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  },
};

document.addEventListener('DOMContentLoaded', () => PUST.init());

// Export for charts page
window.PUST = PUST;
