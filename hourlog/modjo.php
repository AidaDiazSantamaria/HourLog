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
    <title>Modificación jefes de obra</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="javascript.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
</head>
</head>

<body class="vh-100 d-flex align-items-center justify-content-center bg-light">


    <div class="border p-4 bg-white text-center" style="width: 454px;">
        <h4>MODIFICAR JEFES DE OBRA</h4><br>

        <form action="modjo.php" method="post">
            <div class="mb-3">
                <select name='datosmodjo[]' class="form-select">
                    <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar jefe de obra...</option>

                    <?php
                    $jefesobra = obtener_jefesobra($con);

                    while ($fila = obtener_resultados($jefesobra)) {
                        extract($fila);

                        echo "<option value='$id_jo|$nombre_jo|$apellidos_jo'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
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
            <input type="submit" name="modificar_jo" value="MODIFICAR JEFE DE OBRA" class="btn btn-danger" id="botoncrear" disabled>
            </div>
        </form>

        <?php

        if (isset($_POST['modificar_jo'])) {
            if ((empty($_POST['datosmodjo'])) || (empty($_POST['nombre'])) || (empty($_POST['apellidos']))) { //Compruebo que los campos no están vacíos
                echo "<br>Debes rellenar todos los campos."; //En caso de que lo estén, saco un mensaje por pantalla

            } else if ((isset($_POST['datosmodjo'])) && (isset($_POST['nombre'])) && (isset($_POST['apellidos']))) {
                $codigos = $_POST['datosmodjo'];
                $elemento = $codigos[0];
                $contenido = explode("|", $elemento);
                $id_selec = $contenido[0];
                $nombre_jo = $_POST['nombre'];
                $apellidos_jo = $_POST['apellidos'];

                mod_jefeobra($con, $id_selec, $nombre_jo, $apellidos_jo); //Y los seteo en la tabla
                echo '<meta http-equiv="refresh" content="3;URL=modjo.php">';
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
            var selectJo = $("select[name='datosmodjo[]']");
            var selectNom = $("input[name='nombre");
            var selectApe = $("input[name='apellidos");


            // Agregar un evento de cambio a ambos campos de selección

            selectJo.change(function() {
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
                if (selectJo.val() !== "" && !selectJo.find(":selected").is(":disabled") && selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectApe.val() !== "" && !selectApe.find(":selected").is(":disabled")) {
                    $("#botoncrear").prop("disabled", false);
                } else {
                    $("#botoncrear").prop("disabled", true);
                }
            }

            // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
            if (selectJo.val() !== "" && !selectJo.find(":selected").is(":disabled") && selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectApe.val() !== "" && !selectApe.find(":selected").is(":disabled")) {
                $("#botoncrear").prop("disabled", false);
            } else {
                $("#botoncrear").prop("disabled", true);
            }


        });
    </script>
</body>

</html>