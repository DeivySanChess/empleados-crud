<?php
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/Empleado.php";

const ASSET_VERSION = "20260216";

// 1) Preparar modelo.
try {
    $db = (new Database())->getConnection();
} catch (RuntimeException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    die("No se pudo conectar a la base de datos.");
}
$empleadoModel = new Empleado($db);

// 2) Validar y obtener ID.
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    header("Location: index.php?type=err&msg=" . urlencode("ID inválido."));
    exit;
}

// 3) Cargar empleado existente.
$empleado = $empleadoModel->obtenerPorId($id);
if (!$empleado) {
    header("Location: index.php?type=err&msg=" . urlencode("Empleado no encontrado."));
    exit;
}

// 4) Estado inicial del formulario.
$errores = [];
$nombre = $empleado["nombre_completo"];
$cargo  = $empleado["cargo"];
$email  = $empleado["email"];
$fecha  = $empleado["fecha_ingreso"];

// 5) Procesar envío POST.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre_completo"] ?? "";
    $cargo  = $_POST["cargo"] ?? "";
    $email  = $_POST["email"] ?? "";
    $fecha  = $_POST["fecha_ingreso"] ?? "";

    $errores = $empleadoModel->validarCampos($nombre, $cargo, $email, $fecha);

    if (count($errores) === 0) {
        $ok = $empleadoModel->actualizar($id, trim($nombre), trim($cargo), trim($email), trim($fecha));
        if ($ok) {
            header("Location: index.php?type=ok&msg=" . urlencode("Empleado actualizado correctamente."));
            exit;
        }
        $errores[] = "No se pudo actualizar el empleado. Intenta de nuevo.";
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>Editar empleado</title>
  <link rel="stylesheet" href="styles.css?v=<?= ASSET_VERSION ?>">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="titles">
      <h1>Editar empleado</h1>
      <p class="small"><?= htmlspecialchars($empleado["nombre_completo"]) ?></p>
    </div>
    <img src="img/emtelco.png" alt="Logo de Emtelco" class="logo" />
  </div>

  <?php if (count($errores) > 0): ?>
    <div class="alert error">
      <ul>
        <?php foreach($errores as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Formulario de edición -->
  <form method="POST" novalidate>
    <div class="form-row">
      <label>Nombre completo</label>
      <input type="text" name="nombre_completo" value="<?= htmlspecialchars($nombre) ?>" required>
    </div>

    <div class="form-row">
      <label>Cargo</label>
      <input type="text" name="cargo" value="<?= htmlspecialchars($cargo) ?>" required>
    </div>

    <div class="form-row">
      <label>Correo electrónico</label>
      <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
    </div>

    <div class="form-row">
      <label>Fecha de ingreso</label>
      <input type="date" name="fecha_ingreso" value="<?= htmlspecialchars($fecha) ?>" required>
    </div>

    <div class="actions">
      <button type="submit">Actualizar</button>
      <a class="button" href="index.php">Volver</a>
    </div>
  </form>
</div>
</body>
</html>
