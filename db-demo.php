<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once __DIR__ . '/public/bootstrap.php';
use Pop\Db\Db;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pop PHP - MySQL Demo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #326CE5; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #326CE5; color: white; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; color: #155724; margin: 20px 0; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24; margin: 20px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pop PHP Framework - MySQL Integration</h1>
        <p><a href="/">← Volver al índice</a></p>

        <h2>Ejemplo usando Pop\Db\Db</h2>

        <?php
        try {
            
            $creds = array(
                'database' => 'popphp_db',
                'host'     => 'mysql-service',
                'username' => 'popphp_user',
                'password' => 'popphp_pass'
            );

            echo '<div class="success">';
            echo '<strong>✅ Conectando con Pop\Db\Db...</strong><br>';
            echo 'Host: <code>' . $creds['host'] . '</code><br>';
            echo 'Database: <code>' . $creds['database'] . '</code>';
            echo '</div>';

            
            $db = Db::factory('Mysqli', $creds);

            
            $db->adapter()->query("
                CREATE TABLE IF NOT EXISTS pop_examples (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100),
                    description TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");

            
            $result = $db->adapter()->query("SELECT COUNT(*) as count FROM pop_examples");
            $row = $db->adapter()->fetch();
            
            if ($row['count'] == 0) {
                $db->adapter()->query("INSERT INTO pop_examples (name, description) VALUES 
                    ('Archive', 'Manejo de archivos ZIP y TAR'),
                    ('Auth', 'Sistema de autenticación y ACL'),
                    ('Cache', 'Sistema de cache'),
                    ('Database', 'Conexión y queries a bases de datos'),
                    ('Form', 'Generación y validación de formularios')
                ");
                echo '<p style="color: green;">✅ Datos de ejemplo insertados</p>';
            }

            
            echo '<h3>📊 Registros en la tabla pop_examples:</h3>';
            $db->adapter()->query('SELECT * FROM pop_examples ORDER BY id');

            echo '<table>';
            echo '<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Creado</th></tr>';

            // Fetch resultados
            while (($row = $db->adapter()->fetch()) != false) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                echo '<td><strong>' . htmlspecialchars($row['name']) . '</strong></td>';
                echo '<td>' . htmlspecialchars($row['description']) . '</td>';
                echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
                echo '</tr>';
            }

            echo '</table>';

            echo '<div class="success">';
            echo '<strong>Pop PHP Framework funcionando correctamente con MySQL en Kubernetes!</strong>';
            echo '</div>';

        } catch (\Exception $e) {
            echo '<div class="error">';
            echo '<strong>❌ Error:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        ?>

        <h3>Código usado:</h3>
        <pre style="background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>use Pop\Db\Db;

$creds = array(
    'database' => 'popphp_db',
    'host'     => 'mysql-service',
    'username' => 'popphp_user',
    'password' => 'popphp_pass'
);

$db = Db::factory('Mysqli', $creds);
$db->adapter()->query('SELECT * FROM pop_examples');

while (($row = $db->adapter()->fetch()) != false) {
    print_r($row);
}</code></pre>

    </div>
</body>
</html>

