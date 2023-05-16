<?php
require_once 'RandomUserAPI.php';

$randomUserAPI = new RandomUserAPI();
$users = $randomUserAPI->getUsers();

// Guardar los usuarios en una base de datos (ejemplo básico con MySQL)
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'randomuser';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Error al conectar con la base de datos: ' . $conn->connect_error);
}

// Crear la tabla si no existe
$sql = 'CREATE TABLE IF NOT EXISTS usuarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL
)';

$conn->query($sql);

// Insertar los usuarios en la tabla
foreach ($users as $user) {
    $nombre = $conn->real_escape_string($user['name']['first'] . ' ' . $user['name']['last']);
    $email = $conn->real_escape_string($user['email']);

    $sql = "INSERT INTO usuarios (nombre, email) VALUES ('$nombre', '$email')";
    $conn->query($sql);
}

$conn->close();

// Devolver los usuarios como respuesta JSON
$response = array('users' => $users);

header('Content-Type: application/json');
echo json_encode($response);
