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


?>
<html>

<head>
    <title>Panel de usuario</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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
        <div class="border p-4 bg-white text-center" style="width: 454px;">


            <?php
            $user = $_SESSION["usuario"];


            $_SESSION['id_usuario'] = obtener_idusu($con, $user);
            $jefesobra = obtener_jefesobra($con);
            $encargados = obtener_encargados($con);
            $obras = obtener_obras($con);
            $empleados = obtener_empleados($con);
            $idRegistro = 0;

            echo "<form method='post' action='borrarmensualtabla.php'>
            <label for='fecha'>Fecha:</label>
            <input type='text' id='fecha' name='fecha' id='fecha' placeholder= 'DD/MM/AAAA' class='form-control'>";

            ?>

            <select name='datosdelaobra[]' class="form-select">
                <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar obra...</option>
                <?php
                while ($fila = obtener_resultados($obras)) {
                    extract($fila);
                    $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                    echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                }

                echo "</select></br>
                
                <br/><input type='submit' name='enviar' value='SELECCIONAR FECHA' id='botonMostrar' class='btn btn-danger' disabled>
                <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Escoge fecha, obra y pulsa SELECCIONAR FECHA.</div>
                </form>";  


                
                ?>
                <form method='post' action='user.php'>
                    <br><br>
                    <p><input type="submit" name="volver" value="VOLVER" class='btn btn-secondary' />
                </form>

                <script>
                    $(document).ready(function() {
                        // Configurar el calendario en español y con lunes como primer día de la semana
                        $.datepicker.setDefaults($.datepicker.regional['es']);
                        $.datepicker.setDefaults({
                            firstDay: 1
                        });

                        // Traducciones en español para jQuery UI Datepicker
                        $.datepicker.regional['es'] = {
                            closeText: 'Cerrar',
                            prevText: '&#x3C;Anterior',
                            nextText: 'Siguiente&#x3E;',
                            currentText: 'Hoy',
                            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
                            weekHeader: 'Sem',
                            dateFormat: 'dd/mm/yy'
                        };

                        // Asignar el calendario a la entrada de fecha
                        $('#fecha').datepicker($.datepicker.regional['es']);

                        // Validación de fecha
                        $('#fecha').on('change', function() {
                            var inputDate = $(this).val();
                            var parts = inputDate.split('/');
                            var day = parseInt(parts[0], 10);
                            var month = parseInt(parts[1], 10) - 1;
                            var year = parseInt(parts[2], 10);
                            var isValidDate = (new Date(year, month, day)).getDate() === day;

                            if (!isValidDate) {
                                alert('Fecha inválida. Por favor, introduce una fecha válida.');
                                $(this).val('');
                            }
                        });

                        // Hacer clic en el icono de información
                        $(document).on('click', '.infoIcon', function(event) {
                            event.preventDefault();
                            var hiddenText = $(this).next('.hidden-text');
                            hiddenText.fadeToggle(200);
                        });

                        // Obtener las referencias a los campos de selección
                        var selectMes = $("input[name='fecha");
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
        </div>
    </div>
</body>

</html>