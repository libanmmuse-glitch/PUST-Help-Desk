<script>
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
        colors: {
          pust: {
            dark: 'var(--pust-dark)',
            slate: 'var(--pust-slate)',
            primary: 'var(--pust-primary)',
            'primary-light': 'var(--pust-primary-light)',
            'primary-ink': 'var(--pust-primary-ink)',
            navy: 'var(--pust-navy)',
            'navy-light': 'var(--pust-navy-light)',
            amber: 'var(--pust-amber)',
            emerald: 'var(--pust-emerald)',
            rose: 'var(--pust-rose)',
            cyan: 'var(--pust-cyan)',
            green: 'var(--pust-green)',
            white: 'var(--pust-white)',
            bg: 'var(--pust-bg)',
            card: 'var(--pust-card)',
            sidebar: 'var(--pust-sidebar)',
          }
      },
      backgroundImage: {
        'gradient-pust-blue': 'linear-gradient(135deg, <?= PUST_COLOR_BLUE ?> 0%, <?= PUST_COLOR_PRIMARY_INK ?> 100%)',
        'gradient-pust-dark': 'linear-gradient(135deg, <?= PUST_COLOR_DARK ?> 0%, <?= PUST_COLOR_SLATE ?> 100%)',
        'gradient-pust-accent': 'linear-gradient(135deg, <?= PUST_COLOR_AMBER ?> 0%, <?= PUST_COLOR_AMBER_LIGHT ?> 100%)',
        'gradient-pust-hero': 'linear-gradient(135deg, <?= PUST_COLOR_DARK ?> 0%, <?= PUST_COLOR_BLUE ?> 46%, <?= PUST_COLOR_AMBER ?> 100%)',
        'gradient-pust-sidebar': 'linear-gradient(180deg, <?= PUST_COLOR_SIDEBAR ?> 0%, <?= PUST_COLOR_DARK ?> 100%)',
      },
      boxShadow: {
        'pust-soft': '0 1px 3px rgba(15, 23, 42, 0.08)',
        'pust-card': '0 4px 6px -1px rgba(15, 23, 42, 0.08), 0 2px 4px -2px rgba(15, 23, 42, 0.06)',
        'pust-hover': '0 10px 25px -5px rgba(47, 111, 219, 0.15)',
      }
    }
  }
};
</script>
