<?php
session_start(); //Inicio la sesión
require_once("database.php");
$con = conectar();
if ($_SESSION['tipo_usuario'] != '1') { //Comprobación de usuario
    echo '<meta http-equiv="refresh" content="0;URL=index.php">';

    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Modificación empleados</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="javascript.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
    <style>
    body,
    html {

        margin: 1px;
        padding: 1px;
    }

   
</style>

</head>

<body>
    <div class="container" style="display: flex; justify-content: center; align-items: center; height: 100vh;">

        <div class="border p-4 bg-white text-center" style="width: 454px;">
            <h4>MODIFICAR EMPLEADOS</h4>

            <form action="modemp.php" method="post">
                <div class="mb-1">
                    <select name='datosmodemp[]' class="form-select">
                        <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar empleado...</option>

                        <?php
                        $empleados = obtener_empleados($con);

                        while ($fila = obtener_resultados($empleados)) {
                            extract($fila);

                            echo "<option value='$nombre_emp|$apellidos_emp|$dni'>$nombre_emp $apellidos_emp $dni</option>"; //Doy a escoger los empleados
                        }

                        ?>

                    </select>
                </div>

                <div class="mb-1">
                    <label for="nombre" class="form-label"><br>Nuevo nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre">
                </div>
                <div class="mb-1">
                    <label for="apellidos" class="form-label"><br>Nuevos apellidos:</label>
                    <input type="text" class="form-control" id="apellidos" name="apellidos">
                </div>
                <div class="mb-1">
                    <label for="empresa" class="form-label"><br>Nueva empresa:</label>
                    <input type="text" class="form-control" id="empresa" name="empresa">
                </div>
                <div class="mb-1">
                    <label for="dni" class="form-label"><br>DNI correcto:</label>
                    <input type="text" class="form-control" id="dni" name="dni">
                </div>
                <div class="mb-1">
                    <label for="tel" class="form-label"><br>Nuevo teléfono:</label>
                    <input type="text" class="form-control" id="tel" name="tel">
                </div>

                <br>
                <div class="d-grid gap-2">
                <input type="submit" name="modificar_emp" value="MODIFICAR EMPLEADO" class="btn btn-danger" id="botoncrear" disabled>
                </div>

            </form>

            <?php

            if (isset($_POST['modificar_emp'])) {
                if ((empty($_POST['datosmodemp'])) || (empty($_POST['nombre'])) || (empty($_POST['apellidos'])) || (empty($_POST['empresa'])) || (empty($_POST['dni'])) || (empty($_POST['tel']))) { //Compruebo que los campos no están vacíos
                    echo "<br>Debes rellenar todos los campos."; //En caso de que lo estén, saco un mensaje por pantalla

                } else if ((isset($_POST['datosmodemp'])) && (isset($_POST['nombre'])) && (isset($_POST['apellidos'])) && (isset($_POST['empresa'])) && (isset($_POST['dni'])) && (isset($_POST['tel']))) {
                    $codigos = $_POST['datosmodemp'];
                    $elemento = $codigos[0];
                    $contenido = explode("|", $elemento);
                    $dniselec = $contenido[2];

                    $nombre_emp = $_POST['nombre'];
                    $apellidos_emp = $_POST['apellidos'];
                    $empresa = $_POST['empresa'];
                    $dni = $_POST['dni'];
                    $tel = $_POST['tel'];

                    mod_empleado($con, $dniselec, $nombre_emp, $apellidos_emp, $empresa, $dni, $tel); //Y los seteo en la tabla
                    echo '<meta http-equiv="refresh" content="3;URL=modemp.php">';
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
            $("#dni").on("input", function() {
                var dni = $(this).val().trim();
                var regex = /^\d{8}[a-zA-Z]$/;

                if (!regex.test(dni)) {
                    $(this).addClass("is-invalid");
                } else {
                    var dniNumber = dni.substring(0, 8);
                    var dniLetter = dni.substring(8, 9).toUpperCase();
                    var letters = "TRWAGMYFPDXBNJZSQVHLCKE";
                    var letterIndex = dniNumber % 23;

                    if (dniLetter !== letters.charAt(letterIndex)) {
                        $(this).addClass("is-invalid");
                    } else {
                        $(this).removeClass("is-invalid");
                    }
                }
            });
            // Seleccionar el campo de teléfono por su ID
            var telInput = $("#tel");

            // Escuchar el evento de entrada de teclado en el campo de teléfono
            telInput.on("input", function() {
                // Obtener el valor actual del campo de teléfono
                var telValue = telInput.val();

                // Remover cualquier carácter que no sea un número
                var cleanValue = telValue.replace(/\D/g, "");

                // Limitar el número de dígitos a 9
                var maxLength = 9;
                var trimmedValue = cleanValue.slice(0, maxLength);

                // Asignar el valor limitado al campo de teléfono
                telInput.val(trimmedValue);
            });

// Obtener las referencias a los campos de selección
var selectEmp = $("select[name='datosmodemp[]']");
            var selectNom = $("input[name='nombre");
            var selectMail = $("input[name='mail");
            var selectPw = $("input[name='password");
            var selectTipo = $("input[name='tipo");


            // Agregar un evento de cambio a ambos campos de selección

            selectEmp.change(function() {
                habilitarBotonCrear();
            });

            selectNom.change(function() {
                habilitarBotonCrear();
            });

            selectMail.change(function() {
                habilitarBotonCrear();
            });

            selectPw.change(function() {
                habilitarBotonCrear();
            });

            selectTipo.change(function() {
                habilitarBotonCrear();
            });

            // Función para habilitar o deshabilitar el botón "MOSTRAR" según las selecciones
            function habilitarBotonCrear() {
                if (selectEmp.val() !== "" && !selectEmp.find(":selected").is(":disabled") && selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectMail.val() !== "" && !selectMail.find(":selected").is(":disabled") && selectPw.val() !== "" && !selectPw.find(":selected").is(":disabled") && selectTipo.val() !== "" && !selectTipo.find(":selected").is(":disabled")) {
                    $("#botoncrear").prop("disabled", false);
                } else {
                    $("#botoncrear").prop("disabled", true);
                }
            }

            // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
            if (selectEmp.val() !== "" && !selectEmp.find(":selected").is(":disabled") && selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectMail.val() !== "" && !selectMail.find(":selected").is(":disabled") && selectPw.val() !== "" && !selectPw.find(":selected").is(":disabled") && selectTipo.val() !== "" && !selectTipo.find(":selected").is(":disabled")) {
                $("#botoncrear").prop("disabled", false);
            } else {
                $("#botoncrear").prop("disabled", true);
            }

        });

        
        
    </script>
</body>

</html>