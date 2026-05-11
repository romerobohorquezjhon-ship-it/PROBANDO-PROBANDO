<?php
// ============================================
// CONEXIÓN A BASE DE DATOS
// ============================================

// Configuración de conexión
$host = "localhost";
$user = "root";
$pass = "";  // Sin contraseña en XAMPP por defecto
$database = "fastfood_db";

// Crear conexión
$conn = new mysqli($host, $user, $pass, $database);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Error de conexión: ' . $conn->connect_error
    ]));
}

// Configurar charset UTF-8
$conn->set_charset("utf8");

// Habilitar errores en modo desarrollo
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Función para hashear contraseñas
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Función para verificar contraseñas
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Función para log de auditoría
function logAuditoria($conn, $usuario_id, $accion, $descripcion) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $query = "INSERT INTO auditoria (usuario_id, accion, descripcion, ip_address) 
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("isss", $usuario_id, $accion, $descripcion, $ip);
        $stmt->execute();
        $stmt->close();
    }
}

// Función para responder JSON
function respuesta($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}
?>
