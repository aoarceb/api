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



function handleGet($pdo, $params) {
    try {
        if (isset($params['id'])) {
            // Si se proporciona un ID, recuperar una ubicación específica
            $sql = "SELECT id, ST_AsText(coordinates) AS coordinates, altitude, name, description, user_id, timestamp
                    FROM locations
                    WHERE id = :id";

            $sql = "SELECT alarma_id, tipo_alarma, mensaje, fecha_evento, estado, ubicacion FROM eventos_alarmas";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $params['id']]);
            $location = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($location) {
                http_response_code(200);
                echo json_encode($location);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Ubicación no encontrada']);
            }
        } else {
            // Si no se proporciona un ID, recuperar todas las ubicaciones

            $sql = "SELECT alarma_id, tipo_alarma, mensaje, fecha_evento, estado, ubicacion, creado_en FROM eventos_alarmas";

            $stmt = $pdo->query($sql);
            $alarmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode($alarmas);
        }
    } catch (PDOException $e) {
        // Manejar errores de la base de datos
        http_response_code(500);
        echo json_encode(['error' => 'Error al recuperar las ubicaciones: ' . $e->getMessage()]);
    }
}




function handlePost($pdo, $input) {
        // api.php
        header('Content-Type: application/json');

        // Incluir la configuración de la base de datos
        require 'db.php';

        // Verificar el método de la solicitud
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Leer el cuerpo de la solicitud
                $input = json_decode(file_get_contents('php://input'), true);

                // Validar los datos requeridos
                if (isset($input['alarma_id'], $input['tipo_alarma'], $input['mensaje'], $input['fecha_evento'], $input['estado'], $input['ubicacion'])) {
        $alarma_id = $input['alarma_id'];
        $tipo_alarma = $input['tipo_alarma'];
        $mensaje = $input['mensaje'];
        $fecha_evento = $input['fecha_evento'];
        $estado = $input['estado'];
        $ubicacion = $input['ubicacion'];

        try {
            // Insertar el evento en la base de datos
            $stmt = $pdo->prepare("INSERT INTO eventos_alarmas (alarma_id, tipo_alarma, mensaje, fecha_evento, estado, ubicacion)
                                   VALUES (:alarma_id, :tipo_alarma, :mensaje, :fecha_evento, :estado, :ubicacion)");
            $stmt->execute([
                ':alarma_id' => $alarma_id,
                ':tipo_alarma' => $tipo_alarma,
                ':mensaje' => $mensaje,
                ':fecha_evento' => $fecha_evento,
                ':estado' => $estado,
                ':ubicacion' => $ubicacion
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Evento registrado correctamente']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al registrar el evento: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}

}




function handlePut($pdo, $input) {
    $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['name' => $input['name'], 'email' => $input['email'], 'id' => $input['id']]);
    echo json_encode(['message' => 'User updated successfully']);
}

function handleDelete($pdo, $input) {
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $input['id']]);
    echo json_encode(['message' => 'User deleted successfully']);
}
?>
