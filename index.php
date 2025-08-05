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