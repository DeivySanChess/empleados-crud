<?php
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/Empleado.php";

$db = (new Database())->getConnection();
$empleadoModel = new Empleado($db);

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    header("Location: index.php?type=err&msg=" . urlencode("ID inválido."));
    exit;
}

$empleado = $empleadoModel->obtenerPorId($id);
if (!$empleado) {
    header("Location: index.php?type=err&msg=" . urlencode("Empleado no encontrado."));
    exit;
}

$ok = $empleadoModel->eliminar($id);
if ($ok) {
    header("Location: index.php?type=ok&msg=" . urlencode("Empleado eliminado correctamente."));
    exit;
}

header("Location: index.php?type=err&msg=" . urlencode("No se pudo eliminar. Intenta de nuevo."));
exit;
