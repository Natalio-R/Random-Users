# Introducción

Esto es una prueba básica donde se muestra una lista de usuarios random. Los datos son godiso de la API [RandomUser.me](https://randomuser.me/).

Usando PHP para hacer la petición a la API, recoger los datos en formatos JSON y mostrando la lista mediante HTML usando JavaScript, además, se gaurdan dichos usuarios en una pequeña base de datos.

# Explicación de código

A continuación se explica brevemente que hace cada archivo y como se ha desarrollado esta pequeña prueba.


## HTML(index.html)

```html
<div class="list">
  <h1>Lista de Usuarios</h1>
  <ul id="user-list"></ul>
</div>
```

En este código veremos un `<div>` principal con la clase list para poder aplicarle unos estilos en su _CSS_ correspondiente.

## Javascript

```javascript
$(document).ready(function () {
  $.ajax({
    url: "get_users.php",
    type: "GET",
    dataType: "json",
    success: function (response) {
      var users = response.users;

      // Mostrar los usuarios en el HTML
      var userList = $("#user-list");
      for (var i = 0; i < users.length; i++) {
        var user = users[i];
        var fullName = user.name.first + " " + user.name.last;
        var email = user.email;
        var listItem = $("<li>").text(fullName + " - " + email);
        userList.append(listItem);
      }
    },
    error: function (xhr, status, error) {
      console.log(error);
    },
  });
});
```

## PHP(get_users.php)

```php
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
```

En este archivo `php` tenemos la conexión con la base de datos, la creación de la tabla donde se almacenarán los usuarios junto con el insert correspondiente para el insertado de dichos suarios, y por último la respuesta que es una array de usuarios.

## PHP(RandomUsersAPI.php)

```php
class RandomUserAPI
{
	private $apiUrl = 'https://randomuser.me/api/?results=10';

	public function getUsers()
	{
		$curl = curl_init($this->apiUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);

		return json_decode($response, true)['results'];
	}
}
```

Aquí simplemente tenemos la conexión _CURL_ con la API. Y la recogida de datos. Usando una clase de `PHP`.
