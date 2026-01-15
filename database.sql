CREATE TABLE alarms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(100) NOT NULL,
    usuario VARCHAR(100) NOT NULL,
    ciudad VARCHAR(100) NOT NULL,
    direccion_casa VARCHAR(255) NOT NULL,
    nombre_alarma VARCHAR(100) NOT NULL,
    ubicacion_alarma VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
