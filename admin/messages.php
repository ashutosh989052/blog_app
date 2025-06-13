<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) exit("Unauthorized");

// Delete message
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM messages WHERE id = $id");
    header("Location: messages.php");
    exit;
}

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $perPage;

// Search
$search = $_GET['search'] ?? '';
$searchEscaped = $conn->real_escape_string($search);
$whereClause = "";
if (!empty($search)) {
    $whereClause = "WHERE name LIKE '%$searchEscaped%' OR email LIKE '%$searchEscaped%' OR message LIKE '%$searchEscaped%'";
}

// Count total records for pagination
$countRes = $conn->query("SELECT COUNT(*) AS total FROM messages $whereClause");
$totalMessages = $countRes->fetch_assoc()['total'];
$totalPages = ceil($totalMessages / $perPage);

// Fetch messages
$messages = $conn->query("SELECT * FROM messages $whereClause ORDER BY submitted_at DESC LIMIT $start, $perPage");
?>
<link rel="stylesheet" href="../assets/css/messages.css">
<?php include '../includes/admin_navbar.php'; ?>

<div class="page-container">
    <h2 class="page-title">Contact Messages</h2>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search by name, email or message..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <div class="table-wrapper">
        <table class="message-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Submitted At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($messages->num_rows > 0): ?>
                    <?php while ($msg = $messages->fetch_assoc()): ?>
                        <tr>
                            <td><?= $msg['id'] ?></td>
                            <td><?= htmlspecialchars($msg['name']) ?></td>
                            <td><?= htmlspecialchars($msg['email']) ?></td>
                            <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                            <td><?= $msg['submitted_at'] ?></td>
                            <td>
                                <a href="?delete=<?= $msg['id'] ?>" class="btn delete" onclick="return confirm('Delete this message?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="empty-row">No messages found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>
