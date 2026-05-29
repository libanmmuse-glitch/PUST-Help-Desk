<script>
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        pust: {
          dark: '<?= PUST_COLOR_DARK ?>',
          slate: '<?= PUST_COLOR_SLATE ?>',
          primary: '<?= PUST_COLOR_BLUE ?>',
          'primary-light': '<?= PUST_COLOR_BLUE_LIGHT ?>',
          navy: '<?= PUST_COLOR_DARK ?>',
          'navy-light': '<?= PUST_COLOR_SLATE ?>',
          amber: '<?= PUST_COLOR_AMBER ?>',
          emerald: '<?= PUST_COLOR_EMERALD ?>',
          rose: '<?= PUST_COLOR_ROSE ?>',
          cyan: '<?= PUST_COLOR_CYAN ?>',
          green: '<?= PUST_COLOR_EMERALD ?>',
          white: '<?= PUST_COLOR_WHITE ?>',
          bg: '<?= PUST_COLOR_BG ?>',
          card: '<?= PUST_COLOR_CARD ?>',
          sidebar: '<?= PUST_COLOR_SIDEBAR ?>',
        }
      },
      backgroundImage: {
        'gradient-pust-blue': 'linear-gradient(135deg, <?= PUST_COLOR_BLUE ?> 0%, <?= PUST_COLOR_BLUE_LIGHT ?> 100%)',
        'gradient-pust-dark': 'linear-gradient(135deg, <?= PUST_COLOR_DARK ?> 0%, <?= PUST_COLOR_SLATE ?> 100%)',
        'gradient-pust-accent': 'linear-gradient(135deg, <?= PUST_COLOR_AMBER ?> 0%, <?= PUST_COLOR_AMBER_LIGHT ?> 100%)',
        'gradient-pust-hero': 'linear-gradient(135deg, <?= PUST_COLOR_DARK ?> 0%, <?= PUST_COLOR_SLATE ?> 45%, <?= PUST_COLOR_BLUE ?> 100%)',
        'gradient-pust-sidebar': 'linear-gradient(180deg, <?= PUST_COLOR_SIDEBAR ?> 0%, <?= PUST_COLOR_DARK ?> 100%)',
      },
      boxShadow: {
        'pust-soft': '0 1px 3px rgba(15, 23, 42, 0.08)',
        'pust-card': '0 4px 6px -1px rgba(15, 23, 42, 0.08), 0 2px 4px -2px rgba(15, 23, 42, 0.06)',
        'pust-hover': '0 10px 25px -5px rgba(37, 99, 235, 0.15)',
      }
    }
  }
};
</script>
