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
    <title>Alta obras</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="javascript.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
</head>

<body class="vh-100 d-flex align-items-center justify-content-center bg-light">


    <div class="border p-4 bg-white text-center" style="width: 454px;">

        <form action="altaobra.php" method="post">
            <h4>ALTA OBRA</h4>
            <br>

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="codigo" class="form-label">Código:</label>
                <input type="text" class="form-control" id="codigo" name="codigo" required>
            </div>
            <div class="mb-3">
                <label for="empresa" class="form-label">Empresa:</label>
                <input type="text" class="form-control" id="empresa" name="empresa" required>
            </div>
            <div class="mb-3">
                <label for="provincia" class="form-label">Provincia:</label>
                <input type="text" class="form-control" id="provincia" name="provincia" required>
            </div>
            <div class="d-grid gap-2">
            <input type="submit" name="crear" value="CREAR" class="btn btn-danger" id="botoncrear" disabled>
            </div>

        </form>

        <?php

        if (isset($_POST['crear'])) {
            if ((empty($_POST["nombre"])) || (empty($_POST["codigo"])) || (empty($_POST["empresa"])) || (empty($_POST["provincia"]))) {
                echo "<br>Debes introducir todos los datos.<br>";
            } else if ((isset($_POST["nombre"])) && (isset($_POST['codigo'])) && (isset($_POST['empresa'])) && (isset($_POST['provincia']))) {
                $nombre_obra = $_POST['nombre'];
                $codigo_obra = $_POST['codigo'];
                $empresa_obra = $_POST['empresa'];
                $provincia_obra = $_POST['provincia'];


                $resultado = crear_obra($con, $nombre_obra, $provincia_obra, $codigo_obra, $empresa_obra);
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
            var selectCod = $("input[name='codigo");
            var selectEmp = $("input[name='empresa");
            var selectProv = $("input[name='provincia");


            // Agregar un evento de cambio a ambos campos de selección
            selectNom.change(function() {
                habilitarBotonCrear();
            });

            selectCod.change(function() {
                habilitarBotonCrear();
            });

            selectEmp.change(function() {
                habilitarBotonCrear();
            });

            selectProv.change(function() {
                habilitarBotonCrear();
            });

            // Función para habilitar o deshabilitar el botón "MOSTRAR" según las selecciones
            function habilitarBotonCrear() {
                if (selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectCod.val() !== "" && !selectCod.find(":selected").is(":disabled") && selectEmp.val() !== "" && !selectEmp.find(":selected").is(":disabled") && selectProv.val() !== "" && !selectProv.find(":selected").is(":disabled")) {
                    $("#botoncrear").prop("disabled", false);
                } else {
                    $("#botoncrear").prop("disabled", true);
                }
            }

            // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
            if (selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectCod.val() !== "" && !selectCod.find(":selected").is(":disabled") && selectEmp.val() !== "" && !selectEmp.find(":selected").is(":disabled") && selectProv.val() !== "" && !selectProv.find(":selected").is(":disabled")) {
                $("#botoncrear").prop("disabled", false);
            } else {
                $("#botoncrear").prop("disabled", true);
            }


        });
    </script>
</body>

</html>