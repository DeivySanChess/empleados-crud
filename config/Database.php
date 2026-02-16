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
        } catch (PDOException $e) {
            // Permitimos que el controlador superior decida cómo manejar el error.
            throw new RuntimeException("Error de conexión a la base de datos", 0, $e);
        }
        return $this->conn;
    }
}
