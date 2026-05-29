<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['admin']);

$db = getDB();
if (isPost()) {
    requireCsrf();
    if (input('action') === 'add') {
        $db->prepare('INSERT INTO departments (name, code, description, email) VALUES (?,?,?,?)')->execute([
            sanitizeString(input('name')), strtoupper(sanitizeString(input('code'))),
            sanitizeString(input('description')), sanitizeString(input('email'))
        ]);
        flash('success', 'Department added.');
    }
    redirect(appUrl('admin/departments.php'));
}

$departments = $db->query('SELECT d.*, (SELECT COUNT(*) FROM tickets t WHERE t.department_id = d.id) AS ticket_count FROM departments d ORDER BY name')->fetchAll();
$pageTitle = 'Departments';
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="dashboard-card rounded-xl p-6">
        <h3 class="font-semibold mb-4">Add Department</h3>
        <form method="POST" data-validate class="space-y-3"><?= csrfField() ?><input type="hidden" name="action" value="add">
            <input name="name" required placeholder="Name" class="w-full px-3 py-2 border rounded-lg">
            <input name="code" required placeholder="Code" class="w-full px-3 py-2 border rounded-lg">
            <input name="email" type="email" placeholder="Email" class="w-full px-3 py-2 border rounded-lg">
            <textarea name="description" placeholder="Description" class="w-full px-3 py-2 border rounded-lg" rows="2"></textarea>
            <button class="w-full py-2 pust-btn-primary font-semibold rounded-lg">Add</button>
        </form>
    </div>
    <div class="lg:col-span-2 dashboard-card rounded-xl p-6 overflow-x-auto">
        <table class="w-full text-sm"><thead><tr><th class="text-left py-2">Name</th><th>Code</th><th>Tickets</th><th>Status</th></tr></thead>
        <tbody class="divide-y"><?php foreach ($departments as $d): ?>
        <tr><td class="py-3"><?= e($d['name']) ?></td><td><?= e($d['code']) ?></td><td><?= $d['ticket_count'] ?></td>
        <td><?= $d['is_active']?'Active':'Inactive' ?></td></tr>
        <?php endforeach; ?></tbody></table>
    </div>
</div>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
