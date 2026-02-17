<?php
/**
 * Gestor de conexión PDO a la base de datos de empleados.
 * Centraliza la configuración y devuelve una conexión lista para usar.
 */
class Database {
    /** Host del servidor MySQL. */
    private string $host = "localhost";
    /** Nombre de la base de datos. */
    private string $db_name = "emtelco_crud";
    /** Usuario con permisos sobre la base. */
    private string $username = "root";
    /** Contraseña del usuario. */
    private string $password = "";
    /** Conexión activa o null si aún no se ha creado. */
    public ?PDO $conn = null;

    /**
     * Crea y devuelve una conexión PDO configurada.
     *
     * @return PDO|null Conexión lista para usar o null si falló.
     * @throws RuntimeException cuando no es posible conectarse.
     */
    public function getConnection(): ?PDO {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            // Desactiva la emulación para que las consultas preparadas sean nativas.
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->ensureSchema();
        } catch (PDOException $e) {
            // Permitimos que el controlador superior decida cómo manejar el error.
            throw new RuntimeException("Error de conexión a la base de datos", 0, $e);
        }
        return $this->conn;
    }

    /** Asegura que la tabla tenga la columna contacto (idempotente). */
    private function ensureSchema(): void {
        try {
            $stmt = $this->conn->query(
                "SELECT COUNT(*) FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'empleados'
                   AND COLUMN_NAME = 'contacto'"
            );
            $hasColumn = (int)$stmt->fetchColumn() > 0;
            if (!$hasColumn) {
                $this->conn->exec(
                    "ALTER TABLE empleados ADD COLUMN contacto VARCHAR(20) NULL AFTER nombre_completo"
                );
            }
        } catch (PDOException $e) {
            // No interrumpir el flujo de la app por un ajuste de esquema.
            error_log("No se pudo verificar/ajustar el esquema: " . $e->getMessage());
        }
    }
}
