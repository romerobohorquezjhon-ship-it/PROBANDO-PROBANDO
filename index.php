<?php
// ============================================
// LOGIN DE EMPLEADO
// ============================================

header('Content-Type: application/json');
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuesta(false, 'Método no permitido');
}

// Recibir datos
$nombre = trim($_POST['nombre'] ?? '');
$password = $_POST['password'] ?? '';

// Validar campos
if (empty($nombre) || empty($password)) {
    respuesta(false, 'Completa usuario y contraseña');
}

// Buscar usuario
$query = "SELECT id, nombre, apellidos, correo, rol, password FROM empleados WHERE nombre = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nombre);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    respuesta(false, 'Usuario o contraseña incorrectos');
}

$usuario = $result->fetch_assoc();
$stmt->close();

// Verificar contraseña
if (!verifyPassword($password, $usuario['password'])) {
    respuesta(false, 'Usuario o contraseña incorrectos');
}

// Actualizar último acceso
$query = "UPDATE empleados SET ultimoAcceso = NOW() WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $usuario['id']);
$stmt->execute();
$stmt->close();

// Registrar acceso
logAuditoria($conn, $usuario['id'], 'LOGIN', 'Acceso al sistema');

// Responder con datos del usuario
respuesta(true, 'Login exitoso', [
    'id' => $usuario['id'],
    'nombre' => $usuario['nombre'],
    'correo' => $usuario['correo'],
    'rol' => $usuario['rol']
]);

$conn->close();
?>
