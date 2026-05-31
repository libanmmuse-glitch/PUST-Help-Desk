<script>
(function () {
  var theme = null;
  try {
    theme = localStorage.getItem('pust-theme');
  } catch (e) {}

  if (theme !== 'light' && theme !== 'dark') {
    var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    theme = prefersDark ? 'dark' : 'light';
    try {
      localStorage.setItem('pust-theme', theme);
    } catch (e) {}
  }
  document.documentElement.setAttribute('data-theme', theme);
  document.documentElement.classList.toggle('dark', theme === 'dark');
  document.documentElement.style.colorScheme = theme;

  var meta = document.querySelector('meta[name="theme-color"]');
  if (meta) {
    meta.setAttribute('content', theme === 'dark' ? '<?= PUST_COLOR_DARK ?>' : '<?= PUST_COLOR_BLUE ?>');
  }
})();
</script>
