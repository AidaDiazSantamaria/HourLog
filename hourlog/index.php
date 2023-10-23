<?php
session_start(); // Inicio la sesión
?>
<!DOCTYPE html>
<html>

<!-- 
* @aidads         .
*                / \           
*              / ` /
*           <_ <_  |
*             /   |
*           /     |
*         /    \ /
*    |\  (   ) | |
*    \____\__ ) \_) 
-->

<head>
    <title>Hourlog</title>
    <link rel="icon" type="image/jpg" href="IMG/ADS.png" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .hidden-text {
            display: none;
            position: relative;
            padding: 10px;
            background-color: #f8f8f8;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-top: 5px;
        }

        .hidden-text::after {
            content: "";
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 10px;
            border-style: solid;
            border-color: transparent transparent #ccc transparent;
        }

        .infoIcon {
            cursor: pointer;
            width: 20px;
            height: 20px;
            margin-left: 5px;
        }
    </style>
</head>

<body>

<div>
        <form action="../index.html" method="post">
            <br /><input type="submit" name="volver_index" value="VOLVER" class="btn btn-danger" style="background-color: purple; border-color: purple;">
        </form>

    </div>

    <div class="container" style="display: flex; justify-content: center; align-items: center; height: 80vh;">
        <div style="border: 1px solid #ccc; padding: 20px; max-width: 400px; width: 100%; text-align: center;">
        <img src="IMG/logo.png" alt="Logo" style="width: 200px; height: 200px; margin-bottom: 20px;">
            <form action="index.php" method="post">
                <div class="form-group">
                    <label for="usuario">Usuario:</label>
                    <input type="text" id="usuario" name="usuario" class="form-control">

                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="contraseña" class="form-control">
                </div>

                <input type="submit" name="login" value="LOGIN" class="btn btn-danger btn-block">
                <img src="IMG/info.png" alt="Icono Información" class="infoIcon" />
                <div class="hidden-text">Usuarios para testeo: Admin 1234 / User 4567.</div>
            </form>
            <!-- <p><a href="contraseña.php">¿Has olvidado tu contraseña?</a></p> -->
            <?php

            require("database.php");

            $con = conectar(); // Conecto con la base de datos

            if (isset($_POST['login'])) {
                if (empty($_POST["usuario"]) || empty($_POST["contraseña"])) { // Compruebo que los campos no están vacíos
                    echo "<br>Debes introducir usuario y contraseña."; // En caso de que lo estén, saco un mensaje por pantalla

                } else if (isset($_POST["usuario"]) && isset($_POST["contraseña"])) { // Compruebo que estén seteados

                    $usuario = $_POST["usuario"]; // Saco los datos del formulario
                    $contraseña = $_POST["contraseña"];

                    $result = obtener_tipousu($con, $usuario, $contraseña);

                    if ($result && mysqli_num_rows($result) > 0) { // Verifico que $result tenga resultados

                        $row = mysqli_fetch_array($result); // Saco los datos que necesito de la base de datos

                        if ($row["tipo_usuario"] == "2") { // Si el tipo de usuario es 1 pasa al panel de usuarios normales
                            $_SESSION["usuario"] = $usuario;
                            $_SESSION["tipo_usuario"] = $row["tipo_usuario"];
                            echo '<meta http-equiv="refresh" content="0;URL=user.php">';
                        } elseif ($row["tipo_usuario"] == "1") { // Si el tipo de usuario es 2 pasa al panel admin
                            $_SESSION["tipo_usuario"] = $row["tipo_usuario"];
                            $_SESSION["usuario"] = $usuario;
                            echo '<meta http-equiv="refresh" content="0;URL=admin.php">';
                        }
                    } else { // Si no hay resultados, mostrar un mensaje de error
                        echo "Usuario y contraseña no coinciden, o son incorrectos.";
                    }
                }
            }

            ?>

        </div>
    </div>
    


    <script>
        $(document).ready(function() {


            // Hacer clic en el icono de información
            $(document).on('click', '.infoIcon', function(event) {
                event.preventDefault();
                var hiddenText = $(this).next('.hidden-text');
                hiddenText.fadeToggle(200);
            });


        });
    </script>

</body>

</html>