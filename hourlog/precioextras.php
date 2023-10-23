<?php
session_start(); //Inicio la sesión
require("database.php"); //Traigo la base de datos
require 'vendor/autoload.php';

$con = conectar(); //Conecto

if ($_SESSION['tipo_usuario'] != '1') { //Comprobación de usuario
    echo '<meta http-equiv="refresh" content="0;URL=index.php">';
    exit();
}


?>
<html>

<head>
    <title>Panel Admin</title>
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
</head>

<body>
    <div class="container" style="display: flex; justify-content: center; align-items: center; height: 100vh; flex-direction: column;">
        <div class="border p-4 bg-white text-center" style="width: 454px;">
            <h4>REGISTRO MENSUAL</h4>
            <br>

            <form method='post' action='precioextrastabla.php'>
                <select name='datosmensual[]' id="mesSeleccionado" class="form-select">
                    <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar mes...</option>

                    <?php
                    $meses = obtener_mesanios($con);
                    $nombresMeses = array(
                        1 => 'Enero',
                        2 => 'Febrero',
                        3 => 'Marzo',
                        4 => 'Abril',
                        5 => 'Mayo',
                        6 => 'Junio',
                        7 => 'Julio',
                        8 => 'Agosto',
                        9 => 'Septiembre',
                        10 => 'Octubre',
                        11 => 'Noviembre',
                        12 => 'Diciembre'
                    );
                    while ($fila = obtener_resultados($meses)) {
                        extract($fila);
                        $nombreMes = $nombresMeses[$mes]; // Obtener el nombre del mes en español
                        echo "<option value='$mes|$año'>$nombreMes $año</option>"; // Doy a escoger los meses con nombres
                    }
                    ?>
                </select><br><br>

                <div class="d-grid gap-2">
                    <input type='submit' name='mostrar' value='MOSTRAR' id="botonMostrar" class="btn btn-danger" disabled />
                    <img src="IMG/info.png" alt="Icono Información" class="infoIcon" />
                    <div class="hidden-text">Escoge fecha y pulsa MOSTRAR para visualizar el informe mensual de horas extra.</div>
                </div>
            </form>

            <form action="admin.php" method="post">
                <input type="submit" name="volver_index" value="VOLVER" class="btn btn-secondary">
            </form>

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

            // Obtener la referencia al campo de selección
            var selectElement = $("#mesSeleccionado");

            // Agregar un evento de cambio al campo de selección
            selectElement.change(function() {
                // Verificar si se ha seleccionado una opción
                if (selectElement.val() !== "Seleccionar") {
                    // Habilitar el botón si se seleccionó una opción
                    $("#botonMostrar").prop("disabled", false);
                } else {
                    // Si no se seleccionó ninguna opción, deshabilitar el botón
                    $("#botonMostrar").prop("disabled", true);
                }
            });
        });
    </script>
</body>



</html>