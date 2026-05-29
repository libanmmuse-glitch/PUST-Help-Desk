<?php
/** Premium theme CSS variables — loaded before custom.css */
?>
<style id="pust-brand-defaults">
:root {
  /* Primary */
  --pust-dark: <?= PUST_COLOR_DARK ?>;
  --pust-slate: <?= PUST_COLOR_SLATE ?>;
  --pust-primary: <?= PUST_COLOR_BLUE ?>;
  --pust-primary-light: <?= PUST_COLOR_BLUE_LIGHT ?>;
  --pust-navy: <?= PUST_COLOR_DARK ?>;
  --pust-navy-light: <?= PUST_COLOR_SLATE ?>;
  --pust-blue: <?= PUST_COLOR_BLUE ?>;

  /* Accents */
  --pust-amber: <?= PUST_COLOR_AMBER ?>;
  --pust-amber-light: <?= PUST_COLOR_AMBER_LIGHT ?>;
  --pust-emerald: <?= PUST_COLOR_EMERALD ?>;
  --pust-rose: <?= PUST_COLOR_ROSE ?>;
  --pust-cyan: <?= PUST_COLOR_CYAN ?>;
  --pust-green: <?= PUST_COLOR_EMERALD ?>;

  /* Surfaces */
  --pust-white: <?= PUST_COLOR_WHITE ?>;
  --pust-bg: <?= PUST_COLOR_BG ?>;
  --pust-bg-secondary: <?= PUST_COLOR_BG_SECONDARY ?>;
  --pust-card: <?= PUST_COLOR_CARD ?>;
  --pust-sidebar: <?= PUST_COLOR_SIDEBAR ?>;

  /* Text */
  --pust-text: <?= PUST_COLOR_TEXT ?>;
  --pust-text-secondary: <?= PUST_COLOR_TEXT_SECONDARY ?>;
  --pust-text-light: <?= PUST_COLOR_TEXT_LIGHT ?>;

  /* Borders & shadows */
  --pust-border: <?= PUST_COLOR_BORDER ?>;
  --pust-shadow-soft: <?= PUST_SHADOW_SOFT ?>;
  --pust-shadow-card: <?= PUST_SHADOW_CARD ?>;
  --pust-shadow-hover: <?= PUST_SHADOW_HOVER ?>;

  /* Gradients */
  --gradient-blue: linear-gradient(135deg, <?= PUST_COLOR_BLUE ?> 0%, <?= PUST_COLOR_BLUE_LIGHT ?> 100%);
  --gradient-dark: linear-gradient(135deg, <?= PUST_COLOR_DARK ?> 0%, <?= PUST_COLOR_SLATE ?> 100%);
  --gradient-accent: linear-gradient(135deg, <?= PUST_COLOR_AMBER ?> 0%, <?= PUST_COLOR_AMBER_LIGHT ?> 100%);
  --gradient-hero: linear-gradient(135deg, <?= PUST_COLOR_DARK ?> 0%, <?= PUST_COLOR_SLATE ?> 45%, <?= PUST_COLOR_BLUE ?> 100%);
  --gradient-navbar: linear-gradient(135deg, <?= PUST_COLOR_DARK ?> 0%, <?= PUST_COLOR_SLATE ?> 100%);
  --gradient-sidebar: linear-gradient(180deg, <?= PUST_COLOR_SIDEBAR ?> 0%, <?= PUST_COLOR_DARK ?> 100%);
  --gradient-pust-hero: var(--gradient-hero);
  --gradient-pust-primary: var(--gradient-blue);
  --gradient-pust-accent: var(--gradient-accent);
  --gradient-pust-brand: var(--gradient-blue);
  --gradient-pust-sidebar: var(--gradient-sidebar);
  --gradient-icon: var(--gradient-blue);

  color-scheme: light;
}

html[data-theme="light"],
html:not([data-theme]) {
  --dash-bg: <?= PUST_COLOR_BG ?>;
  --dash-bg-alt: <?= PUST_COLOR_BG_SECONDARY ?>;
  --dash-card: <?= PUST_COLOR_CARD ?>;
  --dash-text: <?= PUST_COLOR_TEXT ?>;
  --dash-muted: <?= PUST_COLOR_TEXT_SECONDARY ?>;
  --dash-border: <?= PUST_COLOR_BORDER ?>;
  --dash-input-bg: <?= PUST_COLOR_CARD ?>;
  --dash-input-border: #CBD5E1;
  --dash-table-head: <?= PUST_COLOR_BG_SECONDARY ?>;
  --dash-table-stripe: <?= PUST_COLOR_BG ?>;
  --dash-table-hover: rgba(37, 99, 235, 0.06);
  --dash-icon-hover: rgba(37, 99, 235, 0.08);
  --dash-shadow: <?= PUST_SHADOW_SOFT ?>;
  --dash-link: <?= PUST_COLOR_BLUE ?>;
  --dash-link-hover: <?= PUST_COLOR_BLUE_LIGHT ?>;
  --dash-sidebar: <?= PUST_COLOR_SIDEBAR ?>;
}

html[data-theme="dark"] {
  --dash-bg: <?= PUST_COLOR_DARK ?>;
  --dash-bg-alt: <?= PUST_COLOR_SLATE ?>;
  --dash-card: <?= PUST_COLOR_SLATE ?>;
  --dash-text: #F1F5F9;
  --dash-muted: <?= PUST_COLOR_TEXT_LIGHT ?>;
  --dash-border: #334155;
  --dash-input-bg: #1E293B;
  --dash-input-border: #475569;
  --dash-table-head: #1E293B;
  --dash-table-stripe: <?= PUST_COLOR_DARK ?>;
  --dash-table-hover: rgba(59, 130, 246, 0.12);
  --dash-icon-hover: rgba(59, 130, 246, 0.15);
  --dash-shadow: 0 4px 6px rgba(0, 0, 0, 0.35);
  --dash-link: <?= PUST_COLOR_BLUE_LIGHT ?>;
  --dash-link-hover: <?= PUST_COLOR_BLUE ?>;
  color-scheme: dark;
}
</style>
<script>window.PUST_BRAND={
  dark:'<?= PUST_COLOR_DARK ?>',
  blue:'<?= PUST_COLOR_BLUE ?>',
  blueLight:'<?= PUST_COLOR_BLUE_LIGHT ?>',
  amber:'<?= PUST_COLOR_AMBER ?>',
  emerald:'<?= PUST_COLOR_EMERALD ?>',
  rose:'<?= PUST_COLOR_ROSE ?>',
  cyan:'<?= PUST_COLOR_CYAN ?>',
  text:'<?= PUST_COLOR_TEXT ?>',
  muted:'<?= PUST_COLOR_TEXT_SECONDARY ?>'
};</script>
<meta name="theme-color" content="<?= PUST_COLOR_DARK ?>">
