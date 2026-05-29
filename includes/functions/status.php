<?php
/**
 * Ticket status definitions and tracking helpers
 */

function getTicketStatuses(): array
{
    return [
        'pending'     => ['label' => 'Pending',     'color' => PUST_COLOR_TEXT_SECONDARY, 'step' => 1],
        'open'        => ['label' => 'Open',        'color' => PUST_COLOR_BLUE, 'step' => 2],
        'in_progress' => ['label' => 'In Progress', 'color' => PUST_COLOR_AMBER, 'step' => 3],
        'resolved'    => ['label' => 'Resolved',    'color' => PUST_COLOR_EMERALD, 'step' => 4],
        'closed'      => ['label' => 'Closed',      'color' => PUST_COLOR_TEXT_SECONDARY, 'step' => 5],
    ];
}

function getTicketStatusSlugs(): array
{
    return array_keys(getTicketStatuses());
}

function isValidTicketStatus(string $status): bool
{
    return in_array($status, getTicketStatusSlugs(), true);
}

/** Active ?status= filter from the request (empty string = show all). */
function activeStatusFilter(): string
{
    if (!array_key_exists('status', $_GET)) {
        return '';
    }
    $status = trim((string) ($_GET['status'] ?? ''));
    if ($status === '' || !isValidTicketStatus($status)) {
        return '';
    }

    return $status;
}

function getStatusLabel(string $status): string
{
    $statuses = getTicketStatuses();
    return $statuses[$status]['label'] ?? ucwords(str_replace('_', ' ', $status));
}

function getStatusColor(string $status): string
{
    $statuses = getTicketStatuses();
    return $statuses[$status]['color'] ?? PUST_COLOR_TEXT_SECONDARY;
}

function getStatusStep(string $status): int
{
    $statuses = getTicketStatuses();
    return (int) ($statuses[$status]['step'] ?? 0);
}

function renderStatusTracker(string $currentStatus): string
{
    $statuses = getTicketStatuses();
    $currentStep = getStatusStep($currentStatus);
    $slugs = array_keys($statuses);
    $html = '<div class="status-tracker" role="list">';

    foreach ($slugs as $i => $slug) {
        $info = $statuses[$slug];
        $step = (int) $info['step'];
        $color = $info['color'];
        $classes = ['status-step'];
        if ($step < $currentStep) {
            $classes[] = 'status-step--completed';
        }
        if ($slug === $currentStatus) {
            $classes[] = 'status-step--current';
        }
        $dot = $step < $currentStep ? '&#10003;' : (string) $step;
        $html .= '<div class="' . implode(' ', $classes) . '" role="listitem" style="--step-color:' . e($color) . '">';
        $html .= '<div class="status-step__dot">' . $dot . '</div>';
        $html .= '<span class="status-step__label">' . e($info['label']) . '</span></div>';

        if ($i < count($slugs) - 1) {
            $done = $step < $currentStep;
            $html .= '<div class="status-step__line' . ($done ? ' status-step__line--done' : '') . '" aria-hidden="true"></div>';
        }
    }

    return $html . '</div>';
}

function getStatusCounts(?int $departmentId = null, ?int $userId = null, ?int $staffUserId = null): array
{
    $filters = [];
    if ($staffUserId) {
        $filters['staff_scope_user_id'] = $staffUserId;
        $filters['staff_scope_department_id'] = $departmentId ?? 0;
    } elseif ($departmentId) {
        $filters['department_id'] = $departmentId;
    }
    if ($userId) {
        $filters['user_id'] = $userId;
    }

    return getStatusCountsForFilters($filters);
}

/**
 * Status breakdown for ticket list filters (excludes active status filter so chips show all statuses).
 */
function getStatusCountsForFilters(array $filters = []): array
{
    $db = getDB();
    unset($filters['status'], $filters['status_in']);
    $params = [];
    $whereSql = buildTicketFilterWhere($filters, $params, ['exclude_status' => true]);

    $counts = [
        'pending' => 0,
        'open' => 0,
        'in_progress' => 0,
        'resolved' => 0,
        'closed' => 0,
        'total' => 0,
        'active' => 0,
    ];

    $stmt = $db->prepare("SELECT t.status, COUNT(*) AS c FROM tickets t WHERE $whereSql GROUP BY t.status");
    $stmt->execute($params);

    while ($row = $stmt->fetch()) {
        $slug = $row['status'];
        $c = (int) $row['c'];
        if (isset($counts[$slug])) {
            $counts[$slug] = $c;
        }
        $counts['total'] += $c;
    }

    $counts['active'] = $counts['pending'] + $counts['open'] + $counts['in_progress'];

    return $counts;
}

function suggestStatusAfterAssign(string $currentStatus): ?string
{
    return match ($currentStatus) {
        'pending' => 'open',
        default => null,
    };
}

/** Move ticket forward when staff/admin posts a public reply. */
function suggestStatusAfterStaffReply(string $currentStatus): ?string
{
    return match ($currentStatus) {
        'pending', 'open' => 'in_progress',
        default => null,
    };
}

/** @return list<string> SQL fragments for SET clause */
function applyStatusSideEffects(string $newStatus, string $oldStatus): array
{
    $effects = [];

    if ($newStatus === 'resolved' && $oldStatus !== 'resolved') {
        $effects[] = 'resolved_at = NOW()';
    }
    if ($newStatus === 'closed' && $oldStatus !== 'closed') {
        $effects[] = 'closed_at = NOW()';
    }
    if (in_array($newStatus, ['pending', 'open', 'in_progress'], true)
        && in_array($oldStatus, ['resolved', 'closed'], true)) {
        $effects[] = 'resolved_at = NULL';
        $effects[] = 'closed_at = NULL';
    }

    return $effects;
}
