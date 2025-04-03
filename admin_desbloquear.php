<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 3) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['desbloquear_id'])) {
    $id_usuario = $_POST['desbloquear_id'];
    $stmt = $conn->prepare("CALL DesbloquearCuenta(?)");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_desbloquear.php");
    exit();
}

$result = $conn->query("SELECT id_usuario, nombre, email FROM usuario WHERE bloqueado = 1");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="text-center">Usuarios Bloqueados</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_usuario'] ?></td>
                    <td><?= $row['nombre'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="desbloquear_id" value="<?= $row['id_usuario'] ?>">
                            <button type="submit" class="btn btn-success">Desbloquear</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-primary">Volver</a>
</body>
</html>
