1. **Database Connection: `db.php`**:
   - Create `db.php`.
   - Paste:
     ```php
     <?php
     $host = 'localhost';
     $dbname = 'lisify_db';
     $username = 'root';
     $password = '';

     try {
         $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     } catch (PDOException $e) {
         die("Connection failed: " . $e->getMessage());
     }
     ?>
     ```

2. **Model: `TaskModel.php`**:
   - Create `TaskModel.php`.
   - Paste:
     ```php
     <?php
     require_once 'db.php';

     class TaskModel {
         private $pdo;

         public function __construct($pdo) {
             $this->pdo = $pdo;
         }

         public function getAllTasks() {
             $stmt = $this->pdo->query('SELECT * FROM tasks');
             return $stmt->fetchAll(PDO::FETCH_ASSOC);
         }

         public function createTask($title, $comment) {
             $stmt = $this->pdo->prepare('INSERT INTO tasks (title, comment, is_completed) VALUES (?, ?, 0)');
             $stmt->execute([$title, $comment]);
         }

         public function updateTask($id) {
             $stmt = $this->pdo->prepare('UPDATE tasks SET is_completed = NOT is_completed WHERE id = ?');
             $stmt->execute([$id]);
         }

         public function deleteTask($id) {
             $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
             $stmt->execute([$id]);
         }
     }
     ?>
     ```

3. **Controller: `TaskController.php`**:
   - Create `TaskController.php`.
   - Paste:
     ```php
     <?php
     require_once 'TaskModel.php';

     class TaskController {
         private $model;

         public function __construct($pdo) {
             $this->model = new TaskModel($pdo);
         }

         public function index() {
             $tasks = $this->model->getAllTasks();
             require 'views/index.php';
         }

         public function store() {
             if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title'])) {
                 $this->model->createTask($_POST['title'], $_POST['comment']);
             }
             header('Location: index.php');
             exit;
         }

         public function update($id) {
             $this->model->updateTask($id);
             header('Location: index.php');
             exit;
         }

         public function destroy($id) {
             $this->model->deleteTask($id);
             header('Location: index.php');
             exit;
         }
     }
     ?>
     ```

4. **View: `views/index.php`**:
   - Create a `views` folder inside `lisify`.
   - Create `index.php` inside `views`.
   - Paste:
     ```php
     <!DOCTYPE html>
     <html lang="en">
     <head>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <title>Lisify - ToDo List</title>
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
             <h1 class="text-center mb-4">Lisify</h1>
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
     ```

5. **Main Entry: `index.php`**:
   - Create `index.php` in the `lisify` folder.
   - Paste:
     ```php
     <?php
     require_once 'db.php';
     require_once 'TaskController.php';

     $controller = new TaskController($pdo);

     $action = isset($_GET['action']) ? $_GET['action'] : 'index';
     $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

     if ($action === 'store') {
         $controller->store();
     } elseif ($action === 'update' && $id) {
         $controller->update($id);
     } elseif ($action === 'destroy' && $id) {
         $controller->destroy($id);
     } else {
         $controller->index();
     }
     ?>
     ```

#### Step 5: Run the App
1. **Ensure XAMPP is Running**:
   - Open XAMPP Control Panel.
   - Start Apache and MySQL (green lights).

2. **Open in Browser**:
   - Go to `http://localhost/lisify` in Chrome or Firefox.
   - You’ll see the Lisify app with a form and task list.

3. **Test the App**:
   - **Add Task**: Type a title (e.g., “Study”) and comment (e.g., “Math homework”), click “Add Task”.
   - **Mark Complete**: Check the box (title gets strikethrough).
   - **Comments**: Shown below tasks.
   - **Storage**: Refresh; tasks stay in MySQL.
   - **Delete**: Click “Delete” to remove a task.

#### Step 6: Check It Works
- The app has a clean form and task cards.
- It works on phones (Bootstrap adjusts layout).
- No Tailwind warnings (uses Bootstrap).
- No Composer needed.

---

### Fallback: HTML/JavaScript (Completely Terminal-Free, No PHP)
If PHP setup is too complex or you want a terminal-free option, use the HTML/JavaScript solution (version_id: a0aacf51-e301-4f3b-80ea-b181be6be1ab). It doesn’t use PHP or MVC, so confirm with your teacher.

