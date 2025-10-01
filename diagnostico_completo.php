<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico Completo - Sistema de Documentos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section {
            margin-bottom: 30px;
            padding: 15px;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        h2 {
            color: #666;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-danger {
            background: #dc3545;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 DIAGNÓSTICO COMPLETO DEL SISTEMA</h1>

        <?php
        echo "<div class='section info'>";
        echo "<h2>📋 Información del Servidor</h2>";
        echo "<table>";
        echo "<tr><th>Parámetro</th><th>Valor</th></tr>";
        echo "<tr><td>Servidor Web</td><td>" . $_SERVER['SERVER_SOFTWARE'] . "</td></tr>";
        echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
        echo "<tr><td>Directorio Actual</td><td>" . __DIR__ . "</td></tr>";
        echo "<tr><td>Fecha/Hora</td><td>" . date('Y-m-d H:i:s') . "</td></tr>";
        echo "</table>";
        echo "</div>";

        // Verificar archivos críticos
        echo "<div class='section'>";
        echo "<h2>📁 Verificación de Archivos Críticos</h2>";
        $archivos_criticos = [
            'index.php' => 'Archivo raíz',
            'public/index.php' => 'Punto de entrada',
            '.htaccess' => 'Configuración URLs',
            'config/database.php' => 'Configuración BD',
            'config/routes.php' => 'Sistema de rutas',
            'app/controllers/DocumentController.php' => 'Controlador principal',
            'app/views/documents/index.php' => 'Vista del panel',
            'app/views/documents/show.php' => 'Vista de documentos',
            'app/views/documents/success.php' => 'Vista de éxito',
            'app/views/documents/layouts/orden-compra.php' => 'Formulario orden compra',
            'database/schema_sist.sql' => 'Esquema de BD'
        ];

        echo "<table>";
        echo "<tr><th>Archivo</th><th>Estado</th><th>Descripción</th></tr>";
        foreach ($archivos_criticos as $archivo => $descripcion) {
            $estado = file_exists($archivo) ? "✅ Existe" : "❌ No encontrado";
            $clase = file_exists($archivo) ? "success" : "error";
            echo "<tr><td>$archivo</td><td>$estado</td><td>$descripcion</td></tr>";
        }
        echo "</table>";
        echo "</div>";

        // Probar conexión a BD
        echo "<div class='section'>";
        echo "<h2>🗄️ Prueba de Conexión a Base de Datos</h2>";

        try {
            require_once 'config/database.php';

            $db = getDB();
            echo "<div class='success'>";
            echo "✅ Conexión PDO establecida correctamente<br>";
            echo "📍 Base de datos conectada: " . $db->query('SELECT DB_NAME()')->fetch()['computed'] . "<br>";
            echo "</div>";

            // Verificar tablas
            $stmt = $db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'SIST_ORDEN_COMPRA'");
            $tabla = $stmt->fetch();

            if ($tabla) {
                echo "<div class='success'>";
                echo "✅ Tabla SIST_ORDEN_COMPRA existe<br>";

                // Verificar estructura
                $stmt = $db->query("SELECT COUNT(*) as columnas FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'SIST_ORDEN_COMPRA'");
                $columnas = $stmt->fetch()['columnas'];
                echo "📊 Número de columnas: $columnas<br>";
                echo "</div>";

                // Probar inserción de prueba
                echo "<h3>💉 Prueba de Inserción</h3>";
                try {
                    $datos_prueba = [
                        'OC_NUMERO_EXPEDIENTE' => 'TEST-' . time(),
                        'OC_COMPRADOR_NOMBRE' => 'CLIENTE DE PRUEBA',
                        'OC_COMPRADOR_TIPO_DOCUMENTO' => 'DNI',
                        'OC_COMPRADOR_NUMERO_DOCUMENTO' => '12345678',
                        'OC_VEHICULO_MARCA' => 'TOYOTA',
                        'OC_VEHICULO_MODELO' => 'COROLLA',
                        'OC_FORMA_PAGO' => 'CONTADO',
                        'OC_PRECIO_VENTA' => 50000,
                        'OC_MONEDA_PRECIO_VENTA' => 'MN'
                    ];

                    $sql = "INSERT INTO SIST_ORDEN_COMPRA (
                        OC_NUMERO_EXPEDIENTE, OC_COMPRADOR_NOMBRE, OC_COMPRADOR_TIPO_DOCUMENTO,
                        OC_COMPRADOR_NUMERO_DOCUMENTO, OC_VEHICULO_MARCA, OC_VEHICULO_MODELO,
                        OC_FORMA_PAGO, OC_PRECIO_VENTA, OC_MONEDA_PRECIO_VENTA
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute([
                        $datos_prueba['OC_NUMERO_EXPEDIENTE'],
                        $datos_prueba['OC_COMPRADOR_NOMBRE'],
                        $datos_prueba['OC_COMPRADOR_TIPO_DOCUMENTO'],
                        $datos_prueba['OC_COMPRADOR_NUMERO_DOCUMENTO'],
                        $datos_prueba['OC_VEHICULO_MARCA'],
                        $datos_prueba['OC_VEHICULO_MODELO'],
                        $datos_prueba['OC_FORMA_PAGO'],
                        $datos_prueba['OC_PRECIO_VENTA'],
                        $datos_prueba['OC_MONEDA_PRECIO_VENTA']
                    ]);

                    if ($result) {
                        $id = $db->lastInsertId();
                        echo "<div class='success'>";
                        echo "✅ ¡INSERCIÓN DE PRUEBA EXITOSA!<br>";
                        echo "📄 ID del registro de prueba: <strong>$id</strong><br>";
                        echo "🔍 Datos insertados: " . $datos_prueba['OC_COMPRADOR_NOMBRE'] . " - " . $datos_prueba['OC_VEHICULO_MARCA'] . " " . $datos_prueba['OC_VEHICULO_MODELO'];
                        echo "</div>";

                        // Limpiar registro de prueba
                        $db->exec("DELETE FROM SIST_ORDEN_COMPRA WHERE OC_ID = $id");
                        echo "<div class='info'>🗑️ Registro de prueba eliminado</div>";
                    } else {
                        echo "<div class='error'>❌ Error al ejecutar la inserción de prueba</div>";
                    }

                } catch (Exception $insertError) {
                    echo "<div class='error'>";
                    echo "❌ Error en inserción de prueba: " . $insertError->getMessage() . "<br>";
                    echo "🔍 Esto indica un problema con la estructura de la tabla o permisos";
                    echo "</div>";
                }

            } else {
                echo "<div class='error'>";
                echo "❌ Tabla SIST_ORDEN_COMPRA NO existe<br>";
                echo "💡 Necesitas ejecutar el script: database/schema_sist.sql en SQL Server<br>";
                echo "📋 Consulta SQL sugerida:<br>";
                echo "<code>USE documentos_db; SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'SIST_ORDEN_COMPRA';</code>";
                echo "</div>";
            }

        } catch (PDOException $pdoError) {
            echo "<div class='error'>";
            echo "❌ Error PDO específico: " . $pdoError->getMessage() . "<br>";
            echo "🔍 Esto indica que el driver PDO para SQL Server no está disponible<br>";
            echo "💡 Posibles soluciones:<br>";
            echo "1. Instalar o habilitar el driver php_sqlsrv<br>";
            echo "2. Verificar que SQL Server esté corriendo<br>";
            echo "3. Verificar credenciales en config/database.php<br>";
            echo "4. Verificar que la base de datos exista";
            echo "</div>";
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "❌ Error general: " . $e->getMessage() . "<br>";
            echo "📍 Archivo: " . $e->getFile() . "<br>";
            echo "📍 Línea: " . $e->getLine();
            echo "</div>";
        }

        echo "</div>";

        // Servicios necesarios
        echo "<div class='section'>";
        echo "<h2>🔧 Servicios Necesarios</h2>";
        echo "<div class='warning'>";
        echo "⚠️ Para que la aplicación funcione completamente necesitas:<br><br>";
        echo "1. <strong>Apache/Tomcat corriendo</strong> (puerto 80)<br>";
        echo "2. <strong>SQL Server corriendo</strong> (puerto 1433)<br>";
        echo "3. <strong>Base de datos 'documentos_db' creada</strong><br>";
        echo "4. <strong>Tabla 'SIST_ORDEN_COMPRA' creada</strong> ejecutando schema_sist.sql<br>";
        echo "5. <strong>Driver PDO para SQL Server instalado</strong>";
        echo "</div>";
        echo "</div>";

        // Enlaces de prueba
        echo "<div class='section'>";
        echo "<h2>🔗 Enlaces de la Aplicación</h2>";
        echo "<a href='/digitalizacion-documentos/documents' class='btn btn-success'>📋 Panel de Documentos</a>";
        echo "<a href='/digitalizacion-documentos/documents/show?id=orden-compra' class='btn'>📝 Formulario Orden Compra</a>";
        echo "<a href='/digitalizacion-documentos/documents/success' class='btn btn-secondary'>✅ Página de Éxito</a>";
        echo "</div>";
        ?>
    </div>
</body>
</html>