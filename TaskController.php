<?php
require_once 'TaskModel.php';

class TaskController {
    private $model;

    public function __construct($pdo) {
        $this->model = new TaskModel($pdo);
    }

    public function index() {
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        $tasks = $this->model->getAllTasks($sort, $filter);
        require 'views/index.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title'])) {
            $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
            $this->model->createTask($_POST['title'], $_POST['comment'], $deadline);
        }
        header('Location: index.php');
        exit;
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title'])) {
            $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
            $this->model->editTask($id, $_POST['title'], $_POST['comment'], $deadline);
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