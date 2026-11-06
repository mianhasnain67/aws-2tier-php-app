<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/db.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $message): never
{
    $_SESSION['flash'] = $message;
    header('Location: index.php');
    exit;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
$csrfToken = csrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrfToken, $_POST['csrf_token'] ?? '')) {
        redirect('Invalid form token. Please try again.');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            redirect('Item name is required.');
        }

        if ($action === 'create') {
            $stmt = $conn->prepare('INSERT INTO items (name, description) VALUES (?, ?)');
            $stmt->bind_param('ss', $name, $description);
            $stmt->execute();
            redirect('Item added successfully.');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            redirect('Invalid item.');
        }

        $stmt = $conn->prepare('UPDATE items SET name = ?, description = ? WHERE id = ?');
        $stmt->bind_param('ssi', $name, $description, $id);
        $stmt->execute();
        redirect('Item updated successfully.');
    }

    if ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            redirect('Invalid item.');
        }

        $stmt = $conn->prepare('DELETE FROM items WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        redirect('Item deleted successfully.');
    }
}

$editItem = null;
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($editId) {
    $stmt = $conn->prepare('SELECT id, name, description FROM items WHERE id = ?');
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    $editItem = $result->fetch_assoc();
}

$items = $conn->query('SELECT id, name, description, created_at FROM items ORDER BY created_at DESC, id DESC')->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2-Tier PHP CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
    <header class="page-header">
        <div>
            <p class="eyebrow">AWS 2-Tier Application</p>
            <h1>Items</h1>
        </div>
        <a class="button secondary" href="index.php">New item</a>
    </header>

    <?php if ($flash !== ''): ?>
        <div class="alert"><?= e($flash) ?></div>
    <?php endif; ?>

    <section class="card">
        <h2><?= $editItem ? 'Edit item' : 'Add item' ?></h2>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <input type="hidden" name="action" value="<?= $editItem ? 'update' : 'create' ?>">
            <?php if ($editItem): ?>
                <input type="hidden" name="id" value="<?= (int) $editItem['id'] ?>">
            <?php endif; ?>

            <div class="field">
                <label for="name">Name</label>
                <input id="name" name="name" required maxlength="150"
                       value="<?= e($editItem['name'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?= e($editItem['description'] ?? '') ?></textarea>
            </div>
            <button class="button" type="submit"><?= $editItem ? 'Save changes' : 'Add item' ?></button>
        </form>
    </section>

    <section class="card">
        <h2>Item list</h2>
        <?php if (count($items) === 0): ?>
            <p class="empty">No items yet.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= e($item['name']) ?></td>
                            <td><?= e($item['description']) ?></td>
                            <td><?= e(date('Y-m-d H:i', strtotime($item['created_at']))) ?></td>
                            <td class="actions">
                                <a class="button small" href="?edit=<?= (int) $item['id'] ?>">Edit</a>
                                <form method="post" onsubmit="return confirm('Delete this item?');">
                                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button class="button danger small" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>