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
    <title>Modificación provincias</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="javascript.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
</head>

<body class="vh-100 d-flex align-items-center justify-content-center bg-light">

    <div class="border p-4 bg-white text-center" style="width: 454px;">
        <h4>MODIFICAR PROVINCIAS</h4><br>

        <form action="modprovincia.php" method="post">
            <div>
                <select name='datosmodprov[]' class="form-select">
                    <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar provincia...</option>

                    <?php
                    $provincias = obtener_provincias($con);

                    while ($fila = obtener_resultados($provincias)) {
                        extract($fila);

                        echo "<option value='$id_provincia|$provincia'>$provincia</option>"; //Doy a escoger las provincias
                    }

                    ?>

                </select>

            </div>

            <div class="mb-3">
                <br /><label for="nombre" class="form-label">Nuevo nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nuevo precio hora extra:</label>
                <input type="text" class="form-control" id="phe" name="phe" required placeholder="0.00">
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nuevo precio hora extra festiva:</label>
                <input type="text" class="form-control" id="phef" name="phef" required placeholder="0.00">
            </div>

            <div class="d-grid gap-2">
            <input type="submit" name="modificar" value="MODIFICAR PROVINCIA" class="btn btn-danger" id="botoncrear" disabled>
            </div>


        </form>

        <?php

        if (isset($_POST['modificar'])) {
            // echo "<br>Se va a modificar el encargado con los siguientes datos: <br>Nombre: $nombre_enc<br>Apellidos: $apellidos_enc<br>";
            if ((empty($_POST['datosmodprov'])) || (empty($_POST['nombre'])) || (empty($_POST['phe'])) || (empty($_POST['phef']))) { //Compruebo que los campos no están vacíos
                echo "<br>Debes rellenar todos los campos."; //En caso de que lo estén, saco un mensaje por pantalla

            } else if ((isset($_POST['datosmodprov'])) && (isset($_POST['nombre'])) && (isset($_POST['phe'])) && (isset($_POST['phef']))) {
                $codigos = $_POST['datosmodprov'];
                $elemento = $codigos[0];
                $contenido = explode("|", $elemento);
                $id_provincia = $contenido[0];

                $provincia = $_POST['nombre'];
                $preciohorasextra = $_POST['phe'];
                $preciohorasextrafestivo = $_POST['phef'];


                mod_provincia($con, $id_provincia, $provincia, $preciohorasextra, $preciohorasextrafestivo); //Y los seteo en la tabla
                echo '<meta http-equiv="refresh" content="3;URL=modprovincia.php">';
            }
        }

        ?>

        <form action="admin.php" method="post">
            <br /><input type="submit" name="volver_index" value="VOLVER" class="btn btn-secondary">
        </form>

    </div>
    <script>
        $(document).ready(function() {
            $('#phe, #phef').on('input', function() {
                var inputValue = $(this).val();
                var validDecimalRegex = /^\d+(\.\d*)?$/;

                if (!validDecimalRegex.test(inputValue)) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Obtener las referencias a los campos de selección
            var selectProv = $("select[name='datosmodprov[]']");

            var selectNom = $("input[name='nombre");
            var selectphe = $("input[name='phe");
            var selectphef = $("input[name='phef");


            // Agregar un evento de cambio a ambos campos de selección
            selectProv.change(function() {
                habilitarBotonCrear();
            });

            selectNom.change(function() {
                habilitarBotonCrear();
            });

            selectphe.change(function() {
                habilitarBotonCrear();
            });

            selectphef.change(function() {
                habilitarBotonCrear();
            });

            // Función para habilitar o deshabilitar el botón "MOSTRAR" según las selecciones
            function habilitarBotonCrear() {
                if (selectProv.val() !== "" && !selectProv.find(":selected").is(":disabled") && selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectphe.val() !== "" && !selectphe.find(":selected").is(":disabled") && selectphef.val() !== "" && !selectphef.find(":selected").is(":disabled")) {
                    $("#botoncrear").prop("disabled", false);
                } else {
                    $("#botoncrear").prop("disabled", true);
                }
            }

            // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
            if (selectProv.val() !== "" && !selectProv.find(":selected").is(":disabled") && selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectphe.val() !== "" && !selectphe.find(":selected").is(":disabled") && selectphef.val() !== "" && !selectphef.find(":selected").is(":disabled")) {
                $("#botoncrear").prop("disabled", false);
            } else {
                $("#botoncrear").prop("disabled", true);
            }

        });
    </script>
</body>

</html>