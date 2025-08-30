<?php
require_once 'TaskModel.php';
require_once 'UserModel.php';

session_start();

class TaskController {
    private $taskModel;
    private $userModel;

    public function __construct($pdo) {
        $this->taskModel = new TaskModel($pdo);
        $this->userModel = new UserModel($pdo);
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $sort = $_GET['sort'] ?? 'created_at';
        $filter = $_GET['filter'] ?? 'all';
        $searchTags = $_GET['search_tags'] ?? '';
        $tasks = $this->taskModel->getAllTasks($sort, $filter, $searchTags);
        require 'views/index.php';
    }

    public function store() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $isRepeatable = isset($_POST['is_repeatable']) ? 1 : 0;
        $repeatCadence = $isRepeatable ? ($_POST['repeat_cadence'] ?? 'once') : 'once';
        $this->taskModel->createTask($_POST['title'], $_POST['comment'], $_POST['deadline'], $_POST['priority'], $_POST['tags'] ?? null, $isRepeatable, $repeatCadence);
        $_SESSION['popup_message'] = 'Task added successfully!';
        header('Location: index.php');
        exit;
    }

    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $isRepeatable = isset($_POST['is_repeatable']) ? 1 : 0;
        $repeatCadence = $isRepeatable ? ($_POST['repeat_cadence'] ?? 'once') : 'once';
        $this->taskModel->editTask($_POST['id'], $_POST['title'], $_POST['comment'], $_POST['deadline'], $_POST['priority'], $_POST['tags'] ?? null, $isRepeatable, $repeatCadence);
        header('Location: index.php');
    }

    public function update($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        error_log("Controller: Updating task with ID: $id");
        $this->taskModel->updateTask($id);
        $_SESSION['popup_message'] = 'Task marked as complete!';
        header('Location: index.php');
        exit;
    }

    public function destroy($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $this->taskModel->deleteTask($id);
        header('Location: index.php');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $user = $this->userModel->getUserByUsername($username);
            if ($user && md5($password) === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                if (isset($_POST['remember_me'])) {
                    // Placeholder for remember me
                }
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid username or password';
                require 'views/login.php';
            }
        } else {
            require 'views/login.php';
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            if ($username && $password) {
                $this->userModel->createUser($username, $password);
                header('Location: index.php?action=login');
                exit;
            } else {
                $error = 'Please fill in all fields';
                require 'views/register.php';
            }
        } else {
            require 'views/register.php';
        }
    }
}
?>