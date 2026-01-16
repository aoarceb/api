<?php
header("Content-Type: application/json");
include 'db.php';
$params = $_GET;

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        handleGet($pdo, $params);
        break;
    case 'POST':
        handlePost($pdo, $input);
        break;
    case 'PUT':
        handlePut($pdo, $input);
        break;
    case 'DELETE':
        handleDelete($pdo, $input);
        break;
    default:
        echo json_encode(['message' => 'Invalid request method']);
        break;
}




function handlePost($pdo, $input) {
    // Validación básica de los datos de entrada
    if (!isset($input['usuario']) ) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan campos obligatorios: usuario']);
        return;
    }

    try {
        // Consulta SQL para insertar una nueva ubicación
        
      $sql = "
        INSERT INTO alarms
        (usuario, nombre_alarma, ubicacion_alarma, estado, fecha_creacion)
        VALUES
        (:usuario,  :nombre_alarma, :ubicacion_alarma, :estado, NOW())
        ";
      
      $stmt = $pdo->prepare($sql);

        // Ejecutar la consulta con parámetros de entrada
        $stmt->execute([
        ":usuario" => $input["usuario"],
        ":nombre_alarma" => $input["nombre_alarma"],
        ":ubicacion_alarma" => $input["ubicacion_alarma"],
        ":estado" => $input["estado"]
       ]);
        // Devolver una respuesta JSON de éxito
        http_response_code(201);
        echo json_encode(['message' => 'Alarma creada eb BD exitosamente']);
    } catch (PDOException $e) {
        // Manejar errores de la base de datos
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear la alarma: ' . $e->getMessage()]);
    }
}









// Solo permitir POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Leer JSON del body
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(["error" => "JSON inválido"]);
    exit;
}

// Validar campos mínimos
$required = ["usuario", "ciudad", "direccion", "nombre_alarma", "ubicacion_alarma", "estado"];

foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(["error" => "Falta el campo: $field"]);
        exit;
    }
}

// Conexión a la BD
require_once "db.php"; // $pdo

$sql = "
    INSERT INTO alarm_events
    (usuario, ciudad, direccion, nombre_alarma, ubicacion_alarma, estado, fecha_creacion)
    VALUES
    (:usuario, :ciudad, :direccion, :nombre_alarma, :ubicacion_alarma, :estado, NOW())
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":usuario" => $input["usuario"],
    ":ciudad" => $input["ciudad"],
    ":direccion" => $input["direccion"],
    ":nombre_alarma" => $input["nombre_alarma"],
    ":ubicacion_alarma" => $input["ubicacion_alarma"],
    ":estado" => $input["estado"]
]);

echo json_encode([
    "status" => "ok",
    "message" => "Evento de alarma guardado"
]);
