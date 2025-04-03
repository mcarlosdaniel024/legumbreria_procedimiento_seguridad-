<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4 shadow-lg text-center" style="width: 25rem;">
        <h2>Bienvenido, <?= $_SESSION['usuario'] ?>!</h2>
        <a href="logout.php" class="btn btn-danger mt-3">Cerrar sesión</a>
    </div>
</body>
</html>
