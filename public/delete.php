<?php
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/Empleado.php";

// 1) Preparar modelo.
try {
    $db = (new Database())->getConnection();
} catch (RuntimeException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    die("No se pudo conectar a la base de datos.");
}
$empleadoModel = new Empleado($db);

// 2) Validar ID recibido.
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    header("Location: index.php?type=err&msg=" . urlencode("ID inválido."));
    exit;
}

// 3) Confirmar existencia del registro.
$empleado = $empleadoModel->obtenerPorId($id);
if (!$empleado) {
    header("Location: index.php?type=err&msg=" . urlencode("Empleado no encontrado."));
    exit;
}

// 4) Ejecutar eliminación y redirigir con mensaje.
$ok = $empleadoModel->eliminar($id);
if ($ok) {
    header("Location: index.php?type=ok&msg=" . urlencode("Empleado eliminado correctamente."));
    exit;
}

header("Location: index.php?type=err&msg=" . urlencode("No se pudo eliminar. Intenta de nuevo."));
exit;
