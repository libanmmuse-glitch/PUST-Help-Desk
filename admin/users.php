<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/middleware/auth.php';
requireRole(['admin']);

$db = getDB();
$page = max(1, (int) input('page', 1));
$roleFilter = input('role', '');

// Load faculties and departments for dropdowns
$faculties = [];
try {
    $faculties = getFaculties();
} catch (Throwable $e) {}
$departments = getDepartments(false);
$staffCategories = [
    'lecturer' => 'Lecturer',
    'general' => 'General Staff'
];

if (isPost()) {
    requireCsrf();
    $action = input('action');
    
    if ($action === 'add') {
        $result = adminCreateUser($_POST);
        if ($result['success']) {
            flash('success', 'User created successfully.');
            redirect(appUrl('admin/users.php'));
        } else {
            flash('error', implode(' ', $result['errors']));
            setOld($_POST);
        }
    } elseif ($action === 'edit') {
        $uid = (int) input('user_id');
        $result = adminUpdateUser($uid, $_POST);
        if ($result['success']) {
            flash('success', 'User updated successfully.');
            redirect(appUrl('admin/users.php'));
        } else {
            flash('error', implode(' ', $result['errors']));
            setOld($_POST);
        }
    } elseif ($action === 'delete') {
        $uid = (int) input('user_id');
        if (adminDeleteUser($uid)) {
            flash('success', 'User deleted successfully.');
        } else {
            flash('error', 'Failed to delete user.');
        }
        redirect(appUrl('admin/users.php'));
    } elseif ($action === 'toggle') {
        $uid = (int) input('user_id');
        $stmt = $db->prepare('SELECT is_active FROM users WHERE id = ? AND id != ? AND deleted_at IS NULL');
        $stmt->execute([$uid, userId()]);
        $currentActive = (int) $stmt->fetchColumn();
        if ($currentActive > 0) {
            $db->prepare('UPDATE users SET is_active = 0, deleted_at = NOW() WHERE id = ? AND id != ?')->execute([$uid, userId()]);
        } else {
            $db->prepare('UPDATE users SET is_active = 1, deleted_at = NULL WHERE id = ? AND id != ?')->execute([$uid, userId()]);
        }
        flash('success', 'User status updated.');
        redirect(appUrl('admin/users.php'));
    }
}

$result = getAllUsers($roleFilter, $page, 15);
$users = $result['users'];
$pagination = $result['pagination'];

$pageTitle = 'Manage Users';
require dirname(__DIR__) . '/includes/templates/dashboard-layout.php';
?>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <form method="GET" class="flex gap-3 w-full sm:w-auto">
        <select name="role" class="px-4 py-2 border rounded-lg bg-white dark:bg-slate-800 dark:border-slate-700 text-sm">
            <option value="">All Roles</option>
            <?php foreach (['student','staff','admin'] as $r): ?>
            <option value="<?= $r ?>" <?= $roleFilter===$r?'selected':'' ?>><?= ucfirst($r) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="px-4 py-2 bg-pust-primary text-white font-medium rounded-lg text-sm transition hover:opacity-90">Filter</button>
    </form>
    
    <button type="button" data-modal-open="modal-add-user" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100 rounded-lg text-sm font-semibold transition flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Add User
    </button>
</div>

