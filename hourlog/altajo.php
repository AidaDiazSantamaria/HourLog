<?php
session_start(); //Inicio la sesión
require("database.php"); //Traigo la base de datos
$con = conectar(); //Conecto
if ($_SESSION['tipo_usuario'] != '1') { //Comprobación de usuario
    echo '<meta http-equiv="refresh" content="0;URL=index.php">';

    exit();
}
?>
<html>

<head>
    <title>Alta jefes de obra</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="javascript.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
</head>

<body class="vh-100 d-flex align-items-center justify-content-center bg-light">

    <div class="border p-4 bg-white text-center" style="width: 454px;">

        <form action="altajo.php" method="post">
            <h4>ALTA JEFES DE OBRA</h4>

            <br>
            <div class="mb-3">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="apellidos">Apellidos:</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
            </div>

            <div class="d-grid gap-2">
            <input type="submit" name="crear" value="CREAR" class="btn btn-danger" id="botoncrear" disabled>
            </div>
        </form>

        <?php

        if (isset($_POST['crear'])) {
            if (empty($_POST["nombre"]) || empty($_POST["apellidos"])) {
                echo "<br>Debes introducir todos los datos.<br>";
            } else if (isset($_POST["nombre"]) && isset($_POST["apellidos"])) {
                $nombre_jo = $_POST['nombre'];
                $apellidos_jo = $_POST['apellidos'];

                $resultado = crear_jefeobra($con, $nombre_jo, $apellidos_jo);
            }
        }

        ?>

        <form action="admin.php" method="post">
            <br /><input type="submit" name="volver_index" value="VOLVER" class="btn btn-secondary">
        </form>

    </div>
    <script>
        $(document).ready(function() {

            // Obtener las referencias a los campos de selección
            var selectNom = $("input[name='nombre");
            var selectApe = $("input[name='apellidos");

            // Agregar un evento de cambio a ambos campos de selección
            selectNom.change(function() {
                habilitarBotonCrear();
            });

            selectApe.change(function() {
                habilitarBotonCrear();
            });

            // Función para habilitar o deshabilitar el botón "MOSTRAR" según las selecciones
            function habilitarBotonCrear() {
                if (selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectApe.val() !== "" && !selectApe.find(":selected").is(":disabled")) {
                    $("#botoncrear").prop("disabled", false);
                } else {
                    $("#botoncrear").prop("disabled", true);
                }
            }

            // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
            if (selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectApe.val() !== "" && !selectApe.find(":selected").is(":disabled")) {
                $("#botoncrear").prop("disabled", false);
            } else {
                $("#botoncrear").prop("disabled", true);
            }


        });
    </script>
</body>

</html>