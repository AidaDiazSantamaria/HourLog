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
    <title>Baja encargados</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body class="vh-100 d-flex align-items-center justify-content-center bg-light">


    <div class="border p-4 bg-white text-center" style="width: 454px;">



        <form method='post' action='bajaenc.php'>
            <h4>ELIMINAR ENCARGADOS</h4>
            <select name='datosdelenc[]' class="form-select mb-3">
                <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar encargado...</option>
                <?php
                $encargados = obtener_encargados($con);
                while ($fila = obtener_resultados($encargados)) {
                    extract($fila);

                    echo "<option value='$nombre_enc|$apellidos_enc'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los empleados
                }
                ?>
            </select><br>

            <div class="d-grid gap-2">
            <input type="submit" name="borrar" value="ELIMINAR" class="btn btn-danger" id="botonborrar" disabled>
            </div>
        </form>

        <?php
        if (isset($_POST['borrar'])) {
            if (empty($_POST['datosdelenc'])) { //Compruebo que los campos no están vacíos
                echo "<br>Debes escoger un encargado."; //En caso de que lo estén, saco un mensaje por pantalla

            } else if (isset($_POST['datosdelenc'])) { //Compruebo que estén seteados
                $codigos = $_POST['datosdelenc']; //Si está seteado, traigo su valor y lo meto en una variable
                $elemento = $codigos[0];
                $contenido = explode("|", $elemento);
                $nombre_enc = $contenido[0];
                $apellidos_enc = $contenido[1];


                borrar_encargado($con, $nombre_enc, $apellidos_enc); //Y borro el seleccionado
                echo '<meta http-equiv="refresh" content="3;URL=bajaenc.php">';
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
            var selectUsu = $("select[name='datosdelenc[]']");


            // Agregar un evento de cambio a ambos campos de selección
            selectUsu.change(function() {
                habilitarBotonborrar();
            });

            // Función para habilitar o deshabilitar el botón "MOSTRAR" según las selecciones
            function habilitarBotonborrar() {
                if (selectUsu.val() !== "" && !selectUsu.find(":selected").is(":disabled")) {
                    $("#botonborrar").prop("disabled", false);
                } else {
                    $("#botonborrar").prop("disabled", true);
                }
            }

            // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
            if (selectUsu.val() !== "" && !selectUsu.find(":selected").is(":disabled")) {
                $("#botonborrar").prop("disabled", false);
            } else {
                $("#botonborrar").prop("disabled", true);
            }


        });
    </script>
</body>

</html>