<div class="dashboard-card rounded-xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800">
                <tr>
                    <th class="px-4 py-3 text-left w-12"></th>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Role</th>
                    <th class="px-4 py-3 text-left">Faculty / Dept</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php foreach ($users as $u): ?>
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-4 py-3"><?= renderAvatar($u, 'sm') ?></td>
                    <td class="px-4 py-3 font-medium"><?= e($u['first_name'].' '.$u['last_name']) ?></td>
                    <td class="px-4 py-3 text-slate-500 dark:text-slate-400"><?= e($u['email']) ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wider <?= $u['role'] === 'admin' ? 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400' : ($u['role'] === 'staff' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400' : 'bg-green-50 text-green-700 dark:bg-green-950/30 dark:text-green-400') ?>">
                            <?= e($u['role']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300"><?php
                        if ($u['role'] === 'student') {
                            echo e($u['faculty_name'] ?? '—');
                        } elseif (($u['staff_category'] ?? '') === 'lecturer') {
                            echo e($u['faculty_name'] ?? '—') . ' <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">(Lecturer)</span>';
                        } else {
                            echo e($u['department_name'] ?? '—');
                        }
                    ?></td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium <?= $u['is_active'] ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-slate-50 text-slate-600 dark:bg-slate-950/30 dark:text-slate-400' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $u['is_active'] ? 'bg-emerald-500' : 'bg-slate-400' ?>"></span>
                            <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <!-- Toggle status -->
                            <?php if ($u['id'] != userId()): ?>
                            <form method="POST" class="inline"><?= csrfField() ?>
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="text-slate-500 hover:text-pust-primary dark:text-slate-400 dark:hover:text-white text-xs font-medium transition-colors" title="<?= $u['is_active'] ? 'Deactivate account' : 'Activate account' ?>">
                                    <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                            <?php endif; ?>

                            <!-- Edit button -->
                            <button type="button" data-modal-open="modal-edit-user-<?= $u['id'] ?>" class="text-pust-primary hover:underline text-xs font-semibold transition">Edit</button>

                            <!-- Delete button -->
                            <?php if ($u['id'] != userId()): ?>
                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to archive this user account? The account will be hidden from active views but its history will remain in the database.');">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs font-semibold transition">Archive</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $baseUrl = appUrl('admin/users.php'); require dirname(__DIR__) . '/includes/templates/pagination.php'; ?>
</div>

<!-- ================= ADD USER MODAL ================= -->
<div id="modal-add-user" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-backdrop absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative z-10 border border-slate-200 dark:border-slate-800 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Add New User Account</h3>
            <button type="button" data-modal-close class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-medium">&times;</button>
        </div>
        
        <form method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="add">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">First Name *</label>
                    <input type="text" name="first_name" required value="<?= old('action')==='add'?old('first_name'):'' ?>" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Last Name *</label>
                    <input type="text" name="last_name" required value="<?= old('action')==='add'?old('last_name'):'' ?>" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Email Address *</label>
                    <input type="email" name="email" required value="<?= old('action')==='add'?old('email'):'' ?>" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="<?= old('action')==='add'?old('phone'):'' ?>" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Account Password *</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm" placeholder="At least 8 chars, 1 uppercase, 1 number">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">User Role *</label>
                    <select name="role" id="add-role" required class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm bg-white">
                        <option value="student" <?= old('action')==='add'&&old('role')==='student'?'selected':'' ?>>Student</option>
                        <option value="staff" <?= old('action')==='add'&&old('role')==='staff'?'selected':'' ?>>Staff / Lecturer</option>
                        <option value="admin" <?= old('action')==='add'&&old('role')==='admin'?'selected':'' ?>>Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Status</label>
                    <select name="is_active" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm bg-white">
                        <option value="1" <?= old('action')==='add'&&old('is_active')==='1'?'selected':'' ?>>Active</option>
                        <option value="0" <?= old('action')==='add'&&old('is_active')==='0'?'selected':'' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <!-- DYNAMIC FIELDS -->
            <div id="add-student-id-wrapper">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Student ID *</label>
                <input type="text" name="student_id" id="add-student-id" value="<?= old('action')==='add'?old('student_id'):'' ?>" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm" placeholder="PUST2024001">
            </div>

            <div id="add-category-wrapper" style="display: none;">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Staff Category *</label>
                <select name="staff_category" id="add-staff-category" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm bg-white">
                    <option value="lecturer" <?= old('action')==='add'&&old('staff_category')==='lecturer'?'selected':'' ?>>Lecturer</option>
                    <option value="general" <?= old('action')==='add'&&old('staff_category')==='general'?'selected':'' ?>>General Staff</option>
                </select>
            </div>

            <div id="add-faculty-wrapper">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Faculty *</label>
                <select name="faculty_id" id="add-faculty-id" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm bg-white">
                    <option value="">Select Faculty</option>
                    <?php foreach ($faculties as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= old('action')==='add'&&old('faculty_id')==$f['id']?'selected':'' ?>><?= e($f['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="add-department-wrapper" style="display: none;">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Help Desk Department *</label>
                <select name="department_id" id="add-department-id" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm bg-white">
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= old('action')==='add'&&old('department_id')==$d['id']?'selected':'' ?>><?= e($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800 mt-5">
                <button type="button" data-modal-close class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-400 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-pust-primary text-white font-medium rounded-lg text-sm hover:opacity-90">Create User</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= EDIT USER MODALS ================= -->
<?php foreach ($users as $u): ?>
<div id="modal-edit-user-<?= $u['id'] ?>" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-backdrop absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative z-10 border border-slate-200 dark:border-slate-800 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit User: <?= e($u['first_name'] . ' ' . $u['last_name']) ?></h3>
            <button type="button" data-modal-close class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-medium">&times;</button>
        </div>
        
        <form method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">First Name *</label>
                    <input type="text" name="first_name" required value="<?= e($u['first_name']) ?>" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Last Name *</label>
                    <input type="text" name="last_name" required value="<?= e($u['last_name']) ?>" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Email Address *</label>
                    <input type="email" name="email" required value="<?= e($u['email']) ?>" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="<?= e($u['phone']) ?>" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm" placeholder="Update password (optional)">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">User Role *</label>
                    <?php if ($u['id'] == userId()): ?>
                        <!-- Admin cannot self-change role to prevent lockout -->
                        <input type="hidden" name="role" id="edit-<?= $u['id'] ?>-role" value="admin">
                        <input type="text" readonly disabled value="Administrator" class="w-full px-3 py-2 border rounded-lg bg-slate-50 dark:bg-slate-800 dark:border-slate-700 text-slate-500 text-sm">
                    <?php else: ?>
                        <select name="role" id="edit-<?= $u['id'] ?>-role" required class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm bg-white">
                            <option value="student" <?= $u['role']==='student'?'selected':'' ?>>Student</option>
                            <option value="staff" <?= $u['role']==='staff'?'selected':'' ?>>Staff / Lecturer</option>
                            <option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Administrator</option>
                        </select>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Status</label>
                    <?php if ($u['id'] == userId()): ?>
                        <!-- Admin cannot deactivate themselves -->
                        <input type="hidden" name="is_active" value="1">
                        <input type="text" readonly disabled value="Active" class="w-full px-3 py-2 border rounded-lg bg-slate-50 dark:bg-slate-800 dark:border-slate-700 text-slate-500 text-sm">
                    <?php else: ?>
                        <select name="is_active" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm bg-white">
                            <option value="1" <?= $u['is_active']?'selected':'' ?>>Active</option>
                            <option value="0" <?= !$u['is_active']?'selected':'' ?>>Inactive</option>
                        </select>
                    <?php endif; ?>
                </div>
            </div>

            <!-- DYNAMIC FIELDS FOR EDIT -->
            <div id="edit-<?= $u['id'] ?>-student-id-wrapper">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Student ID *</label>
                <input type="text" name="student_id" id="edit-<?= $u['id'] ?>-student-id" value="<?= e($u['student_id']) ?>" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm" placeholder="PUST2024001">
            </div>

            <div id="edit-<?= $u['id'] ?>-category-wrapper" style="display: none;">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Staff Category *</label>
                <select name="staff_category" id="edit-<?= $u['id'] ?>-staff-category" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm bg-white">
                    <option value="lecturer" <?= $u['staff_category']==='lecturer'?'selected':'' ?>>Lecturer</option>
                    <option value="general" <?= $u['staff_category']==='general'?'selected':'' ?>>General Staff</option>
                </select>
            </div>

            <div id="edit-<?= $u['id'] ?>-faculty-wrapper">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Faculty *</label>
                <select name="faculty_id" id="edit-<?= $u['id'] ?>-faculty-id" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm bg-white">
                    <option value="">Select Faculty</option>
                    <?php foreach ($faculties as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= $u['faculty_id']==$f['id']?'selected':'' ?>><?= e($f['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="edit-<?= $u['id'] ?>-department-wrapper" style="display: none;">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Help Desk Department *</label>
                <select name="department_id" id="edit-<?= $u['id'] ?>-department-id" class="w-full px-3 py-2 border rounded-lg dark:border-slate-700 dark:bg-slate-800 dark:text-white text-sm bg-white">
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= $u['department_id']==$d['id']?'selected':'' ?>><?= e($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800 mt-5">
                <button type="button" data-modal-close class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-400 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-pust-primary text-white font-medium rounded-lg text-sm hover:opacity-90">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

<script>
function toggleUserFields(prefix) {
    const roleSelect = document.getElementById(prefix + '-role');
    const categorySelect = document.getElementById(prefix + '-staff-category');
    
    const facultyWrapper = document.getElementById(prefix + '-faculty-wrapper');
    const departmentWrapper = document.getElementById(prefix + '-department-wrapper');
    const studentIdWrapper = document.getElementById(prefix + '-student-id-wrapper');
    
    const facultySelect = document.getElementById(prefix + '-faculty-id');
    const departmentSelect = document.getElementById(prefix + '-department-id');
    const studentIdInput = document.getElementById(prefix + '-student-id');
    const categorySelectEl = document.getElementById(prefix + '-staff-category');
    const categoryWrapper = document.getElementById(prefix + '-category-wrapper');

    if (!roleSelect) return;

    const role = roleSelect.value;
    const category = categorySelect ? categorySelect.value : 'general';

    if (role === 'student') {
        if (studentIdWrapper) studentIdWrapper.style.display = '';
        if (studentIdInput) {
            studentIdInput.required = true;
            studentIdInput.disabled = false;
        }
        
        if (facultyWrapper) facultyWrapper.style.display = '';
        if (facultySelect) {
            facultySelect.required = true;
            facultySelect.disabled = false;
        }
        
        if (departmentWrapper) departmentWrapper.style.display = 'none';
        if (departmentSelect) {
            departmentSelect.required = false;
            departmentSelect.disabled = true;
        }
        
        if (categoryWrapper) categoryWrapper.style.display = 'none';
        if (categorySelectEl) {
            categorySelectEl.required = false;
            categorySelectEl.disabled = true;
        }
    } else if (role === 'staff') {
        if (studentIdWrapper) studentIdWrapper.style.display = 'none';
        if (studentIdInput) {
            studentIdInput.required = false;
            studentIdInput.disabled = true;
        }
        
        if (categoryWrapper) categoryWrapper.style.display = '';
        if (categorySelectEl) {
            categorySelectEl.required = true;
            categorySelectEl.disabled = false;
        }

        if (category === 'lecturer') {
            if (facultyWrapper) facultyWrapper.style.display = '';
            if (facultySelect) {
                facultySelect.required = true;
                facultySelect.disabled = false;
            }
            
            if (departmentWrapper) departmentWrapper.style.display = 'none';
            if (departmentSelect) {
                departmentSelect.required = false;
                departmentSelect.disabled = true;
            }
        } else {
            if (facultyWrapper) facultyWrapper.style.display = 'none';
            if (facultySelect) {
                facultySelect.required = false;
                facultySelect.disabled = true;
            }
            
            if (departmentWrapper) departmentWrapper.style.display = '';
            if (departmentSelect) {
                departmentSelect.required = true;
                departmentSelect.disabled = false;
            }
        }
    } else { // admin
        if (studentIdWrapper) studentIdWrapper.style.display = 'none';
        if (studentIdInput) {
            studentIdInput.required = false;
            studentIdInput.disabled = true;
        }
        
        if (facultyWrapper) facultyWrapper.style.display = 'none';
        if (facultySelect) {
            facultySelect.required = false;
            facultySelect.disabled = true;
        }
        
        if (departmentWrapper) departmentWrapper.style.display = '';
        if (departmentSelect) {
            departmentSelect.required = false;
            departmentSelect.disabled = false;
        }
        
        if (categoryWrapper) categoryWrapper.style.display = 'none';
        if (categorySelectEl) {
            categorySelectEl.required = false;
            categorySelectEl.disabled = true;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Setup add
    const addRole = document.getElementById('add-role');
    const addCat = document.getElementById('add-staff-category');
    if (addRole) {
        addRole.addEventListener('change', () => toggleUserFields('add'));
        addCat?.addEventListener('change', () => toggleUserFields('add'));
        toggleUserFields('add');
    }

    // Setup edits
    document.querySelectorAll('[id^="modal-edit-user-"]').forEach(modal => {
        const id = modal.id.replace('modal-edit-user-', '');
        const role = document.getElementById('edit-' + id + '-role');
        const cat = document.getElementById('edit-' + id + '-staff-category');
        if (role) {
            role.addEventListener('change', () => toggleUserFields('edit-' + id));
            cat?.addEventListener('change', () => toggleUserFields('edit-' + id));
            toggleUserFields('edit-' + id);
        }
    });

    // Auto-open modals on error validation load
    <?php if (old('action') === 'add'): ?>
        document.getElementById('modal-add-user')?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    <?php elseif (old('action') === 'edit'): ?>
        document.getElementById('modal-edit-user-<?= (int)old('user_id') ?>')?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    <?php endif; ?>
});
</script>

<?php require dirname(__DIR__) . '/includes/templates/dashboard-footer.php'; ?>
