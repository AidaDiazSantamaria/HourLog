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
    <title>Modificar obras</title>
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
        <h4>MODIFICAR OBRAS</h4><br>

        <form action="modobra.php" method="post">
            <div class="mb-3">
                <select name='datosmodobra[]' class="form-select">
                    <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar obra...</option>

                    <?php
                    $obras = obtener_obras($con);

                    while ($fila = obtener_resultados($obras)) {
                        extract($fila);

                        echo "<option value='$nombre_obra|$codigo_obra|$provincia_obra|$id_obra'>$nombre_obra $codigo_obra $provincia_obra</option><br>"; //Doy a escoger los obras
                    }

                    ?>

                </select>

            </div>
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
            <input type="submit" name="modificar_obra" value="MODIFICAR OBRA" class="btn btn-danger" id="botoncrear" disabled>
            </div>

        </form>

        <?php

        if (isset($_POST['modificar_obra'])) {
            // echo "<br>Se ha escogido la obra con los siguientes datos: <br>Nombre: $nombre_obra<br>Código: $codigo_obra<br>Empresa: $empresa_obra<br>Provincia: $provincia_obra<br>";
            if ((empty($_POST['datosmodobra'])) || (empty($_POST['nombre'])) || (empty($_POST['codigo'])) || (empty($_POST['empresa'])) || (empty($_POST["provincia"]))) { //Compruebo que los campos no están vacíos
                echo "<br>Debes rellenar todos los campos."; //En caso de que lo estén, saco un mensaje por pantalla

            } else if ((isset($_POST['datosmodobra'])) && (isset($_POST['nombre'])) && (isset($_POST['codigo'])) && (isset($_POST['empresa'])) && (isset($_POST['provincia']))) { //Compruebo que estén seteados
                $codigos = $_POST['datosmodobra'];
                $elemento = $codigos[0];
                $contenido = explode("|", $elemento);
                $id_obra = $contenido[3];



                $nombre_obra = $_POST['nombre'];
                $codigo_obra = $_POST['codigo'];
                $empresa_obra = $_POST['empresa'];
                $provincia_obra = $_POST['provincia'];


                mod_obra($con, $id_obra, $nombre_obra, $provincia_obra, $codigo_obra, $empresa_obra); //Y los seteo en la tabla
                echo '<meta http-equiv="refresh" content="3;URL=modobra.php">';
            }
        }

        ?>

        <form action="admin.php" method="post">
            <br/><input type="submit" name="volver_index" value="VOLVER" class="btn btn-secondary">
        </form>

    </div>
    <script>
        $(document).ready(function() {

            // Obtener las referencias a los campos de selección
            var selectObra = $("select[name='datosmodobra[]']");

            var selectNom = $("input[name='nombre");
            var selectCod = $("input[name='codigo");
            var selectEmp = $("input[name='empresa");
            var selectProv = $("input[name='provincia");


            // Agregar un evento de cambio a ambos campos de selección
            selectObra.change(function() {
                habilitarBotonCrear();
            });

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
                if (selectObra.val() !== "" && !selectObra.find(":selected").is(":disabled") &&selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectCod.val() !== "" && !selectCod.find(":selected").is(":disabled") && selectEmp.val() !== "" && !selectEmp.find(":selected").is(":disabled") && selectProv.val() !== "" && !selectProv.find(":selected").is(":disabled")) {
                    $("#botoncrear").prop("disabled", false);
                } else {
                    $("#botoncrear").prop("disabled", true);
                }
            }

            // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
            if (selectObra.val() !== "" && !selectObra.find(":selected").is(":disabled") &&selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectCod.val() !== "" && !selectCod.find(":selected").is(":disabled") && selectEmp.val() !== "" && !selectEmp.find(":selected").is(":disabled") && selectProv.val() !== "" && !selectProv.find(":selected").is(":disabled")) {
                $("#botoncrear").prop("disabled", false);
            } else {
                $("#botoncrear").prop("disabled", true);
            }


        });
    </script>
</body>

</html>