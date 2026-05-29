<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['staff']);

$departments = getDepartments();
$priorities = getPriorities();

if (isPost()) {
    requireCsrf();
    $file = $_FILES['attachment'] ?? null;
    $result = createTicket($_POST, userId(), $file);
    if ($result['success']) {
        flash('success', 'Ticket ' . $result['ticket_number'] . ' submitted successfully!');
        redirect(appUrl('staff/ticket-details.php?id=' . $result['ticket_id']));
    }
    flash('error', implode(' ', $result['errors']));
}

$pageTitle = 'Submit Ticket';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => appUrl('staff/dashboard.php')],
    ['label' => 'New Ticket', 'url' => ''],
];
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="max-w-2xl dashboard-card rounded-xl p-6">
    <div class="mb-6">
        <h2 class="text-xl font-semibold dash-text">New Ticket</h2>
        <p class="text-sm dash-muted mt-1">Select the department that should receive your request and provide the details below.</p>
    </div>
    <form method="POST" enctype="multipart/form-data" data-validate class="space-y-4">
        <?= csrfField() ?>
        <div>
            <label class="block text-sm font-medium mb-1">Department *</label>
            <select name="department_id" id="department_id" required class="w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-slate-800">
                <option value="">Select department</option>
                <?php foreach ($departments as $d): ?>
                <option value="<?= $d['id'] ?>" <?= (string) input('department_id', '') === (string) $d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select name="category_id" id="category_id" class="w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-slate-800">
                <option value="">Select category</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Priority</label>
            <select name="priority_id" class="w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-slate-800">
                <?php foreach ($priorities as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $p['slug'] === 'medium' ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Subject *</label>
            <input type="text" name="subject" required maxlength="255" value="<?= e(input('subject', '')) ?>" class="w-full px-4 py-2.5 border rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Description *</label>
            <textarea name="description" required rows="6" class="w-full px-4 py-2.5 border rounded-lg"><?= e(input('description', '')) ?></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Attachment</label>
            <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="w-full text-sm">
            <p class="text-xs dash-muted mt-1">Max 5MB. JPG, PNG, PDF, DOC allowed.</p>
        </div>
        <button type="submit" class="px-6 py-2.5 pust-btn-primary font-semibold rounded-lg">Submit Ticket</button>
    </form>
</div>
<script>
const departmentSelect = document.getElementById('department_id');
const categorySelect = document.getElementById('category_id');

async function loadCategories(departmentId) {
    categorySelect.innerHTML = '<option value="">Select category</option>';
    if (!departmentId) return;

    categorySelect.innerHTML = '<option value="">Loading...</option>';
    const res = await fetch('<?= appUrl('api/categories.php') ?>?department_id=' + encodeURIComponent(departmentId));
    const data = await res.json();
    categorySelect.innerHTML = '<option value="">Select category</option>';
    data.categories.forEach(c => {
        categorySelect.innerHTML += `<option value="${c.id}">${c.name}</option>`;
    });
}

departmentSelect?.addEventListener('change', function() {
    loadCategories(this.value);
});

if (departmentSelect?.value) {
    loadCategories(departmentSelect.value);
}
</script>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
