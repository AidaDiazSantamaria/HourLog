<?php
session_start(); // Inicio de sesión
require("database.php"); // Incluir el archivo de la base de datos
$con = conectar(); // Conexión a la base de datos
if ($_SESSION['tipo_usuario'] != '1') { //Comprobación de usuario
    echo '<meta http-equiv="refresh" content="0;URL=index.php">';

    exit();
}
?>

<html>

<head>
    <title>Baja usuarios</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>
    <div class="container" style="display: flex; justify-content: center; align-items: center; height: 100vh;">

        <div class="border p-4 bg-white text-center" style="width: 454px;">
            <form method="post" action="bajausu.php">
                <h4>ELIMINAR USUARIOS</h4>
                <br>
                <select name="datosdelusu[]" class="form-select mb-3">
                    <option selected="selected" disabled="disabled" value="Seleccionar">Seleccionar usuario...</option>
                    <?php
                    $usuarios = obtener_usuarios($con);

                    while ($fila = obtener_resultados($usuarios)) {
                        extract($fila);
                        echo "<option value='$nombre_usu'>$nombre_usu</option>"; // Doy a escoger los usuarios
                    }
                    ?>
                </select>
                <div class="d-grid gap-2">
                    <input type="submit" name="borrar" value="ELIMINAR" class="btn btn-danger" id="botonborrar" disabled>

                </div>
            </form>

            <?php
            if (isset($_POST['borrar'])) {
                if (empty($_POST['datosdelusu'])) { // Compruebo que los campos no estén vacíos
                    echo "<br>Debes escoger un usuario."; // En caso de que estén vacíos, muestro un mensaje
                } else if (isset($_POST['datosdelusu'])) { // Compruebo que estén seteados
                    $codigos = $_POST['datosdelusu']; // Obtengo el valor seleccionado y lo asigno a una variable
                    borrar_usuario($con, $codigos); // Borro el usuario seleccionado
                    echo '<meta http-equiv="refresh" content="3;URL=bajausu.php">';
                }
            }
            ?>

            <form action="admin.php" method="post">
                <br /><input type="submit" name="volver_index" value="VOLVER" class="btn btn-secondary">
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {


            // Obtener las referencias a los campos de selección
            var selectUsu = $("select[name='datosdelusu[]']");


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