<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['student']);

$departments = getDepartments();
$priorities = getPriorities();
$categories = getCategories();

if (isPost()) {
    requireCsrf();
    $file = $_FILES['attachment'] ?? null;
    $result = createTicket($_POST, userId(), $file);
    if ($result['success']) {
        flash('success', 'Ticket ' . $result['ticket_number'] . ' submitted successfully!');
        redirect(appUrl('student/ticket-details.php?id=' . $result['ticket_id']));
    }
    flash('error', implode(' ', $result['errors']));
}

$pageTitle = 'Submit Ticket';
$breadcrumbs = [['label' => 'Dashboard', 'url' => appUrl('student/dashboard.php')], ['label' => 'New Ticket', 'url' => '']];
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="max-w-2xl dashboard-card rounded-xl p-6">
    <form method="POST" enctype="multipart/form-data" data-validate class="space-y-4">
        <?= csrfField() ?>
        <div>
            <label class="block text-sm font-medium mb-1">Department *</label>
            <select name="department_id" id="department_id" required class="w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-slate-800">
                <option value="">Select department</option>
                <?php foreach ($departments as $d): ?>
                <option value="<?= $d['id'] ?>"><?= e($d['name']) ?></option>
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
            <input type="text" name="subject" required maxlength="255" class="w-full px-4 py-2.5 border rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Description *</label>
            <textarea name="description" required rows="6" class="w-full px-4 py-2.5 border rounded-lg"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Attachment</label>
            <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="w-full text-sm">
            <p class="text-xs text-slate-500 mt-1">Max 5MB. JPG, PNG, PDF, DOC allowed.</p>
        </div>
        <button type="submit" class="px-6 py-2.5 pust-btn-primary font-semibold rounded-lg">Submit Ticket</button>
    </form>
</div>
<script>
document.getElementById('department_id')?.addEventListener('change', async function() {
    const sel = document.getElementById('category_id');
    sel.innerHTML = '<option value="">Loading...</option>';
    const res = await fetch('<?= appUrl('api/categories.php') ?>?department_id=' + this.value);
    const data = await res.json();
    sel.innerHTML = '<option value="">Select category</option>';
    data.categories.forEach(c => {
        sel.innerHTML += `<option value="${c.id}">${c.name}</option>`;
    });
});
</script>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
