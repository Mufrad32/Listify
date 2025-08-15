<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listify - ToDo List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin-top: 20px; }
        .task-card { margin-bottom: 15px; }
        .completed { text-decoration: line-through; color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Listify</h1>
        <div class="card p-3 mb-4">
            <h2 class="h5 mb-3">Add New Task</h2>
            <form action="index.php?action=store" method="POST">
                <input type="text" name="title" class="form-control mb-2" placeholder="Task title" required>
                <textarea name="comment" class="form-control mb-2" placeholder="Comment" rows="3"></textarea>
                <input type="datetime-local" name="deadline" class="form-control mb-2" placeholder="Deadline (optional)">
                <button type="submit" class="btn btn-primary w-100">Add Task</button>
            </form>
        </div>
        <div class="d-flex justify-content-between mb-3">
            <div>
                <a href="index.php?filter=all" class="btn btn-outline-primary btn-sm <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'all' || !isset($_GET['filter'])) ? 'active' : ''; ?>">All</a>
                <a href="index.php?filter=completed" class="btn btn-outline-primary btn-sm <?php echo isset($_GET['filter']) && $_GET['filter'] === 'completed' ? 'active' : ''; ?>">Completed</a>
                <a href="index.php?filter=incomplete" class="btn btn-outline-primary btn-sm <?php echo isset($_GET['filter']) && $_GET['filter'] === 'incomplete' ? 'active' : ''; ?>">Incomplete</a>
            </div>
            <div>
                <select onchange="window.location.href='index.php?sort='+this.value" class="form-select form-select-sm">
                    <option value="title" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'title' ? 'selected' : ''; ?>>Sort by Title</option>
                    <option value="status" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'status' ? 'selected' : ''; ?>>Sort by Status</option>
                </select>
            </div>
        </div>
        <div>
            <?php if (empty($tasks)): ?>
                <p class="text-center text-muted">No tasks yet. Add one above!</p>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="card task-card p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-grow-1">
                                <form action="index.php?action=update&id=<?php echo $task['id']; ?>" method="POST" class="me-2">
                                    <input type="checkbox" name="is_completed" 
                                           <?php echo $task['is_completed'] ? 'checked' : ''; ?> 
                                           onchange="this.form.submit()" class="form-check-input">
                                </form>
                                <div class="flex-grow-1">
                                    <h3 class="<?php echo $task['is_completed'] ? 'completed' : ''; ?>">
                                        <?php echo htmlspecialchars($task['title']); ?>
                                    </h3>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($task['comment'] ?: 'No comment'); ?></p>
                                    <p class="text-muted mb-0">Deadline: <?php echo isset($task['deadline']) && $task['deadline'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($task['deadline']))) : 'None'; ?></p>
                                    <button class="btn btn-link btn-sm" onclick="document.getElementById('edit-form-<?php echo $task['id']; ?>').style.display='block';">Edit</button>
                                </div>
                            </div>
                            <form action="index.php?action=destroy&id=<?php echo $task['id']; ?>" method="POST">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                        <div id="edit-form-<?php echo $task['id']; ?>" style="display: none;" class="mt-2">
                            <form action="index.php?action=edit&id=<?php echo $task['id']; ?>" method="POST">
                                <input type="text" name="title" class="form-control mb-2" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                                <textarea name="comment" class="form-control mb-2" rows="3"><?php echo htmlspecialchars($task['comment'] ?: ''); ?></textarea>
                                <input type="datetime-local" name="deadline" class="form-control mb-2" value="<?php echo isset($task['deadline']) && $task['deadline'] ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($task['deadline']))) : ''; ?>">
                                <button type="submit" class="btn btn-success btn-sm">Save</button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="this.parentElement.style.display='none';">Cancel</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>