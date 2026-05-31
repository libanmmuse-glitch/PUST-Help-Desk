<?php
/**
 * PUST Help Desk — premium UI color system (single source of truth).
 *
 * Palette direction:
 * - Blue: trust, academics, and digital service
 * - White: clarity and space
 * - Green: campus support / progress / success
 */
define('PUST_DEFAULT_THEME', 'light');

/* Primary brand */
define('PUST_COLOR_DARK', '#081423');
define('PUST_COLOR_BLUE', '#2F6FDB');
define('PUST_COLOR_BLUE_LIGHT', '#EAF3FF');
define('PUST_COLOR_SLATE', '#18314F');
define('PUST_COLOR_PRIMARY_INK', '#1E4B88');

/* Accents */
define('PUST_COLOR_AMBER', '#2FA66A');
define('PUST_COLOR_AMBER_LIGHT', '#DDF4E7');
define('PUST_COLOR_EMERALD', '#24965E');
define('PUST_COLOR_ROSE', '#D94C63');
define('PUST_COLOR_CYAN', '#31B6DA');

/* Backgrounds */
define('PUST_COLOR_BG', '#F6FAFF');
define('PUST_COLOR_BG_SECONDARY', '#EDF8F1');
define('PUST_COLOR_CARD', '#FFFFFF');
define('PUST_COLOR_SIDEBAR', '#0E2138');

/* Text */
define('PUST_COLOR_TEXT', '#0F172A');
define('PUST_COLOR_TEXT_SECONDARY', '#5B6B7F');
define('PUST_COLOR_TEXT_LIGHT', '#CBD5E1');
define('PUST_COLOR_WHITE', '#FFFFFF');

/* Borders & shadows */
define('PUST_COLOR_BORDER', '#D7E3EF');
define('PUST_SHADOW_SOFT', '0 1px 3px rgba(15, 23, 42, 0.08), 0 1px 2px rgba(15, 23, 42, 0.05)');
define('PUST_SHADOW_CARD', '0 12px 30px rgba(15, 23, 42, 0.06)');
define('PUST_SHADOW_HOVER', '0 18px 40px -10px rgba(47, 111, 219, 0.18), 0 8px 16px -8px rgba(36, 150, 94, 0.14)');

/* Legacy aliases (templates) */
define('PUST_COLOR_PRIMARY', PUST_COLOR_BLUE);
define('PUST_COLOR_PRIMARY_DARK', PUST_COLOR_PRIMARY_INK);
define('PUST_COLOR_PRIMARY_LIGHT', PUST_COLOR_BLUE_LIGHT);
define('PUST_COLOR_ACCENT', PUST_COLOR_AMBER);
