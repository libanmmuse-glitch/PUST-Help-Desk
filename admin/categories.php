<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['admin']);

$db = getDB();
$departments = getDepartments();

if (isPost()) {
    requireCsrf();
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', input('name')));
    $db->prepare('INSERT INTO categories (name, slug, description, department_id) VALUES (?,?,?,?)')->execute([
        sanitizeString(input('name')), $slug, sanitizeString(input('description')),
        input('department_id') ? (int)input('department_id') : null
    ]);
    flash('success', 'Category added.');
    redirect(appUrl('admin/categories.php'));
}

$categories = $db->query('SELECT c.*, d.name AS dept_name FROM categories c LEFT JOIN departments d ON c.department_id = d.id ORDER BY c.name')->fetchAll();
$pageTitle = 'Categories';
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="dashboard-card rounded-xl p-6">
        <form method="POST" data-validate class="space-y-3"><?= csrfField() ?>
            <input name="name" required placeholder="Category name" class="w-full px-3 py-2 border rounded-lg">
            <select name="department_id" class="w-full px-3 py-2 border rounded-lg"><option value="">All Departments</option>
                <?php foreach ($departments as $d): ?><option value="<?= $d['id'] ?>"><?= e($d['name']) ?></option><?php endforeach; ?>
            </select>
            <textarea name="description" class="w-full px-3 py-2 border rounded-lg" rows="2"></textarea>
            <button class="w-full py-2 pust-btn-primary font-semibold rounded-lg">Add Category</button>
        </form>
    </div>
    <div class="lg:col-span-2 dashboard-card rounded-xl p-6">
        <table class="w-full text-sm"><thead><tr><th class="text-left py-2">Name</th><th>Department</th><th>Active</th></tr></thead>
        <tbody><?php foreach ($categories as $c): ?><tr class="border-t"><td class="py-2"><?= e($c['name']) ?></td><td><?= e($c['dept_name']??'All') ?></td><td><?= $c['is_active']?'Yes':'No' ?></td></tr><?php endforeach; ?></tbody>
        </table>
    </div>
</div>
<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
