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

// 2) Estado inicial del formulario.
$errores = [];
$nombre = "";
$cargo  = "";
$email  = "";
$fecha  = "";

// 3) Procesar envío POST.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre_completo"] ?? "";
    $cargo  = $_POST["cargo"] ?? "";
    $email  = $_POST["email"] ?? "";
    $fecha  = $_POST["fecha_ingreso"] ?? "";

    $errores = $empleadoModel->validarCampos($nombre, $cargo, $email, $fecha);

    if (count($errores) === 0) {
        $ok = $empleadoModel->crear(trim($nombre), trim($cargo), trim($email), trim($fecha));
        if ($ok) {
            header("Location: index.php?type=ok&msg=" . urlencode("Empleado creado correctamente."));
            exit;
        }
        $errores[] = "No se pudo crear el empleado. Intenta de nuevo.";
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>Crear empleado</title>
  <link rel="stylesheet" href="styles.css?v=<?= ASSET_VERSION ?>">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="titles">
      <h1>Crear empleado</h1>
      <p class="small">Todos los campos son obligatorios.</p>
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

  <!-- Formulario de alta -->
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
      <button type="submit">Guardar</button>
      <a class="button" href="index.php">Volver</a>
    </div>
  </form>
</div>
</body>
</html>
