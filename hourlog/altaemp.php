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
    <title>Alta empleados</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="javascript.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
</head>

<body class="vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="border p-4 bg-white text-center" style="width: 454px;">
        <form action="altaemp.php" method="post">
            <h4>ALTA EMPLEADOS</h4>
            <br>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>

            <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos:</label>
                <input class="form-control" type="text" name="apellidos" id="apellidos">
            </div>

            <div class="mb-3">
                <label for="empresa" class="form-label">Empresa:</label>
                <input class="form-control" type="text" name="empresa" id="empresa">
            </div>

            <div class="mb-3">
                <label for="dni" class="form-label">DNI:</label>
                <input class="form-control" type="text" name="dni" id="dni">
            </div>

            <div class="mb-3">
                <label for="tel" class="form-label">Teléfono:</label>
                <input class="form-control" type="text" name="tel" id="tel">
            </div>

            <div class="d-grid gap-2">
            <input type="submit" name="crear" value="CREAR" class="btn btn-danger" id="botoncrear"  disabled>
            </div>
        </form>

        <?php
        if (isset($_POST['crear'])) {//Si se pulsa CREAR, se usan los datos para llamar a la función crear_empleado
            if (empty($_POST["nombre"]) || empty($_POST["apellidos"]) || empty($_POST["empresa"]) || empty($_POST["dni"]) || empty($_POST["tel"])) {
                echo "<br>Debes introducir todos los datos.<br>";
            } else if (isset($_POST["nombre"]) && isset($_POST["apellidos"]) && isset($_POST["empresa"]) && isset($_POST["dni"]) && isset($_POST["tel"])) {
                $nombre_emp = $_POST['nombre'];
                $apellidos_emp = $_POST['apellidos'];
                $empresa = $_POST['empresa'];
                $dni = $_POST['dni'];
                $tel = $_POST['tel'];

                $resultado = crear_empleado($con, $nombre_emp, $apellidos_emp, $empresa, $dni, $tel);
            }
        }
        ?>

        <form action="admin.php" method="post">
            <br /><input type="submit" name="volver_index" value="VOLVER" class="btn btn-secondary">
        </form>

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
            var selectNom = $("input[name='nombre");
            var selectApe = $("input[name='apellidos");
            var selectEmp = $("input[name='empresa");
            var selectDni = $("input[name='dni");
            var selectTel = $("input[name='tel");



            // Agregar un evento de cambio a ambos campos de selección
            selectNom.change(function() {
                habilitarBotonCrear();
            });

            selectApe.change(function() {
                habilitarBotonCrear();
            });

            selectEmp.change(function() {
                habilitarBotonCrear();
            });

            selectDni.change(function() {
                habilitarBotonCrear();
            });

            selectTel.change(function() {
                habilitarBotonCrear();
            });

            // Función para habilitar o deshabilitar el botón "MOSTRAR" según las selecciones
            function habilitarBotonCrear() {
                if (selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectApe.val() !== "" && !selectApe.find(":selected").is(":disabled") && selectEmp.val() !== "" && !selectEmp.find(":selected").is(":disabled") && selectDni.val() !== "" && !selectDni.find(":selected").is(":disabled") && selectTel.val() !== "" && !selectTel.find(":selected").is(":disabled")) {
                    $("#botoncrear").prop("disabled", false);
                } else {
                    $("#botoncrear").prop("disabled", true);
                }
            }

            // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
            if (selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectApe.val() !== "" && !selectApe.find(":selected").is(":disabled") && selectEmp.val() !== "" && !selectEmp.find(":selected").is(":disabled") && selectDni.val() !== "" && !selectDni.find(":selected").is(":disabled") && selectTel.val() !== "" && !selectTel.find(":selected").is(":disabled")) {
                $("#botoncrear").prop("disabled", false);
            } else {
                $("#botoncrear").prop("disabled", true);
            }


        });
    </script>
</body>

</html>