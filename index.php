<?php
// Configuración de conexión a MySQL
$host = getenv('MYSQL_HOST') ?: 'mysql-service';
$dbname = getenv('MYSQL_DATABASE') ?: 'popphp_db';
$user = getenv('MYSQL_USER') ?: 'popphp_user';
$password = getenv('MYSQL_PASSWORD') ?: 'popphp_pass';

echo "<h1>🚀 Pop PHP Legacy en Kubernetes</h1>";
echo "<h2>Estado de la aplicación</h2>";

// Información del servidor
echo "<p><strong>Hostname:</strong> " . gethostname() . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Intentar conexión a MySQL
echo "<h2>🗄️ Conexión a MySQL</h2>";

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p style='color: green;'>✅ <strong>Conexión exitosa a MySQL</strong></p>";
    echo "<p><strong>Host:</strong> $host</p>";
    echo "<p><strong>Database:</strong> $dbname</p>";
    
    // Crear tabla si no existe
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS visits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            hostname VARCHAR(255)
        )
    ");
    
    // Registrar visita
    $stmt = $pdo->prepare("INSERT INTO visits (hostname) VALUES (?)");
    $stmt->execute([gethostname()]);
    
    // Mostrar estadísticas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM visits");
    $total = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("
        SELECT hostname, COUNT(*) as count 
        FROM visits 
        GROUP BY hostname 
        ORDER BY count DESC
    ");
    $stats = $stmt->fetchAll();
    
    echo "<h3>📊 Estadísticas de visitas</h3>";
    echo "<p><strong>Total de visitas:</strong> $total</p>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Pod (Hostname)</th><th>Visitas</th></tr>";
    foreach ($stats as $row) {
        echo "<tr><td>{$row['hostname']}</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ <strong>Error de conexión:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><em>Pop PHP Framework v1 (2016) - Migrado a Kubernetes por Luciana Cristaldo (2025)</em></p>";
?>
