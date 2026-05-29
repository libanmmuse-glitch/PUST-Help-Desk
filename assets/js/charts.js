/**
 * Chart.js dashboard charts — premium theme palette
 */

function initDashboardCharts(data) {
  if (typeof Chart === 'undefined') return;

  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  const textColor = isDark ? '#CBD5E1' : '#64748B';
  const gridColor = isDark ? 'rgba(148, 163, 184, 0.15)' : 'rgba(226, 232, 240, 0.8)';

  const brand = window.PUST_BRAND || {};
  const colors = [
    brand.blue || '#2563EB',
    brand.blueLight || '#3B82F6',
    brand.amber || '#F59E0B',
    brand.emerald || '#10B981',
    brand.cyan || '#06B6D4',
    brand.muted || '#94A3B8',
  ];

  const statusColors = {
    pending: brand.muted || '#64748B',
    open: brand.blue || '#2563EB',
    in_progress: brand.amber || '#F59E0B',
    resolved: brand.emerald || '#10B981',
    closed: brand.muted || '#94A3B8',
  };

  const defaultOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom',
        labels: { color: textColor, padding: 16, usePointStyle: true },
      },
    },
    scales: {
      x: { ticks: { color: textColor }, grid: { color: gridColor } },
      y: { ticks: { color: textColor }, grid: { color: gridColor } },
    },
  };

  const deptCtx = document.getElementById('chart-departments');
  if (deptCtx && data.by_department) {
    new Chart(deptCtx, {
      type: 'doughnut',
      data: {
        labels: data.by_department.map(d => d.name),
        datasets: [{
          data: data.by_department.map(d => d.count),
          backgroundColor: colors,
          borderWidth: 2,
          borderColor: isDark ? '#1E293B' : '#FFFFFF',
        }],
      },
      options: defaultOptions,
    });
  }

  const statusCtx = document.getElementById('chart-status');
  if (statusCtx && data.by_status) {
    new Chart(statusCtx, {
      type: 'bar',
      data: {
        labels: data.by_status.map(s => s.label || s.status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())),
        datasets: [{
          label: 'Tickets',
          data: data.by_status.map(s => s.count),
          backgroundColor: data.by_status.map(s => statusColors[s.status] || brand.blue),
          borderRadius: 6,
        }],
      },
      options: { ...defaultOptions, plugins: { legend: { display: false } } },
    });
  }

  const monthlyCtx = document.getElementById('chart-monthly');
  if (monthlyCtx && data.monthly) {
    new Chart(monthlyCtx, {
      type: 'line',
      data: {
        labels: data.monthly.map(m => m.month),
        datasets: [{
          label: 'Tickets Created',
          data: data.monthly.map(m => m.count),
          borderColor: brand.blue || '#2563EB',
          backgroundColor: 'rgba(37, 99, 235, 0.1)',
          fill: true,
          tension: 0.35,
          pointBackgroundColor: brand.blueLight || '#3B82F6',
          pointBorderColor: '#FFFFFF',
          pointBorderWidth: 2,
        }],
      },
      options: defaultOptions,
    });
  }
}
