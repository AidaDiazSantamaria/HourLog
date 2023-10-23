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

$user = $_SESSION["usuario"];

$id_usuario = obtener_idusu($con, $user);
$_SESSION['id_usuario'] = $id_usuario;
$jefesobra = obtener_jefesobra($con);
$encargados = obtener_encargados($con);
$obras = obtener_obras($con);
$empleados = obtener_empleados($con);

?>
<html>

<head>
    <title>Panel de usuario</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="javascript.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
         html, body {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            padding: 20px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

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

        @media (max-width: 576px) {
        .container {
            max-width: 300px;
        }
    }

    @media (min-width: 577px) and (max-width: 991px) {
        .container {
            max-width: 500px;
        }
    }

    @media (min-width: 992px) and (max-width: 1919px) {
        .container {
            max-width: 800px;
        }
    }

    @media (min-width: 1920px) {
        .container {
            max-width: 1000px;
        }
    }
    </style>


</head>

<body>
    <div class="container vh-100 d-flex align-items-center justify-content-center">

        <div class="d-flex flex-column align-items-center">
            <div class="border p-4 bg-white text-center" style="width: 454px;">
                <h4>PANEL USUARIO</h4>
                <br>
                <div class="row mt-2">
                    <div class="col">
                    <form method='post' action='registromensual.php'>
                    <div class="d-grid gap-2">                            
                        <input type='submit' name='mensual' value='REGISTRO MENSUAL' class="btn btn-danger" />
                        <img src="IMG/info.png" alt="Icono Información" class="infoIcon" />
                        <div class="hidden-text">Para visualizar y exportar el REGISTRO MENSUAL.</div>
                        </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <form action="modificarmensual.php" method="post">
                            <div class="d-grid gap-2">
                                <input type="submit" name="modificar" value="MODIFICAR" class="btn btn-danger" />
                                <img src="IMG/info.png" alt="Icono Información" class="infoIcon" />
                                <div class="hidden-text">Pulsa MODIFICAR si quieres corregir algún registro.</div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col">
                        <form action="borrarmensual.php" method="post">
                            <div class="d-grid gap-2">
                                <input type="submit" name="eliminar" value="ELIMINAR" class="btn btn-danger" />
                                <img src="IMG/info.png" alt="Icono Información" class="infoIcon" />
                                <div class="hidden-text">Pulsa ELIMINAR si quieres borrar algún registro.</div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                    <form method='post' action='registrocabe.php'>
                        <div class="d-grid gap-2">                            
                                <input type="submit" name="cargar" value="REGISTRO DIARIO" class="btn btn-danger">
                                <img src="IMG/info.png" alt="Icono Información" class="infoIcon" />
                                <div class="hidden-text">Para rellenar el REGISTRO DIARIO.</div>                            
                        </div>
                        </form>
                    </div>
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

            <div class="d-grid gap-2">
                <form method="post">
                    <input type="hidden" name="cerrar_sesion" value="1">
                    <br /><br /><br /><input type="submit" class="btn btn-secondary" name="cerrar_sesion" value="CERRAR SESIÓN" /></p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>