1. **Copy Code**:
   - Create a file in Notepad or VS Code.
   - Paste:
     ```html
     <!DOCTYPE html>
     <html lang="en">
     <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Lisify - ToDo List</title>
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
         <h1 class="text-center mb-4">Lisify</h1>
         <div class="card p-3 mb-4">
           <h2 class="h5 mb-3">Add New Task</h2>
           <input type="text" id="taskTitle" class="form-control mb-2" placeholder="Task title">
           <textarea id="taskComment" class="form-control mb-2" placeholder="Comment" rows="3"></textarea>
           <button onclick="addTask()" class="btn btn-primary w-100">Add Task</button>
         </div>
         <div id="taskList"></div>
       </div>
       <script>
         let tasks = JSON.parse(localStorage.getItem('lisifyTasks')) || [];
         function displayTasks() {
           const taskList = document.getElementById('taskList');
           taskList.innerHTML = '';
           if (tasks.length === 0) {
             taskList.innerHTML = '<p class="text-center text-muted">No tasks yet. Add one above!</p>';
           } else {
             tasks.forEach(task => {
               const taskCard = document.createElement('div');
               taskCard.className = 'card task-card p-3';
               taskCard.innerHTML = `
                 <div class="d-flex align-items-center justify-content-between">
                   <div class="d-flex align-items-center">
                     <input type="checkbox" ${task.isCompleted ? 'checked' : ''} 
                            onchange="toggleComplete(${task.id})" class="form-check-input me-2">
                     <div>
                       <h3 class="${task.isCompleted ? 'task-title completed' : 'task-title'}">
                         ${task.title}
                       </h3>
                       <p class="text-muted mb-0">${task.comment || 'No comment'}</p>
                     </div>
                   </div>
                   <button onclick="deleteTask(${task.id})" class="btn btn-danger btn-sm">Delete</button>
                 </div>
               `;
               taskList.appendChild(taskCard);
             });
           }
         }
         function addTask() {
           const title = document.getElementById('taskTitle').value;
           const comment = document.getElementById('taskComment').value;
           if (!title.trim()) return;
           tasks.push({ id: Date.now(), title: title, comment: comment, isCompleted: false });
           localStorage.setItem('lisifyTasks', JSON.stringify(tasks));
           document.getElementById('taskTitle').value = '';
           document.getElementById('taskComment').value = '';
           displayTasks();
         }
         function toggleComplete(id) {
           tasks = tasks.map(task =>
             task.id === id ? { ...task, isCompleted: !task.isCompleted } : task
           );
           localStorage.setItem('lisifyTasks', JSON.stringify(tasks));
           displayTasks();
         }
         function deleteTask(id) {
           tasks = tasks.filter(task => task.id !== id);
           localStorage.setItem('lisifyTasks', JSON.stringify(tasks));
           displayTasks();
         }
         displayTasks();
       </script>
     </body>
     </html>
     ```

2. **Save and Run**:
   - Save as `lisify.html` (File > Save As, set “All Files”).
   - Double-click to open in Chrome.
   - Test adding, completing, and deleting tasks.

---

### Recommendation
- **Try Plain PHP First**: This uses PHP and MVC, aligns with your teacher’s preference, and avoids Composer. It’s simple with XAMPP and MySQL, requiring only file creation and phpMyAdmin.
- **HTML Fallback**: If PHP setup is too hard, use the HTML/JavaScript solution. It’s completely terminal-free but doesn’t use PHP or MVC, so check with your teacher.

### Why PHP Meets Sprint 1
- **Add Tasks**: Form for title and comment.
- **Mark Complete**: Checkbox with strikethrough.
- **Storage**: MySQL database.
- **Comments**: Saved and shown.
- **SRS**: Responsive (Bootstrap), fast (plain PHP), intuitive UI.
- **MVC**: Model (TaskModel), View (index.php), Controller (TaskController).

### Troubleshooting
- **Page Not Loading**: Ensure XAMPP’s Apache/MySQL are running. Try `http://localhost/lisify`.
- **Database Error**: Verify `lisify_db` and table in phpMyAdmin.
- **No Tasks Saved**: Check `db.php` connection settings.
- **Share Issues**: Tell me the step or error (e.g., browser error via F12).

If you want the HTML solution, simpler PHP steps, or help with a specific issue, let me know, and I’ll make it even easier!