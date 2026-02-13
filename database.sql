CREATE DATABASE IF NOT EXISTS emtelco_crud CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE emtelco_crud;

CREATE TABLE IF NOT EXISTS empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    cargo VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    fecha_ingreso DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- (Opcional) Datos de prueba
INSERT INTO empleados (nombre_completo, cargo, email, fecha_ingreso)
VALUES
('Ana Pérez', 'Analista', 'ana.perez@example.com', '2026-01-10'),
('Carlos Gómez', 'Desarrollador', 'carlos.gomez@example.com', '2026-02-01');
