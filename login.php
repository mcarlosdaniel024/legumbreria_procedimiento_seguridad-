<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'conexion.php';
    
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    $stmt = $conn->prepare("SELECT id_usuario, nombre, contraseña, intentos_fallidos, bloqueado, id_rol FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if ($user['bloqueado']) {
            $error = "Cuenta bloqueada. Contacta al administrador.";
        } elseif (hash('sha256', $password) === $user['contraseña']) {
            $_SESSION['usuario'] = $user['nombre'];
            $_SESSION['rol'] = $user['id_rol'];
            
            $stmt = $conn->prepare("UPDATE usuario SET intentos_fallidos = 0 WHERE id_usuario = ?");
            $stmt->bind_param("i", $user['id_usuario']);
            $stmt->execute();
            
            if ($user['id_rol'] == 3) { 
                header("Location: admin_desbloquear.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $intentos = $user['intentos_fallidos'] + 1;
            if ($intentos >= 4) {
                $stmt = $conn->prepare("UPDATE usuario SET bloqueado = 1 WHERE id_usuario = ?");
                $stmt->bind_param("i", $user['id_usuario']);
                $stmt->execute();
                $error = "Cuenta bloqueada por demasiados intentos.";
            } else {
                $stmt = $conn->prepare("UPDATE usuario SET intentos_fallidos = ? WHERE id_usuario = ?");
                $stmt->bind_param("ii", $intentos, $user['id_usuario']);
                $stmt->execute();
                $error = "Contraseña incorrecta. Intentos restantes: " . (4 - $intentos);
            }
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4 shadow-lg" style="width: 25rem;">
        <h2 class="text-center mb-3">Iniciar Sesión</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"> <?= $error ?> </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
    </div>
</body>
</html>
