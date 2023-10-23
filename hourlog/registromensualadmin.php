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
    <title>Panel de usuario</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="javascript.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
</head>
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

<body class="vh-100 d-flex align-items-center justify-content-center bg-light flex-column">

    <div class="border p-4 bg-white text-center small-screen" style="width: 454px;">

        <h4>REGISTRO MENSUAL</h4>
        <br>
        <form method='post' action='registromensualadmintabla.php'>

            <select name='datosmensual[]' class="form-select">
                <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar mes...</option>
                <?php
                $obras = obtener_obras($con);

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
            <select name='datosdelaobra[]' class="form-select">
                <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar obra...</option>
                <?php
                while ($fila = obtener_resultados($obras)) {
                    extract($fila);
                    $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                    echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                }
                ?>
            </select></br>
            <div class="d-grid gap-2">
                <input type='submit' name='mostrar' value='MOSTRAR' id="botonMostrar" class="btn btn-danger" disabled />
                <img src="IMG/info.png" alt="Icono Información" class="infoIcon" />
                <div class="hidden-text">Escoge fecha, obra y pulsa MOSTRAR para visualizar el informe mensual de horas.</div>
            </div>
        </form>


        <form action="admin.php" method="post">
            <br /><input type="submit" name="volver_index" value="VOLVER" class="btn btn-secondary">
        </form>

    </div>
    <script>
        $(document).ready(function() {

            // Hacer clic en el icono de información
            $(document).on('click', '.infoIcon', function(event) {
                event.preventDefault();
                var hiddenText = $(this).next('.hidden-text');
                hiddenText.fadeToggle(200);
            });

            // Obtener las referencias a los campos de selección
        var selectMes = $("select[name='datosmensual[]']");
        var selectObra = $("select[name='datosdelaobra[]']");

        // Agregar un evento de cambio a ambos campos de selección
        selectMes.change(function() {
            habilitarBotonMostrar();
        });

        selectObra.change(function() {
            habilitarBotonMostrar();
        });

        // Función para habilitar o deshabilitar el botón "MOSTRAR" según las selecciones
        function habilitarBotonMostrar() {
            if (selectMes.val() !== "" && !selectMes.find(":selected").is(":disabled") && selectObra.val() !== "" && !selectObra.find(":selected").is(":disabled")) {
                $("#botonMostrar").prop("disabled", false);
            } else {
                $("#botonMostrar").prop("disabled", true);
            }
        }

        // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
        if (selectMes.val() !== "" && !selectMes.find(":selected").is(":disabled") && selectObra.val() !== "" && !selectObra.find(":selected").is(":disabled")) {
            $("#botonMostrar").prop("disabled", false);
        } else {
            $("#botonMostrar").prop("disabled", true);
        }

        });
    </script>

</body>

</html>