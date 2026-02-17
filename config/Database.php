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
                "SELECT COLUMN_NAME, IS_NULLABLE
                   FROM information_schema.COLUMNS
                  WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'empleados'
                    AND LOWER(COLUMN_NAME) = 'contacto'
                  LIMIT 1"
            );
            $col = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$col) {
                // No existe la columna, se crea en minúsculas y nullable.
                $this->conn->exec(
                    "ALTER TABLE empleados ADD COLUMN contacto VARCHAR(20) NULL AFTER nombre_completo"
                );
            } else {
                // Existe pero podría ser NOT NULL; si es así, la hacemos nullable.
                if (strcasecmp($col['IS_NULLABLE'] ?? '', 'YES') !== 0) {
                    $colName = $col['COLUMN_NAME']; // preserva el nombre real (Contacto/contacto)
                    $this->conn->exec(
                        "ALTER TABLE empleados MODIFY COLUMN `{$colName}` VARCHAR(20) NULL"
                    );
                }
            }
        } catch (PDOException $e) {
            // No interrumpir el flujo de la app por un ajuste de esquema.
            error_log("No se pudo verificar/ajustar el esquema: " . $e->getMessage());
        }
    }
}
