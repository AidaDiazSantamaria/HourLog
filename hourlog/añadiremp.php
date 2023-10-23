</body>

</html>

<?php
session_start(); //Inicio la sesión
require("database.php"); //Traigo la base de datos
$con = conectar(); //Conecto

if ($_SESSION['tipo_usuario'] != '2') { //Comprobación de usuario
    echo '<meta http-equiv="refresh" content="0;URL=index.php">';
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cerrar_sesion'])) {

    cerrar_sesion();
}

$tabla3 = $_SESSION['tabla3'];


?>
<html>

<head>
    <title>Panel de usuario</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="javascript.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
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
    <div class="container vh-100 d-flex align-items-center justify-content-center flex-column">
        <?php
        echo $tabla3;
        ?>

        <div class="border p-4 bg-white text-center" style="width: 454px;">


            <form method='post' action='cargaremp.php'>
                <div class='d-grid gap-2'>

                    <input type='submit' name='cargar' value='CARGAR ÚLTIMO DÍA' class="btn btn-danger" />
                    <img src="IMG/info.png" alt="Icono Información" class="infoIcon" />
                    <div class="hidden-text">En CARGAR ÚLTIMO DÍA, puedes añadir registros según lo que añadiste el día anterior.</div>

                </div>
            </form>

            <?php
            echo "<br><br>";

            echo "<form method='post' action='masemp.php'>
                <div class='d-grid gap-2'>  
                <input type='submit' name='añadir' value='MÁS EMPLEADOS' class='btn btn-danger'/>
                <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                <div class='hidden-text'>Pulsa MÁS EMPLEADOS para añadir registros desde cero.</div>
    
                </div>
                </form>";
            ?>

            <form method="post">
                <br>
                <p><input type="submit" name="otro" value="VOLVER" class="btn btn-secondary">
            </form>
        </div>

        <?php
        if (isset($_POST['otro'])) {
            $fecha = $_SESSION['fecha'];
            $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $_SESSION['fecha'])));
            $codigo_obra = $_SESSION['codigo_obra'];
            $id_usuario = $_SESSION['id_usuario'];

            // Verificar si hay registros en horascuerpo para la fecha y código de obra
            $consulta = "SELECT COUNT(*) AS total FROM horascuerpo WHERE fecha = '$fecha' AND codigo_obra = '$codigo_obra'";
            $resultado = mysqli_query($con, $consulta);

            if ($resultado && mysqli_fetch_assoc($resultado)['total'] == 0) {
                // No hay registros en horascuerpo, eliminar último registro de horascabecera del id de usuario
                $deleteQuery = "DELETE FROM horascabecera WHERE id_usuario = $id_usuario ORDER BY id_horascabe DESC LIMIT 1";
                mysqli_query($con, $deleteQuery);
            }

            unset($_SESSION['fecha']);
            echo '<meta http-equiv="refresh" content="0;URL=user.php">';

            exit;
        }
        ?>

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