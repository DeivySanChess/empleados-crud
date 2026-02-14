<?php
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/Empleado.php";

$db = (new Database())->getConnection();
$empleadoModel = new Empleado($db);

$empleados = $empleadoModel->obtenerTodos();

$mensaje = $_GET["msg"] ?? "";
$tipo = $_GET["type"] ?? "";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>CRUD Empleados - EMTELCO</title>
  <link rel="stylesheet" href="styles.css?v=20260214">
</head>
<body>
<div class="container">
  <div class="header">
    <div class="titles">
      <h1>CRUD de Empleados</h1>
      <p class="small">PHP (POO) + MySQL (PDO) • Ordenado por fecha de ingreso (DESC)</p>
    </div>
    <img src="img/emtelco.png" alt="Logo de Emtelco" class="logo" />
  </div>

  <div class="actions">
    <a class="button" href="create.php">+ Crear empleado</a>
  </div>

  <?php if ($mensaje): ?>
    <div class="alert <?= $tipo === "ok" ? "success" : "error" ?>">
      <?= htmlspecialchars($mensaje) ?>
    </div>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>Nombre completo</th>
        <th>Cargo</th>
        <th>Email</th>
        <th>Fecha ingreso</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($empleados) === 0): ?>
        <tr><td colspan="5">No hay empleados registrados.</td></tr>
      <?php else: ?>
        <?php foreach($empleados as $e): ?>
          <tr>
            <td><?= htmlspecialchars($e["nombre_completo"]) ?></td>
            <td><span class="badge"><?= htmlspecialchars($e["cargo"]) ?></span></td>
            <td><?= htmlspecialchars($e["email"]) ?></td>
            <td><?= htmlspecialchars($e["fecha_ingreso"]) ?></td>
            <td class="actions">
              <a class="button" href="edit.php?id=<?= (int)$e["id"] ?>">Editar</a>
              <a class="button" href="delete.php?id=<?= (int)$e["id"] ?>" onclick="return confirm('¿Eliminar este empleado?');">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
