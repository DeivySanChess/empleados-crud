# Prueba Técnica 1 - CRUD Empleados (PHP + MySQL)

## Objetivo
Aplicación CRUD básica para gestión de empleados.

Cada empleado tiene:
- Nombre completo
- Cargo
- Correo electrónico
- Fecha de ingreso

## Requisitos técnicos
- PHP (orientado a objetos, sin framework)
- MySQL (estructura incluida en `database.sql`)
- Conexión PDO
- Validación en servidor y seguridad mínima (prepared statements)
- Listado en tabla ordenado por fecha de ingreso (DESC)

## Cómo ejecutar (XAMPP / Windows)
1. Abrir XAMPP Control Panel y encender:
   - Apache
   - MySQL
2. Importar `database.sql` en phpMyAdmin:
   - http://localhost/phpmyadmin
3. Copiar la carpeta del proyecto a:
   - C:\xampp\htdocs\empleados-crud
4. Abrir en el navegador:
   - http://localhost/empleados-crud/public/index.php

## Estructura
- `config/Database.php`: conexión a BD
- `models/Empleado.php`: lógica CRUD (POO)
- `public/`: vistas (index, create, edit, delete)
