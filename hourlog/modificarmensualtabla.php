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
        <div class="border p-4 bg-white text-center" style="width: 850px;">

            <?php

            if (isset($_POST['enviar'])) {
                if (empty($_POST["fecha"]) || empty($_POST["datosdelaobra"])) {
                    echo "<br>Debes seleccionar una fecha y una obra.<br>";
                } else if (isset($_POST["fecha"]) || (isset($_POST["datosdelaobra"]))) {
                    $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $_POST["fecha"])));
                    $fechaderecha = date('d-m-Y', strtotime(str_replace('/', '-', $_POST["fecha"])));
                    $_SESSION['fecha'] = $fecha;
                    $datosobra = $_POST['datosdelaobra'];
                    $obra_selec = explode("|", $datosobra[0]);
                    $codigo_obra = $obra_selec[1];
                    $_SESSION['codigo_obra'] = $codigo_obra;

                    $id_usuario = $_SESSION['id_usuario'];

                    $resultado = obtener_ulregistrocuerpo($con, $id_usuario, $fecha, $codigo_obra);
                    $num_filas = obtener_num_filas($resultado);
                    if ($num_filas == 0) {

                        $user = $_SESSION["usuario"];


                        $_SESSION['id_usuario'] = obtener_idusu($con, $user);
                        $jefesobra = obtener_jefesobra($con);
                        $encargados = obtener_encargados($con);
                        $obras = obtener_obras($con);
                        $empleados = obtener_empleados($con);
                        $idRegistro = 0;

                        echo "<form method='post' action='modificarmensualtabla.php'>
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
                        echo "<br>No tienes registros para la fecha y obra seleccionadas<br>";
                    } else {
                        echo "Vas a modificar el registro del día $fechaderecha<br>";

                        $id_usuario = $_SESSION['id_usuario'];

                        echo "<form method='post' action='modificarmensualtabla.php'>";
                        echo "<br><table id='tabla4' border='1' style='border-width: 2px; border-style: solid;width: 100%;'>";

                        echo '<tr>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">EMPLEADO</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">HORAS</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">EXT.</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">FEST.</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">BAJA</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">VACAC.</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">F.J.</th>';

                        echo "</tr>";
                        echo "<tr>                
                        
                        ";


                        $ultregistro = obtener_ulregistrocuerpo($con, $id_usuario, $fecha, $codigo_obra);
                        while ($registro = obtener_resultados($ultregistro)) {
                            extract($registro);
                            $id_usuario = $_SESSION['id_usuario'];

                            $valor_registro = explode('|', $registro[0]);
                            $nombre_emp = $registro[1];
                            $apellidos_emp = $registro[2];
                            $empresa_emp = $registro[3];
                            $dni_emp = $registro[4];
                            $tel_emp = $registro[5];
                            $horas = $registro[6];
                            $horas_a_parte = $registro[7];
                            $vacaciones =  $registro[8];
                            $falta =  $registro[9];
                            $festivo =  $registro[10];
                            $baja =  $registro[11];



                            echo "<td>";
                            echo "<select name='datosdelemp[]' class='form-select mb-3'>";
                            echo "<option selected='selected' value='$nombre_emp|$apellidos_emp|$empresa_emp|$dni_emp|$tel_emp'>$nombre_emp $apellidos_emp $empresa_emp $dni_emp $tel_emp</option>";
                            $empleados = obtener_empleados($con);
                            $empleados = obtener_empleados($con);
                            while ($fila = obtener_resultados($empleados)) {
                                $nombre_emp = $fila['nombre_emp'];
                                $apellidos_emp = $fila['apellidos_emp'];
                                $empresa = $fila['empresa'];
                                $dni = $fila['dni'];
                                $tel = $fila['tel'];
                                $valor = "$nombre_emp|$apellidos_emp|$empresa|$dni|$tel";
                                if ($valor !== "$nombre_emp|$apellidos_emp|$empresa_emp|$dni_emp|$tel_emp") {
                                    echo "<option value='$valor'>$nombre_emp $apellidos_emp $empresa $dni $tel</option>";
                                }
                            }
                            echo "</select>
                                    </td>
                                    <td><select name='horas[]' class='form-select mb-3'>
                                    <option selected='selected' value='$horas'>$horas</option>
                                    <option value=8>8</option>
                                    <option value=7>7</option>
                                    <option value=6>6</option>            
                                    <option value=5>5</option>
                                    <option value=4>4</option>
                                    <option value=3>3</option>
                                    <option value=2>2</option>
                                    <option value=1>1</option>
                                    </td>           
                                    <td><select name='horas_a_parte[]' class='form-select mb-3'>
                                    <option selected='selected' value='$horas_a_parte'>$horas_a_parte</option>
                                    <option value=1>1</option>
                                    <option value=2>2</option>
                                    <option value=3>3</option>
                                    <option value=4>4</option>
                                    <option value=5>5</option>
                                    <option value=6>6</option>
                                    <option value=7>7</option>
                                    <option value=8>8</option>
                                    </td>           
                                    <td><select name='festivo[]' class='form-select mb-3'>
                                    <option selected='selected' value='$festivo'>$festivo</option>";
                            if ($festivo === 'sí') {
                                echo '<option value="no">no</option>';
                            } else {
                                echo '<option value="sí">sí</option>';
                            }
                            echo "</td>
                                    <td><select name='baja[]' class='form-select mb-3'>
                                    <option selected='selected' value='$baja'>$baja</option>";
                            if ($baja === 'sí') {
                                echo '<option value="no">no</option>';
                            } else {
                                echo '<option value="sí">sí</option>';
                            }
                            echo "</td> 
                                    <td><select name='vacaciones[]' class='form-select mb-3'>
                                    <option selected='selected' value='$vacaciones'>$vacaciones</option>";
                            if ($vacaciones === 'sí') {
                                echo '<option value="no">no</option>';
                            } else {
                                echo '<option value="sí">sí</option>';
                            }
                            echo "</td> 
                                    <td><select name='falta[]' class='form-select mb-3'>
                                    <option selected='selected' value='$falta'>$falta</option>";
                            if ($falta === 'sí') {
                                echo '<option value="no">no</option>';
                            } else {
                                echo '<option value="sí">sí</option>';
                            }
                            echo "</td>";
                            echo "<td>
                
                                </tr>";
                        }
                        echo "</table>
                        
                                <div class='d-grid gap-2'>
                                <br><input type='submit' name='guardarhorasult' value='MODIFICAR' class='btn btn-danger'/>
                                <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                                    <div class='hidden-text'>Modifica solo lo que quieras cambiar y pulsa MODIFICAR.<br> Deja como está lo que no quieras cambiar.</div>
                                </div>
                                </form>
                                
                                ";
                    }
                }
            }
           

            if (isset($_POST['guardarhorasult'])) {

                if (
                    empty($_POST["datosdelemp"]) || empty($_POST["horas"])
                ) {
                    echo "<br>Debes seleccionar un empleado y las horas.<br>";
                } else if (
                    isset($_POST["datosdelemp"]) && isset($_POST["horas"]) || isset($_POST["horas_a_parte"]) || isset($_POST['festivo']) || isset($_POST['baja']) || isset($_POST['vacaciones']) || isset($_POST['falta'])
                ) {
                    $id_usuario = $_SESSION['id_usuario'];
                    $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $_SESSION['fecha'])));
                    eliminar_registroHCUEfecha($con, $fecha);

                    $cabecera = obtener_porfechacabe($con, $id_usuario, $fecha);
                    while ($registro = obtener_resultados($cabecera)) {
                        extract($registro);

                        $nombre_obra = $registro[2];
                        $codigo_obra = $registro[3];
                        $empresa_obra = $registro[4];
                        $nombre_jo = $registro[5];
                        $apellidos_jo = $registro[6];

                        $nombre_enc = $registro[7];
                        $apellidos_enc = $registro[8];
                        $obtenerprovincia = obtener_provincia($con, $codigo_obra);
                        while ($registro = obtener_resultados($obtenerprovincia)) {
                            extract($registro);
                            $provincia = $registro[0];
                        }
                    }


                    foreach ($_POST["datosdelemp"] as $key => $empleado) {
                        $valor_emp = explode('|', $empleado);
                        $nombre_emp = $valor_emp[0];
                        $apellidos_emp = $valor_emp[1];
                        $empresa_emp = $valor_emp[2];
                        $dni_emp = $valor_emp[3];
                        $tel_emp = $valor_emp[4];

                        $jornada1 = $_POST['horas'][$key];
                        $valor_j1 = explode('|', $jornada1);
                        $horas = $valor_j1[0];

                        $jornada2 = $_POST['horas_a_parte'][$key];
                        $valor_j2 = explode('|', $jornada2);
                        $horas_a_parte = $valor_j2[0];

                        $festivo = $_POST['festivo'][$key] === 'sí' ? 'sí' : 'no';
                        $baja = $_POST['baja'][$key] === 'sí' ? 'sí' : 'no';
                        $vacaciones = $_POST['vacaciones'][$key] === 'sí' ? 'sí' : 'no';
                        $falta = $_POST['falta'][$key] === 'sí' ? 'sí' : 'no';



                        $resultado = guardar_horascuerpo($con, $nombre_emp, $apellidos_emp, $empresa_emp, $dni_emp, $tel_emp, $horas, $horas_a_parte, $vacaciones, $falta, $festivo, $baja, $fecha, $provincia, $nombre_obra, $codigo_obra, $empresa_obra, $nombre_jo, $apellidos_jo, $nombre_enc, $apellidos_enc, $id_usuario);
                        $idRegistro = obtener_id_registro_insertado($con);
                        $_SESSION['idRegistro'] = $idRegistro;
                    }
                    if ($resultado) {

                        echo "</br>Los datos se han modificado correctamente.";

                        

                        echo "<br>
                        <form method='post' action='añadiremp.php'>";
                        $id_usuario = $_SESSION['id_usuario'];
                        echo "<br><table id='tabla4' border='1' style='border-width: 2px; border-style: solid;width: 100%;'>";

                        echo '<tr>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">EMPLEADO</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">HORAS</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">EXT.</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">FEST.</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">BAJA</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">VACAC.</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; ">F.J.</th>';


                        echo "</tr>";
                        echo "<tr>";


                        $ultregistro = obtener_ulregistrocuerpo($con, $id_usuario, $fecha, $codigo_obra);
                        while ($registro = obtener_resultados($ultregistro)) {
                            extract($registro);
                            $id_usuario = $_SESSION['id_usuario'];

                            $valor_registro = explode('|', $registro[0]);
                            $nombre_emp = $registro[1];
                            $apellidos_emp = $registro[2];
                            $empresa_emp = $registro[3];
                            $dni_emp = $registro[4];
                            $tel_emp = $registro[5];
                            $horas = $registro[6];
                            $horas_a_parte = $registro[7];
                            $vacaciones =  $registro[8];
                            $falta =  $registro[9];
                            $festivo =  $registro[10];
                            $baja =  $registro[11];



                            echo "<td style='text-align: center; border: 1px solid gray;'>$nombre_emp $apellidos_emp $empresa_emp $dni_emp $tel_emp </td>
                    <td style='text-align: center; border: 1px solid gray;'>$horas</td>           
                    <td style='text-align: center; border: 1px solid gray;'>$horas_a_parte</td>           
                    <td style='text-align: center; border: 1px solid gray;'>$festivo</td>
                    <td style='text-align: center; border: 1px solid gray;'>$baja</td> 
                    <td style='text-align: center; border: 1px solid gray;'>$vacaciones</td> 
                    <td style='text-align: center; border: 1px solid gray;'>$falta</td>";
                            echo "
                </tr>";
                        }
                        echo "</table>
                    
                    </form>
                    ";
                    } else {
                        echo "</br>Ha habido un error al modificar los datos.";
                    }
                }
            }

                ?>

                <form method='post' action='modificarmensual.php'>
                    <br><br>
                    <p><input type="submit" name="volver" value="VOLVER" class='btn btn-secondary' />

                </form>

        </div>

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