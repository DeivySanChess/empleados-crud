<?php
/**
 * Modelo de Empleado: centraliza validaciones y operaciones CRUD.
 */
class Empleado {
    /** Conexión PDO inyectada al construir la clase. */
    private PDO $conn;
    /** Nombre de la tabla utilizada. */
    private string $table = "empleados";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Valida los campos del formulario y devuelve un arreglo de mensajes.
     * Se comparte entre creación y edición para evitar duplicar reglas.
     */
    public function validarCampos(string $nombre, string $cargo, string $email, string $fecha): array {
        $errores = [];

        // Normalización básica: recortar espacios.
        $nombre = trim($nombre);
        $cargo  = trim($cargo);
        $email  = trim($email);
        $fecha  = trim($fecha);

        // Reglas obligatorias.
        if ($nombre === "") $errores[] = "El nombre completo es obligatorio.";
        if ($cargo === "")  $errores[] = "El cargo es obligatorio.";
        if ($email === "")  $errores[] = "El correo es obligatorio.";
        if ($fecha === "")  $errores[] = "La fecha de ingreso es obligatoria.";

        // Formato de correo.
        if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo no tiene un formato válido.";
        }

        // Formato de fecha YYYY-MM-DD.
        if ($fecha !== "") {
            $dt = DateTime::createFromFormat('Y-m-d', $fecha);
            $isValid = $dt && $dt->format('Y-m-d') === $fecha;
            if (!$isValid) $errores[] = "La fecha de ingreso no es válida (formato YYYY-MM-DD).";
        }

        // Límites de longitud para evitar datos erróneos o truncados.
        if (mb_strlen($nombre) > 100) $errores[] = "El nombre supera 100 caracteres.";
        if (mb_strlen($cargo) > 50)   $errores[] = "El cargo supera 50 caracteres.";
        if (mb_strlen($email) > 100)  $errores[] = "El correo supera 100 caracteres.";

        return $errores;
    }

    /** Obtiene todos los empleados ordenados por fecha de ingreso descendente. */
    public function obtenerTodos(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY fecha_ingreso DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Devuelve un empleado por ID o null si no existe. */
    public function obtenerPorId(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Crea un nuevo empleado. */
    public function crear(string $nombre, string $cargo, string $email, string $fecha): bool {
        $sql = "INSERT INTO {$this->table} (nombre_completo, cargo, email, fecha_ingreso)
                VALUES (:nombre, :cargo, :email, :fecha)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":nombre" => $nombre,
            ":cargo"  => $cargo,
            ":email"  => $email,
            ":fecha"  => $fecha
        ]);
    }

    /** Actualiza un registro existente. */
    public function actualizar(int $id, string $nombre, string $cargo, string $email, string $fecha): bool {
        $sql = "UPDATE {$this->table}
                SET nombre_completo = :nombre, cargo = :cargo, email = :email, fecha_ingreso = :fecha
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":id"     => $id,
            ":nombre" => $nombre,
            ":cargo"  => $cargo,
            ":email"  => $email,
            ":fecha"  => $fecha
        ]);
    }

    /** Elimina un empleado por ID. */
    public function eliminar(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}
