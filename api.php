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
            $sql = "SELECT id, ST_AsText(coordinates) AS coordinates, altitude, name, description, user_id, timestamp
                    FROM locations";
            $stmt = $pdo->query($sql);
            $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode($locations);
        }
    } catch (PDOException $e) {
        // Manejar errores de la base de datos
        http_response_code(500);
        echo json_encode(['error' => 'Error al recuperar las ubicaciones: ' . $e->getMessage()]);
    }
}








function handlePost($pdo, $input) {
    // Validación básica de los datos de entrada
    if (!isset($input['latitude']) || !isset($input['longitude'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan campos obligatorios: latitude o longitude']);
        return;
    }

    try {
        // Consulta SQL para insertar una nueva ubicación
        $sql = "INSERT INTO locations (coordinates, altitude, name, description, user_id)
                VALUES (POINT(:latitude, :longitude), :altitude, :name, :description, :user_id)";
        $stmt = $pdo->prepare($sql);

        // Ejecutar la consulta con parámetros de entrada
        $stmt->execute([
            'latitude' => $input['latitude'],
            'longitude' => $input['longitude'],
            'altitude' => $input['altitude'] ?? null,
            'name' => $input['name'] ?? null,
            'description' => $input['description'] ?? null,
            'user_id' => $input['user_id'] ?? null
        ]);

        // Devolver una respuesta JSON de éxito
        http_response_code(201);
        echo json_encode(['message' => 'Location created successfully']);
    } catch (PDOException $e) {
        // Manejar errores de la base de datos
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear la ubicación: ' . $e->getMessage()]);
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
