<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

/* ─────────────────  Handle actions ───────────────── */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("UPDATE users SET deleted_at = NOW() WHERE id = $id");
}
if (isset($_GET['restore'])) {
    $id = (int) $_GET['restore'];
    $conn->query("UPDATE users SET deleted_at = NULL WHERE id = $id");
}
if (isset($_GET['ban'])) {
    $id = (int) $_GET['ban'];
    $conn->query("UPDATE users SET is_banned = 1 WHERE id = $id");
}
if (isset($_GET['unban'])) {
    $id = (int) $_GET['unban'];
    $conn->query("UPDATE users SET is_banned = 0 WHERE id = $id");
}

/* ─────────────────  Fetch filtered list ───────────────── */
$search  = $conn->real_escape_string($_GET['search'] ?? '');
$users   = $conn->query(
    "SELECT * FROM users
     WHERE is_admin = 0
       AND (username LIKE '%$search%' OR email LIKE '%$search%')
     ORDER BY id DESC"
);
?>
<?php include '../includes/admin_navbar.php'; ?>
<link rel="stylesheet" href="../assets/css/admin_users.css">

<div class="page-container">
    <h2 class="page-title">Manage Users</h2>

    <form method="GET" class="search-form">
        <input type="text" name="search"
               placeholder="Search by email or username"
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>

    <div class="table-wrapper">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($u = $users->fetch_assoc()): ?>
                <tr class="<?= $u['deleted_at'] ? 'soft-deleted' : '' ?>">
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td>
                        <?php
                           if ($u['deleted_at'])      echo '<span class="tag deleted">Deleted</span>';
                           elseif ($u['is_banned'])    echo '<span class="tag banned">Banned</span>';
                           else                         echo '<span class="tag active">Active</span>';
                        ?>
                    </td>
                    <td>
                        <?php if ($u['deleted_at']): ?>
                            <a href="?restore=<?= $u['id'] ?>" class="btn restore">Restore</a>
                        <?php else: ?>
                            <a href="?delete=<?= $u['id'] ?>" class="btn delete"
                               onclick="return confirm('Soft‑delete this user?')">Delete</a>
                        <?php endif; ?>

                        <?php if ($u['is_banned']): ?>
                            <a href="?unban=<?= $u['id'] ?>" class="btn unban">Unban</a>
                        <?php else: ?>
                            <a href="?ban=<?= $u['id'] ?>" class="btn ban"
                               onclick="return confirm('Ban this user?')">Ban</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
