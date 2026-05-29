<script>
(function () {
  var theme = localStorage.getItem('pust-theme');
  if (theme !== 'light' && theme !== 'dark') {
    theme = '<?= PUST_DEFAULT_THEME ?>';
    localStorage.setItem('pust-theme', theme);
  }
  document.documentElement.setAttribute('data-theme', theme);
  document.documentElement.classList.toggle('dark', theme === 'dark');
})();
</script>
