<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once __DIR__ . '/public/bootstrap.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pop PHP Framework - Migración a Kubernetes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #326CE5; border-bottom: 3px solid #326CE5; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        .examples { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        .example { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #326CE5; }
        .example a { text-decoration: none; color: #326CE5; font-weight: bold; }
        .example a:hover { color: #1a4d99; }
        .status { background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #28a745; }
        .status h3 { margin: 0 0 10px 0; color: #155724; }
        .info { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pop PHP Framework v1 - Kubernetes Migration</h1>
        
        <div class="status">
            <h3>✅ Sistema Operacional</h3>
            <p><strong>Hostname:</strong> <?php echo gethostname(); ?></p>
            <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
            <p><strong>Server Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>

        <div class="info">
            <h3>📊 Conexión a MySQL</h3>
            <?php
            try {
                $pdo = new PDO(
                    'mysql:host=mysql-service;dbname=popphp_db;charset=utf8mb4',
                    'popphp_user',
                    'popphp_pass',
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                echo '<p style="color: green;">✅ <strong>MySQL conectado exitosamente</strong></p>';
                
                // Crear tabla demo si no existe
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS framework_visits (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        hostname VARCHAR(255),
                        page VARCHAR(255)
                    )
                ");
                
                // Registrar visita
                $stmt = $pdo->prepare("INSERT INTO framework_visits (hostname, page) VALUES (?, ?)");
                $stmt->execute([gethostname(), 'index']);
                
                // Stats
                $total = $pdo->query("SELECT COUNT(*) FROM framework_visits")->fetchColumn();
                echo "<p><strong>Total de visitas al framework:</strong> $total</p>";
                
            } catch (PDOException $e) {
                echo '<p style="color: red;">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            ?>
        </div>

        <h2>Ejemplos Disponibles del Framework</h2>
        <p>Explora los ejemplos del Pop PHP Framework. Algunos pueden requerir configuración adicional.</p>
        
        <div class="examples">
            <?php
            $examplesDir = __DIR__ . '/public/examples';
            $examples = array_diff(scandir($examplesDir), array('.', '..', 'assets', 'tmp'));
            sort($examples);
            
            foreach ($examples as $example) {
                if (is_dir($examplesDir . '/' . $example)) {
                    $files = array_diff(scandir($examplesDir . '/' . $example), array('.', '..'));
                    $fileCount = count($files);
                    
                    echo '<div class="example">';
                    echo '<a href="/public/examples/' . $example . '/">' . ucfirst($example) . '</a>';
                    echo '<br><small>' . $fileCount . ' ejemplo(s)</small>';
                    echo '</div>';
                }
            }
            ?>
        </div>

        <h2>Ejemplo de Base de Datos</h2>
        <p><a href="/db-demo.php" style="color: #326CE5; font-weight: bold;">Ver ejemplo de conexión a MySQL con Pop PHP →</a></p>

        <hr style="margin: 40px 0;">
        <p style="text-align: center; color: #666;">
            <em>Pop PHP Framework v1 (2016) - Migrado a Kubernetes por Luciana Cristaldo (2025)</em>
        </p>
    </div>
</body>
</html>

