<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listify - ToDo List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa 0%, #ffffff 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        .container {
            max-width: 700px;
            margin-top: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            background: #fff;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }
        .task-card {
            margin-bottom: 15px;
            padding: 15px;
            border-left: 4px solid #007bff;
        }
        .completed {
            text-decoration: line-through;
            color: #6c757d;
            opacity: 0.7;
        }
        .timer-section {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        #timer-display {
            font-size: 2.5rem;
            font-weight: 700;
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
        }
        .timer-controls button {
            margin-right: 10px;
            transition: background-color 0.3s;
        }
        .timer-controls button:hover {
            filter: brightness(1.1);
        }
        .deadline-red {
            color: #dc3545 !important;
            font-weight: 600;
        }
        .priority-high { color: #dc3545; }
        .priority-normal { color: #17a2b8; }
        .priority-low { color: #28a745; }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
        }
        @media (max-width: 768px) {
            .container { margin-top: 10px; padding: 10px; }
            .task-card { padding: 10px; }
            #timer-display { font-size: 2rem; }
        }
        .popup-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            max-width: 300px;
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <header class="header text-center">
        <h1>Listify</h1>
    </header>
    <div class="container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="text-end mb-3">
                <form action="index.php?action=logout" method="POST">
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        <?php endif; ?>
        <?php
        // Display pop-up if message exists
        if (isset($_SESSION['popup_message'])) {
            echo '<div class="alert alert-success popup-alert" id="popupAlert" role="alert">' . htmlspecialchars($_SESSION['popup_message']) . '</div>';
            unset($_SESSION['popup_message']); // Clear the message after displaying
        }
        ?>
        <div class="card p-4 mb-4">
            <h2 class="h4 mb-3 text-primary">Add New Task</h2>
            <form action="index.php?action=store" method="POST">
                <input type="text" name="title" class="form-control mb-2" placeholder="Task title" required>
                <textarea name="comment" class="form-control mb-2" placeholder="Comment" rows="3"></textarea>
                <input type="datetime-local" name="deadline" class="form-control mb-2" placeholder="Deadline (optional)">
                <select name="priority" class="form-control mb-2">
                    <option value="low">Low</option>
                    <option value="normal" selected>Normal</option>
                    <option value="high">High</option>
                </select>
                <input type="text" name="tags" class="form-control mb-2" placeholder="Tags (space-separated, e.g., work home)">
                <div class="form-check mb-2">
                    <input type="checkbox" name="is_repeatable" id="isRepeatable" class="form-check-input" value="1">
                    <label class="form-check-label" for="isRepeatable">Repeatable Task</label>
                </div>
                <select name="repeat_cadence" id="repeatCadence" class="form-control mb-2" disabled>
                    <option value="once">Once</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="weekday">Weekday</option>
                    <option value="monthly">Monthly</option>
                    <option value="annually">Annually</option>
                </select>
                <button type="submit" class="btn btn-primary w-100">Add Task</button>
            </form>
        </div>
        <div class="mb-3">
            <form action="index.php" method="GET" class="input-group">
                <input type="text" name="search_tags" class="form-control" placeholder="Search by tags (space-separated)">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div class="d-flex justify-content-between mb-3">
            <div class="btn-group" role="group">
                <a href="index.php?filter=all" class="btn btn-outline-primary <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'all' || !isset($_GET['filter'])) ? 'active' : ''; ?>">All</a>
                <a href="index.php?filter=completed" class="btn btn-outline-primary <?php echo isset($_GET['filter']) && $_GET['filter'] === 'completed' ? 'active' : ''; ?>">Completed</a>
                <a href="index.php?filter=incomplete" class="btn btn-outline-primary <?php echo isset($_GET['filter']) && $_GET['filter'] === 'incomplete' ? 'active' : ''; ?>">Incomplete</a>
            </div>
            <div>
                <select onchange="window.location.href='index.php?sort='+this.value" class="form-select form-select-sm">
                    <option value="created_at" <?php echo !isset($_GET['sort']) || $_GET['sort'] === 'created_at' ? 'selected' : ''; ?>>Sort by Latest</option>
                    <option value="title" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'title' ? 'selected' : ''; ?>>Sort by Title</option>
                    <option value="status" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'status' ? 'selected' : ''; ?>>Sort by Status</option>
                    <option value="priority" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'priority' ? 'selected' : ''; ?>>Sort by Priority</option>
                    <option value="deadline" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'deadline' ? 'selected' : ''; ?>>Sort by Deadline</option>
                </select>
            </div>
        </div>
        <div class="timer-section">
            <h2 class="h4 mb-3 text-success">Focus Timer</h2>
            <div id="timer-display">00:00</div>
            <input type="number" id="timer-duration" class="form-control mb-2" min="1" max="60" value="25" placeholder="Minutes">
            <div class="timer-controls">
                <button onclick="startTimer()" class="btn btn-success">Start</button>
                <button onclick="pauseTimer()" class="btn btn-warning" disabled>Pause</button>
                <button onclick="resetTimer()" class="btn btn-secondary">Reset</button>
            </div>
        </div>
        <div>
            <?php if (empty($tasks)): ?>
                <p class="text-center text-muted">No tasks yet. Add one above!</p>
            <?php else: ?>
                <?php
                $currentDate = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="card task-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-grow-1">
                                <form action="index.php?action=update&id=<?php echo htmlspecialchars($task['id']); ?>" method="POST" class="me-2">
                                    <input type="checkbox" name="is_completed" 
                                           <?php echo $task['is_completed'] ? 'checked' : ''; ?> 
                                           onchange="this.disabled=true; this.form.submit()" class="form-check-input">
                                </form>
                                <div class="flex-grow-1">
                                    <h3 class="<?php echo $task['is_completed'] ? 'completed' : ''; ?>">
                                        <?php echo htmlspecialchars($task['title']); ?>
                                    </h3>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($task['comment'] ?: 'No comment'); ?></p>
                                    <p class="text-muted mb-0 priority-<?php echo strtolower($task['priority']); ?>">Priority: <?php echo htmlspecialchars($task['priority']); ?></p>
                                    <?php if ($task['deadline']): ?>
                                        <?php
                                        $deadlineDate = new DateTime($task['deadline']);
                                        $daysLeft = $currentDate->diff($deadlineDate)->days;
                                        $isRed = $daysLeft <= 2 && !$task['is_completed'];
                                        ?>
                                        <p class="text-muted mb-0 <?php echo $isRed ? 'deadline-red' : ''; ?>">
                                            Deadline: <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($task['deadline']))); ?>
                                            (<?php echo $daysLeft; ?> day<?php echo $daysLeft !== 1 ? 's' : ''; ?> left)
                                        </p>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">Deadline: None</p>
                                    <?php endif; ?>
                                    <?php if ($task['tags']): ?>
                                        <p class="text-muted mb-0">Tags: <?php echo htmlspecialchars($task['tags']); ?></p>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">Tags: None</p>
                                    <?php endif; ?>
                                    <?php if ($task['is_repeatable']): ?>
                                        <p class="text-muted mb-0">Repeats: <?php echo htmlspecialchars($task['repeat_cadence']); ?></p>
                                    <?php endif; ?>
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
                                <select name="priority" class="form-control mb-2">
                                    <option value="low" <?php echo $task['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                                    <option value="normal" <?php echo $task['priority'] === 'normal' ? 'selected' : ''; ?>>Normal</option>
                                    <option value="high" <?php echo $task['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                                </select>
                                <input type="text" name="tags" class="form-control mb-2" value="<?php echo htmlspecialchars($task['tags'] ?: ''); ?>" placeholder="Tags (space-separated)">
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="is_repeatable" id="editRepeatable<?php echo $task['id']; ?>" class="form-check-input" value="1" <?php echo $task['is_repeatable'] ? 'checked' : ''; ?> onchange="document.getElementById('editCadence<?php echo $task['id']; ?>').disabled = !this.checked;">
                                    <label class="form-check-label" for="editRepeatable<?php echo $task['id']; ?>">Repeatable Task</label>
                                </div>
                                <select name="repeat_cadence" id="editCadence<?php echo $task['id']; ?>" class="form-control mb-2" <?php echo !$task['is_repeatable'] ? 'disabled' : ''; ?>>
                                    <option value="once" <?php echo !$task['is_repeatable'] || $task['repeat_cadence'] === 'once' ? 'selected' : ''; ?>>Once</option>
                                    <option value="daily" <?php echo $task['repeat_cadence'] === 'daily' ? 'selected' : ''; ?>>Daily</option>
                                    <option value="weekly" <?php echo $task['repeat_cadence'] === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                                    <option value="weekday" <?php echo $task['repeat_cadence'] === 'weekday' ? 'selected' : ''; ?>>Weekday</option>
                                    <option value="monthly" <?php echo $task['repeat_cadence'] === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                                    <option value="annually" <?php echo $task['repeat_cadence'] === 'annually' ? 'selected' : ''; ?>>Annually</option>
                                </select>
                                <button type="submit" class="btn btn-success btn-sm">Save</button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="this.parentElement.style.display='none';">Cancel</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <script>
        document.getElementById('isRepeatable').addEventListener('change', function() {
            document.getElementById('repeatCadence').disabled = !this.checked;
            if (!this.checked) {
                document.getElementById('repeatCadence').value = 'once';
            }
        });

        document.querySelectorAll('input[name="is_repeatable"]').forEach(checkbox => {
            const id = checkbox.id.replace('editRepeatable', '');
            const cadenceSelect = document.getElementById('editCadence' + id);
            checkbox.addEventListener('change', function() {
                cadenceSelect.disabled = !this.checked;
                if (!this.checked) {
                    cadenceSelect.value = 'once';
                }
            });
        });

        let time = 00 * 60; // Default 00 minutes in seconds
        let timer = null;
        const display = document.getElementById('timer-display');
        const durationInput = document.getElementById('timer-duration');
        const startBtn = document.querySelector('.timer-controls .btn-success');
        const pauseBtn = document.querySelector('.timer-controls .btn-warning');
        const resetBtn = document.querySelector('.timer-controls .btn-secondary');

        function updateDisplay() {
            const minutes = Math.floor(time / 60);
            const seconds = time % 60;
            display.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        function startTimer() {
            const duration = parseInt(durationInput.value) || 25;
            if (!timer) {
                time = duration * 60;
                timer = setInterval(() => {
                    if (time > 0) {
                        time--;
                        updateDisplay();
                    } else {
                        clearInterval(timer);
                        timer = null;
                        pauseBtn.disabled = true;
                        alert('Focus session completed!');
                    }
                }, 1000);
                startBtn.disabled = true;
                pauseBtn.disabled = false;
            }
        }

        function pauseTimer() {
            if (timer) {
                clearInterval(timer);
                timer = null;
                startBtn.disabled = false;
                pauseBtn.disabled = true;
            }
        }

        function resetTimer() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
            time = parseInt(durationInput.value) * 60 || 25 * 60;
            updateDisplay();
            startBtn.disabled = false;
            pauseBtn.disabled = true;
        }

        // Auto-dismiss pop-up after 3 seconds
        window.onload = function() {
            const popup = document.getElementById('popupAlert');
            if (popup) {
                setTimeout(() => {
                    popup.style.display = 'none';
                }, 3000);
            }
        };

        // Initial display update
        updateDisplay();
    </script>
</body>
</html>