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
    <title>Alta usuarios</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>

</head>

<body>
    <div class="container" style="display: flex; justify-content: center; align-items: center; height: 100vh;">

        <div class="border p-4 bg-white text-center" style="width: 454px;">
            <h4>ALTA USUARIOS</h4>
            <br>
            <form action="altausu.php" method="post">

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>

                <div class="mb-3">
                    <label for="mail" class="form-label">Correo electrónico:</label>
                    <input type="text" class="form-control" id="mail" name="mail" required pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$" title="Por favor, introduce un correo electrónico válido.">                   
                    <p id="error-message"></p>

                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña:</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="contraseña" required>
                        <button type="button" id="show-password-btn" class="btn btn-outline-secondary">Mostrar contraseña</button>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button id="generarpw" class="btn btn-secondary">GENERAR CONTRASEÑA SEGURA</button><br />
                </div>

                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo: 1-Admin 2-Usuario</label>
                    <input type="text" class="form-control" id="tipo" name="tipo" placeholder="1/2" required>
                </div>

                <div class="d-grid gap-2">
                    <input type="submit" name="crear" value="CREAR" class="btn btn-danger" id="botoncrear" disabled>
                </div>
            </form>

            <?php
            if (isset($_POST['crear'])) {
                if (empty($_POST["nombre"]) || empty($_POST["contraseña"]) || empty($_POST["tipo"]) || empty($_POST["mail"])) {
                    echo "<br>Debes introducir todos los datos.<br>";
                } else if (isset($_POST["nombre"]) && isset($_POST["contraseña"]) && isset($_POST["tipo"]) && isset($_POST["mail"])) {
                    $nombre_usu = $_POST['nombre'];
                    $pw = $_POST['contraseña'];
                    $tipo_usuario = $_POST['tipo'];
                    $mail = $_POST['mail'];


                    $resultado = crear_usuario($con, $nombre_usu, $pw, $tipo_usuario, $mail);
                }
            }
            ?>

            <script>
                $(document).ready(function() {

                    $('#generarpw').click(function(event) {
                        event.preventDefault(); // Evita que el formulario se envíe
                        var length = 10,
                            charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
                        var pw = "";
                        for (var i = 0, n = charset.length; i < length; ++i) {
                            pw += charset.charAt(Math.floor(Math.random() * n));
                        }
                        $('input[name="contraseña"]').val(pw); // Asigna la contraseña generada al campo correspondiente
                    });

                    $("#show-password-btn").on("click", function() {
                        var passwordInput = $("#password");
                        var passwordFieldType = passwordInput.attr("type");
                        var newPasswordFieldType = passwordFieldType === "password" ? "text" : "password";
                        passwordInput.attr("type", newPasswordFieldType);
                        $(this).text(newPasswordFieldType === "password" ? "Mostrar contraseña" : "Ocultar contraseña");
                    });

                    $('#mail').on('input', function() {
                        var email = $(this).val().trim();
                        var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                        if (!regex.test(email)) {
                            $(this).addClass('is-invalid');
                            $('#error-message').text('Por favor, introduce un correo electrónico válido.');
                        } else {
                            $(this).removeClass('is-invalid');
                            $('#error-message').text('');
                        }
                    });

                    // Obtener las referencias a los campos de selección
                    var selectNom = $("input[name='nombre");
                    var selectMail = $("input[name='mail");
                    var selectPw = $("input[name='password");
                    var selectTipo = $("input[name='tipo");


                    // Agregar un evento de cambio a ambos campos de selección
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
                        if (selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectMail.val() !== "" && !selectMail.find(":selected").is(":disabled") && selectPw.val() !== "" && !selectPw.find(":selected").is(":disabled") && selectTipo.val() !== "" && !selectTipo.find(":selected").is(":disabled")) {
                            $("#botoncrear").prop("disabled", false);
                        } else {
                            $("#botoncrear").prop("disabled", true);
                        }
                    }

                    // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
                    if (selectNom.val() !== "" && !selectNom.find(":selected").is(":disabled") && selectMail.val() !== "" && !selectMail.find(":selected").is(":disabled") && selectPw.val() !== "" && !selectPw.find(":selected").is(":disabled") && selectTipo.val() !== "" && !selectTipo.find(":selected").is(":disabled")) {
                        $("#botoncrear").prop("disabled", false);
                    } else {
                        $("#botoncrear").prop("disabled", true);
                    }


                });
            </script>

            <form action="admin.php" method="post">
                <br /><input type="submit" name="volver_index" value="VOLVER" class="btn btn-secondary">
            </form>
        </div>

</body>

</html>