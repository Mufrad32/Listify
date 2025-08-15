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
                <button type="submit" class="btn btn-primary w-100">Add Task</button>
            </form>
        </div>
        <div>
            <?php if (empty($tasks)): ?>
                <p class="text-center text-muted">No tasks yet. Add one above!</p>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="card task-card p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <form action="index.php?action=update&id=<?php echo $task['id']; ?>" method="POST" class="me-2">
                                    <input type="checkbox" name="is_completed" 
                                           <?php echo $task['is_completed'] ? 'checked' : ''; ?> 
                                           onchange="this.form.submit()" class="form-check-input">
                                </form>
                                <div>
                                    <h3 class="<?php echo $task['is_completed'] ? 'completed' : ''; ?>">
                                        <?php echo htmlspecialchars($task['title']); ?>
                                    </h3>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($task['comment'] ?: 'No comment'); ?></p>
                                </div>
                            </div>
                            <form action="index.php?action=destroy&id=<?php echo $task['id']; ?>" method="POST">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>