<?php
session_start(); //Inicio la sesión
require_once("database.php");
$con = conectar();
if ($_SESSION['tipo_usuario'] != '1') { //Comprobación de usuario
    echo '<meta http-equiv="refresh" content="0;URL=index.php">';

    exit();
}
?>
<html>

<head>
    <title>Modificación encargados</title>
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
        <h4>MODIFICAR ENCARGADOS</h4><br>

        <form action="modenc.php" method="post">
            <div class="mb-3">
                <select name='datosmodenc[]' class="form-select">
                    <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar encargado...</option>

                    <?php
                    $encargados = obtener_encargados($con);

                    while ($fila = obtener_resultados($encargados)) {
                        extract($fila);

                        echo "<option value='$id_encargado|$nombre_enc|$apellidos_enc'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                    }

                    ?>

                </select>

            </div>

            <div class="mb-3">
                <label for="nombre" class="form-label"><br>Nuevo nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre">
            </div>
            <div class="mb-3">
                <label for="apellidos" class="form-label"><br>Nuevos apellidos:</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos">
            </div>
            <div class="d-grid gap-2">
            <input type="submit" name="modificar_enc" value="MODIFICAR ENCARGADO" class="btn btn-danger" id="botoncrear" disabled>
            </div>

        </form>

        <?php

        if (isset($_POST['modificar_enc'])) {
            if ((empty($_POST['datosmodenc'])) || (empty($_POST['nombre'])) || (empty($_POST['apellidos']))) { //Compruebo que los campos no están vacíos
                echo "<br>Debes rellenar todos los campos."; //En caso de que lo estén, saco un mensaje por pantalla

            } else if ((isset($_POST['datosmodenc'])) && (isset($_POST['nombre'])) && (isset($_POST['apellidos']))) {
                $codigos = $_POST['datosmodenc'];
                $elemento = $codigos[0];
                $contenido = explode("|", $elemento);
                $id_encargado = $contenido[0];

                $nombre_enc = $_POST['nombre'];
                $apellidos_enc = $_POST['apellidos'];

                mod_encargado($con, $id_encargado, $nombre_enc, $apellidos_enc); //Y los seteo en la tabla
                echo '<meta http-equiv="refresh" content="3;URL=modenc.php">';
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
            var selectUsu = $("select[name='datosmodusu[]']");
            var selectNom = $("input[name='nombre");
            var selectApe = $("input[name='apellidos");


            // Agregar un evento de cambio a ambos campos de selección

            selectUsu.change(function() {
                habilitarBotonCrear();
            });

            selectNom.change(function() {
                habilitarBotonCrear();
            });

            selectApe.change(function() {
                habilitarBotonCrear();
            });

            // Función para habilitar o deshabilitar el botón "MOSTRAR" según las selecciones
            function habilitarBotonCrear() {
                if (selectUsu.val() !== "" && !selectUsu.find(":selected").is(":disabled") && selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectApe.val() !== "" && !selectApe.find(":selected").is(":disabled")) {
                    $("#botoncrear").prop("disabled", false);
                } else {
                    $("#botoncrear").prop("disabled", true);
                }
            }

            // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
            if (selectUsu.val() !== "" && !selectUsu.find(":selected").is(":disabled") && selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectApe.val() !== "" && !selectApe.find(":selected").is(":disabled")) {
                $("#botoncrear").prop("disabled", false);
            } else {
                $("#botoncrear").prop("disabled", true);
            }


        });
    </script>
</body>

</html>