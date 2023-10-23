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
    <title>Baja jefes de obra</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</head>

<body class="vh-100 d-flex align-items-center justify-content-center bg-light">

    <div class="border p-4 bg-white text-center" style="width: 454px;">
        <h4>ELIMINAR JEFES DE OBRA</h4><br>


        <form method='post' action='bajajo.php'>
            <select name='datosdeljo[]' class="form-select">
                <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar jefe de obra...</option>
                <?php
                $jefesobra = obtener_jefesobra($con);

                while ($fila = obtener_resultados($jefesobra)) {
                    extract($fila);

                    echo "<option value='$id_jo|$nombre_jo|$apellidos_jo'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                }
                ?>
            </select><br>

            <div class="d-grid gap-2">
            <input type="submit" name="borrar" value="ELIMINAR" class="btn btn-danger" id="botonborrar" disabled>
            </div>
        </form>
        <?php
        if (isset($_POST['borrar'])) {
            if (empty($_POST['datosdeljo'])) { //Compruebo que los campos no están vacíos
                echo "<br>Debes escoger un jefe de obra."; //En caso de que lo estén, saco un mensaje por pantalla

            } else if (isset($_POST['datosdeljo'])) { //Compruebo que estén seteados
                $codigos = $_POST['datosdeljo']; //Si está seteado, traigo su valor y lo meto en una variable
                $elemento = $codigos[0];
                $contenido = explode("|", $elemento);
                $id_selec = $contenido[0];

                borrar_jefeobra($con, $id_selec); //Y borro el seleccionado
                echo '<meta http-equiv="refresh" content="3;URL=bajajo.php">';
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
            var selecJo = $("select[name='datosdeljo[]']");


            // Agregar un evento de cambio a ambos campos de selección
            selecJo.change(function() {
                habilitarBotonborrar();
            });

            // Función para habilitar o deshabilitar el botón "MOSTRAR" según las selecciones
            function habilitarBotonborrar() {
                if (selecJo.val() !== "" && !selecJo.find(":selected").is(":disabled")) {
                    $("#botonborrar").prop("disabled", false);
                } else {
                    $("#botonborrar").prop("disabled", true);
                }
            }

            // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
            if (selecJo.val() !== "" && !selecJo.find(":selected").is(":disabled")) {
                $("#botonborrar").prop("disabled", false);
            } else {
                $("#botonborrar").prop("disabled", true);
            }


        });
    </script>
</body>

</html>