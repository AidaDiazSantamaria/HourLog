<?php
session_start(); //Inicio la sesión
require("database.php"); //Traigo la base de datos
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$con = conectar(); //Conecto

if ($_SESSION['tipo_usuario'] != '2') { //Comprobación de usuario
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
    body,
    html {

        margin: 25px;
        padding: 200px;
    }

    .columna-gris {
        background-color: #ccc;
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
</style>

</head>

<body class="vh-100 d-flex align-items-center justify-content-center bg-light flex-column">
    </div>
    <div class="d-flex align-items-center justify-content-center flex-column" style="width: 70vw;">
        <?php
        if (isset($_POST['mostrar'])) {
            if (empty($_POST['datosmensual']) || empty($_POST['datosdelaobra'])) { //Compruebo que los campos no están vacíos
                echo "<br>Debes escoger un mes y una obra."; //En caso de que lo estén, saco un mensaje por pantalla

            } else if (isset($_POST['datosmensual']) || isset($_POST['datosdelaobra'])) { // Compruebo que estén seteados
                $codigos = $_POST['datosmensual'];
                $_SESSION['datosmensual'] = $codigos;
                $datosobra = $_POST['datosdelaobra'];
                $obra_selec = explode("|", $datosobra[0]);
                $nombre_obra = $obra_selec[0];
                $codigo_obra = $obra_selec[1];
                $empresa_obra = $obra_selec[2];
                $_SESSION['nombre_obra'] = $nombre_obra;
                $_SESSION['codigo_obra'] = $codigo_obra;
                $_SESSION['empresa_obra'] = $empresa_obra;


                foreach ($codigos as $codigo) {
                    $codigos_seleccionados = explode("|", $codigo);
                    $mes = $codigos_seleccionados[0];
                    $año = $codigos_seleccionados[1];
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

                    echo "Mes: $nombresMeses[$mes]<br>";
                    echo "Año: $año<br>";
                    echo "Obra: $nombre_obra $codigo_obra<br/>";


                    $mesAnterior = $mes - 1;
                    if ($mesAnterior == 0) {
                        $mesAnterior = 12;
                    }

                    $consulta = "SELECT nombre_emp, apellidos_emp, empresa_emp, dni_emp, nombre_jo, apellidos_jo, nombre_enc, apellidos_enc, nombre_obra, codigo_obra, empresa_obra, DATE_FORMAT(fecha, '%d') as dia, horas, horas_a_parte, festivo, vacaciones, falta, baja, provincia 
                                    FROM horascuerpo 
                                    WHERE ((MONTH(fecha) = '$mesAnterior' AND DAY(fecha) >= 26) OR (MONTH(fecha) = '$mes' AND DAY(fecha) <= 25)) 
                                    AND YEAR(fecha) = '$año' 
                                    AND codigo_obra = '$codigo_obra'
                                    ORDER BY fecha";


                    $resultado = mysqli_query($con, $consulta);
                    if (mysqli_num_rows($resultado) == 0) {
                        echo "<br>No tienes registros para la fecha y obra seleccionadas<br>";
                    } else {
                        $empleados = array();
                        while ($row = mysqli_fetch_assoc($resultado)) {
                            $nombre = $row['nombre_emp'];
                            $fecha = $row['dia'];
                            $horas = $row['horas'];

                            if (!isset($empleados[$nombre])) {
                                $empleados[$nombre] = array();
                            }
                            if (!isset($empleados[$nombre][$fecha])) {
                                $empleados[$nombre][$fecha] = array(
                                    'horas' => 0,
                                    'horas_a_parte' => 0,
                                    'festivo' => '',
                                    'vacaciones' => '',
                                    'falta' => '',
                                    'baja' => '',
                                    'provincia' => '',
                                    'apellidos_emp' => '',
                                    'empresa_emp' => '',
                                    'dni_emp' => ''
                                );
                            }
                            $empleados[$nombre][$fecha]['horas'] += $horas; // Sumar las horas trabajadas al día correspondiente para el empleado
                            $empleados[$nombre][$fecha]['horas_a_parte'] = $row['horas_a_parte']; // Obtener las horas a parte para el día correspondiente
                            $empleados[$nombre][$fecha]['festivo'] = $row['festivo']; // Obtener el valor de festivo para el día correspondiente
                            $empleados[$nombre][$fecha]['vacaciones'] = $row['vacaciones']; // Obtener el valor de vacaciones para el día correspondiente
                            $empleados[$nombre][$fecha]['falta'] = $row['falta']; // Obtener el valor de falta para el día correspondiente
                            $empleados[$nombre][$fecha]['baja'] = $row['baja']; // Obtener el valor de baja para el día correspondiente
                            $empleados[$nombre][$fecha]['provincia'] = $row['provincia']; // Obtener el valor de provincia para el día correspondiente
                            $empleados[$nombre][$fecha]['apellidos_emp'] = $row['apellidos_emp']; // Obtener los apellidos del empleado
                            $empleados[$nombre][$fecha]['empresa_emp'] = $row['empresa_emp']; // Obtener el nombre de la empresa del empleado
                            $empleados[$nombre][$fecha]['dni_emp'] = $row['dni_emp']; // Obtener el DNI del empleado
                        }

                        echo '<table border="1" style="border-width: 2px; border-style: solid; width: 100%;">';

                        echo '<tr>';
                        echo '<th style="text-align: center; border: 1px solid gray; border-bottom-width: 3px;">EMPLEADO</th>';
                        // Obtener el último día del mes anterior
                        $ultimoDiaAnterior = cal_days_in_month(CAL_GREGORIAN, $mesAnterior, $año);

                        // Generar las columnas para los días del mes
                        for ($i = 26; $i <= $ultimoDiaAnterior; $i++) {
                            $diaSemana = date('N', strtotime("$año-$mesAnterior-$i")); // Obtener el número de día de la semana (1: lunes, 7: domingo)
                            $columnClass = $diaSemana > 5 ? 'columna-gris' : ''; // Agregar clase CSS para días sábado y domingo

                            echo "<th class='columna-encabezado $columnClass' style='width:30px; text-align: center; border: 1px solid gray; border-collapse: collapse; padding: 5px;'>$i</th>";
                        }

                        // Obtener el último día del mes actual
                        $ultimoDiaActual = cal_days_in_month(CAL_GREGORIAN, $mes, $año);

                        // Generar las columnas para los días del mes actual
                        for ($i = 1; $i <= 25; $i++) {
                            $diaSemana = date('N', strtotime("$año-$mes-$i")); // Obtener el número de día de la semana (1: lunes, 7: domingo)
                            $columnClass = $diaSemana > 5 ? 'columna-gris' : ''; // Agregar clase CSS para días sábado y domingo

                            echo "<th class='columna-encabezado $columnClass' style='width:30px; text-align: center; border: 1px solid gray; border-collapse: collapse; padding: 5px;'>$i</th>";
                        }
                        echo '<th style="text-align: center; border: 1px solid gray; border-bottom-width: 3px;">HORAS</th>';
                        echo '<th style="text-align: center; border: 1px solid gray; border-bottom-width: 3px;">TOTAL</th>';

                        echo '</tr>';
                        $secuenciaHoras = array('Convenio', 'Trabajadas', 'Ordinarias', 'Extraord.', 'Festivas');
                        foreach ($empleados as $nombre => $fechas) {
                            $horas_totales = 0;
                            $horas_a_parte_totales = 0;
                            $horas_festivas_totales = 0;
                            $horas_trabajadas = 0;
                            $horas_trabajadas_totales = 0;
                            $horas_convenio_totales = 0;


                            // Generar las filas para cada empleado
                            for ($j = 0; $j < 6; $j++) {
                                echo '<tr>';

                                if ($j === 0) {
                                    echo "<td rowspan='6' style='border: 1px solid gray; border-bottom-width: 3px; padding: 8px; text-align: center; vertical-align: middle;'>$nombre {$fechas[$fecha]['apellidos_emp']} {$fechas[$fecha]['dni_emp']} {$fechas[$fecha]['empresa_emp']}</td>";
                                } elseif ($j === 1) {
                                    for ($k = 26; $k <= $ultimoDiaAnterior; $k++) {
                                        $dia = str_pad($k, 2, "0", STR_PAD_LEFT); // Agregar ceros a la izquierda si es necesario
                                        $diaSemana = date('N', strtotime("$año-$mesAnterior-$dia")); // Obtener el número de día de la semana (1: lunes, 7: domingo)
                                        $festivo = isset($fechas[$dia]['festivo']) ? $fechas[$dia]['festivo'] : 'no'; // Verificar si la fecha es festiva
                                        $horasconvenio = $festivo === 'sí' || $diaSemana > 5 ? 0 : 8; // Establecer horas de convenio (0 para festivos y días sábado y domingo)

                                        echo "<td style='border: 1px solid gray; border-top-width: 3px; padding: 8px; text-align: center; vertical-align: middle;'>$horasconvenio</td>";
                                        $horas_convenio_totales += $horasconvenio;
                                    }

                                    for ($k = 1; $k <= 25; $k++) {
                                        $dia = str_pad($k, 2, "0", STR_PAD_LEFT); // Agregar ceros a la izquierda si es necesario
                                        $diaSemana = date('N', strtotime("$año-$mes-$dia")); // Obtener el número de día de la semana (1: lunes, 7: domingo)
                                        $festivo = isset($fechas[$dia]['festivo']) ? $fechas[$dia]['festivo'] : 'no'; // Verificar si la fecha es festiva
                                        $horasconvenio = $festivo === 'sí' || $diaSemana > 5 ? 0 : 8; // Establecer horas de convenio (0 para festivos y días sábado y domingo)

                                        echo "<td style='border: 1px solid gray; border-top-width: 3px; padding: 8px; text-align: center; vertical-align: middle;'>$horasconvenio</td>";
                                        $horas_convenio_totales += $horasconvenio;
                                    }
                                    echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>Convenio</td>";
                                    echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>$horas_convenio_totales</td>";
                                } elseif ($j === 2) {
                                    for ($k = 26; $k <= $ultimoDiaAnterior; $k++) {
                                        $dia = str_pad($k, 2, "0", STR_PAD_LEFT); // Agregar ceros a la izquierda si es necesario
                                        $horas = 0;
                                        $horas_a_parte = 0;
                                        $horas_festivas = 0;
                                        $horas_trabajadas = 0;


                                        if (isset($fechas[$dia])) {
                                            $horas = $fechas[$dia]['horas'];
                                            $horas_a_parte = $fechas[$dia]['horas_a_parte'];
                                            $festivo = $fechas[$dia]['festivo'];
                                            $vacaciones = $fechas[$dia]['vacaciones'];
                                            $falta = $fechas[$dia]['falta'];
                                            $baja = $fechas[$dia]['baja'];

                                            if ($festivo == 'sí') {
                                                $horas = 0; // Establecer las horas a 0 en días festivos
                                                $horas_a_parte = 0; // Establecer las horas a parte a 0 en días festivos
                                                $horas_festivas = $horas + $horas_a_parte; // Calcular las horas festivas (suma de horas y horas a parte)
                                            }

                                            if ($vacaciones == 'sí') {
                                                $horas_trabajadas = "V"; // Marcar como vacaciones
                                            } elseif ($baja == 'sí') {
                                                $horas_trabajadas = "BM"; // Marcar como baja
                                            } elseif ($falta == 'sí') {
                                                $horas_trabajadas = "FJ"; // Marcar como falta
                                            } else {
                                                $horas_trabajadas = $horas + $horas_a_parte; // Calcular las horas trabajadas (suma de horas y horas a parte)
                                                $horas_trabajadas_totales += $horas_trabajadas;
                                            }
                                        }

                                        echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>$horas_trabajadas</td>";
                                    }
                                    for ($k = 1; $k <= 25; $k++) {
                                        $dia = str_pad($k, 2, "0", STR_PAD_LEFT); // Agregar ceros a la izquierda si es necesario
                                        $horas = 0;
                                        $horas_a_parte = 0;
                                        $horas_festivas = 0;
                                        $horas_trabajadas = 0;


                                        if (isset($fechas[$dia])) {
                                            $horas = $fechas[$dia]['horas'];
                                            $horas_a_parte = $fechas[$dia]['horas_a_parte'];
                                            $festivo = $fechas[$dia]['festivo'];
                                            $vacaciones = $fechas[$dia]['vacaciones'];
                                            $falta = $fechas[$dia]['falta'];
                                            $baja = $fechas[$dia]['baja'];

                                            if ($festivo == 'sí') {
                                                $horas = 0; // Establecer las horas a 0 en días festivos
                                                $horas_a_parte = 0; // Establecer las horas a parte a 0 en días festivos
                                                $horas_festivas = $horas + $horas_a_parte; // Calcular las horas festivas (suma de horas y horas a parte)
                                            }

                                            if ($vacaciones == 'sí') {
                                                $horas_trabajadas = "V"; // Marcar como vacaciones
                                            } elseif ($baja == 'sí') {
                                                $horas_trabajadas = "BM"; // Marcar como baja
                                            } elseif ($falta == 'sí') {
                                                $horas_trabajadas = "FJ"; // Marcar como falta
                                            } else {
                                                $horas_trabajadas = $horas + $horas_a_parte; // Calcular las horas trabajadas (suma de horas y horas a parte)
                                                $horas_trabajadas_totales += $horas_trabajadas;
                                            }
                                        }

                                        echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>$horas_trabajadas</td>";
                                    }
                                    echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>Trabajadas</td>";
                                    echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>$horas_trabajadas_totales</td>";
                                } elseif ($j === 3) {
                                    for ($k = 26; $k <= $ultimoDiaAnterior; $k++) {
                                        $dia = str_pad($k, 2, "0", STR_PAD_LEFT); // Agregar ceros a la izquierda si es necesario
                                        $horas = 0;

                                        if (isset($fechas[$dia])) {
                                            $festivo = $fechas[$dia]['festivo'];

                                            if ($festivo == 'sí') {
                                                $horas = 0; // Establecer las horas a 0 en días festivos
                                            } else {
                                                $horas = $fechas[$dia]['horas'];
                                            }
                                        }

                                        $horas_totales += $horas; // Sumar las horas
                                        echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>$horas</td>";
                                    }

                                    for ($k = 1; $k <= 25; $k++) {
                                        $dia = str_pad($k, 2, "0", STR_PAD_LEFT); // Agregar ceros a la izquierda si es necesario
                                        $horas = 0;

                                        if (isset($fechas[$dia])) {
                                            $festivo = $fechas[$dia]['festivo'];

                                            if ($festivo == 'sí') {
                                                $horas = 0; // Establecer las horas a 0 en días festivos
                                            } else {
                                                $horas = $fechas[$dia]['horas'];
                                            }
                                        }

                                        $horas_totales += $horas; // Sumar las horas
                                        echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>$horas</td>";
                                    }
                                    echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>Ordinarias</td>";
                                    echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>$horas_totales</td>";
                                } elseif ($j === 4) {
                                    for ($k = 26; $k <= $ultimoDiaAnterior; $k++) {
                                        $dia = str_pad($k, 2, "0", STR_PAD_LEFT); // Agregar ceros a la izquierda si es necesario
                                        $horas_a_parte = 0;

                                        if (isset($fechas[$dia])) {
                                            $festivo = $fechas[$dia]['festivo'];

                                            if ($festivo == 'sí') {
                                                $horas_a_parte = 0; // Establecer las horas a parte a 0 en días festivos
                                            } else {
                                                $horas_a_parte = $fechas[$dia]['horas_a_parte'];
                                            }
                                        }

                                        $horas_a_parte_totales += $horas_a_parte; // Sumar las horas a parte
                                        echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>$horas_a_parte</td>";
                                    }

                                    for ($k = 1; $k <= 25; $k++) {
                                        $dia = str_pad($k, 2, "0", STR_PAD_LEFT); // Agregar ceros a la izquierda si es necesario
                                        $horas_a_parte = 0;

                                        if (isset($fechas[$dia])) {
                                            $festivo = $fechas[$dia]['festivo'];

                                            if ($festivo == 'sí') {
                                                $horas_a_parte = 0; // Establecer las horas a parte a 0 en días festivos
                                            } else {
                                                $horas_a_parte = $fechas[$dia]['horas_a_parte'];
                                            }
                                        }

                                        $horas_a_parte_totales += $horas_a_parte; // Sumar las horas a parte
                                        echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>$horas_a_parte</td>";
                                    }
                                    echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>Extraordinarias</td>";
                                    echo "<td style='border: 1px solid gray; padding: 8px; text-align: center; vertical-align: middle;'>$horas_a_parte_totales</td>";
                                } elseif ($j === 5) {
                                    for ($k = 26; $k <= $ultimoDiaAnterior; $k++) {
                                        $dia = str_pad($k, 2, "0", STR_PAD_LEFT); // Agregar ceros a la izquierda si es necesario
                                        $horas_festivas = 0;

                                        if (isset($fechas[$dia])) {
                                            $festivo = $fechas[$dia]['festivo'];

                                            if ($festivo == 'sí') {
                                                $horas = 0; // Establecer las horas a 0 en días festivos
                                                $horas_a_parte = 0; // Establecer las horas a parte a 0 en días festivos
                                                $horas_festivas = $fechas[$dia]['horas'] + $fechas[$dia]['horas_a_parte'];
                                            }
                                        }

                                        $horas_festivas_totales += $horas_festivas; // Sumar las horas festivas
                                        echo "<td style='border: 1px solid gray; border-bottom-width: 3px; padding: 8px; text-align: center; vertical-align: middle;'>$horas_festivas</td>";
                                    }
                                    for ($k = 1; $k <= 25; $k++) {
                                        $dia = str_pad($k, 2, "0", STR_PAD_LEFT); // Agregar ceros a la izquierda si es necesario
                                        $horas_festivas = 0;

                                        if (isset($fechas[$dia])) {
                                            $festivo = $fechas[$dia]['festivo'];

                                            if ($festivo == 'sí') {
                                                $horas = 0; // Establecer las horas a 0 en días festivos
                                                $horas_a_parte = 0; // Establecer las horas a parte a 0 en días festivos
                                                $horas_festivas = $fechas[$dia]['horas'] + $fechas[$dia]['horas_a_parte'];
                                            }
                                        }

                                        $horas_festivas_totales += $horas_festivas; // Sumar las horas festivas
                                        echo "<td style='border: 1px solid gray; border-bottom-width: 3px; padding: 8px; text-align: center; vertical-align: middle;'>$horas_festivas</td>";
                                    }
                                    echo "<td style='border: 1px solid gray; border-bottom-width: 3px; padding: 8px; text-align: center; vertical-align: middle;'>Festivas</td>";

                                    echo "<td style='border: 1px solid gray; border-bottom-width: 3px; padding: 8px; text-align: center; vertical-align: middle;'>$horas_festivas_totales</td>";
                                }

                                echo '</tr>';
                            }
                        }


                        echo '</table>';
                    

                    echo "<form action='registromensualtabla.php' method='post'>";
                    echo "<br>";
                    echo "<br>";
                    echo "
                <div class='d-grid gap-2'>
                <input type='submit' name='exportar' value='EXPORTAR' class='btn btn-success'/>
                <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                <div class='hidden-text'>Pulsa aquí para EXPORTAR a Excel.</div>
                </div>";
                    echo "</form>";
                    }
                }
            }
        }

        if (isset($_POST['exportar'])) {

            $spreadsheet3 = new Spreadsheet();
            $sheet = $spreadsheet3->getActiveSheet();

            $codigos = $_SESSION['datosmensual'];
            $nombre_obra = $_SESSION['nombre_obra'];
            $codigo_obra = $_SESSION['codigo_obra'];
            $empresa_obra = $_SESSION['empresa_obra'];

            foreach ($codigos as $codigo) {
                $codigos_seleccionados = explode("|", $codigo);
                $mes = $codigos_seleccionados[0];
                $año = $codigos_seleccionados[1];
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


                $mesAnterior = $mes - 1;
                if ($mesAnterior == 0) {
                    $mesAnterior = 12;
                }

                $consulta = "SELECT nombre_emp, apellidos_emp, empresa_emp, dni_emp, nombre_jo, apellidos_jo, nombre_enc, apellidos_enc, nombre_obra, codigo_obra, empresa_obra, DATE_FORMAT(fecha, '%d') as dia, horas, horas_a_parte, festivo, vacaciones, falta, baja, provincia 
                                    FROM horascuerpo 
                                    WHERE ((MONTH(fecha) = '$mesAnterior' AND DAY(fecha) >= 26) OR (MONTH(fecha) = '$mes' AND DAY(fecha) <= 25)) 
                                    AND YEAR(fecha) = '$año' 
                                    AND codigo_obra = '$codigo_obra'
                                    ORDER BY fecha";

                $resultado = mysqli_query($con, $consulta);

                $empleados = array();
                while ($row = mysqli_fetch_assoc($resultado)) {
                    $nombre = $row['nombre_emp'];
                    $fecha = $row['dia'];
                    $horas = $row['horas'];

                    if (!isset($empleados[$nombre])) {
                        $empleados[$nombre] = array();
                    }
                    if (!isset($empleados[$nombre][$fecha])) {
                        $empleados[$nombre][$fecha] = array(
                            'horas' => 0,
                            'horas_a_parte' => 0,
                            'festivo' => '',
                            'vacaciones' => '',
                            'falta' => '',
                            'baja' => '',
                            'provincia' => '',
                            'apellidos_emp' => '',
                            'empresa_emp' => '',
                            'dni_emp' => '',
                            'nombre_jo' => '',
                            'apellidos_jo' => '',
                            'nombre_enc' => '',
                            'apellidos_enc' => ''
                        );
                    }
                    $empleados[$nombre][$fecha]['horas'] += $horas; // Sumar las horas trabajadas al día correspondiente para el empleado
                    $empleados[$nombre][$fecha]['horas_a_parte'] = $row['horas_a_parte']; // Obtener las horas a parte para el día correspondiente
                    $empleados[$nombre][$fecha]['festivo'] = $row['festivo']; // Obtener el valor de festivo para el día correspondiente
                    $empleados[$nombre][$fecha]['vacaciones'] = $row['vacaciones']; // Obtener el valor de vacaciones para el día correspondiente
                    $empleados[$nombre][$fecha]['falta'] = $row['falta']; // Obtener el valor de falta para el día correspondiente
                    $empleados[$nombre][$fecha]['baja'] = $row['baja'];
                    $empleados[$nombre][$fecha]['provincia'] = $row['provincia']; // Obtener la provincia para el día correspondiente
                    $empleados[$nombre][$fecha]['apellidos_emp'] = $row['apellidos_emp']; // Obtener los apellidos del empleado
                    $empleados[$nombre][$fecha]['empresa_emp'] = $row['empresa_emp']; // Obtener la empresa del empleado
                    $empleados[$nombre][$fecha]['dni_emp'] = $row['dni_emp']; // Obtener el DNI del empleado
                    $empleados[$nombre][$fecha]['nombre_jo'] = $row['nombre_jo']; // Obtener el nombre del jefe de obra
                    $empleados[$nombre][$fecha]['apellidos_jo'] = $row['apellidos_jo']; // Obtener los apellidos del jefe de obra
                    $empleados[$nombre][$fecha]['nombre_enc'] = $row['nombre_enc']; // Obtener el nombre del encargado
                    $empleados[$nombre][$fecha]['apellidos_enc'] = $row['apellidos_enc']; // Obtener los apellidos del encargado

                }

                $sheet->setCellValue('AJ1', "Mes: " . $nombresMeses[$mes]);
                $sheet->setCellValue('AJ2', "Año: " . $año);
                $sheet->setCellValue('AJ3', "Jefe de obra: " . $empleados[$nombre][$fecha]['nombre_jo'] . "  " . $empleados[$nombre][$fecha]['apellidos_jo']);
                $sheet->setCellValue('AJ4', "Encargado: " . $empleados[$nombre][$fecha]['nombre_enc'] . "  " . $empleados[$nombre][$fecha]['apellidos_enc']);
                $sheet->setCellValue('AJ5', "Obra: " . $nombre_obra . " " . $codigo_obra . " " . $empresa_obra);


                $columnTitles = ['EMPLEADO'];

                $ultimoDia = cal_days_in_month(CAL_GREGORIAN, $mesAnterior, $año);

                // Generar las columnas para los días del mes
                for ($i = 26; $i <= $ultimoDia; $i++) {
                    $diaSemana = date('N', strtotime("$año-$mesAnterior-$i")); // Obtener el número de día de la semana (1: lunes, 7: domingo)
                    $columnTitles[] = $i;
                }

                // Obtener el último día del mes actual
                $ultimoDiaActual = cal_days_in_month(CAL_GREGORIAN, $mes, $año);

                // Generar las columnas para los días del mes actual
                for ($i = 1; $i <= 25; $i++) {
                    $diaSemana = date('N', strtotime("$año-$mes-$i")); // Obtener el número de día de la semana (1: lunes, 7: domingo)
                    $columnTitles[] = $i;
                }

                $columnTitles[] = 'HORAS';
                $columnTitles[] = 'TOTAL';

                $row = 1;
                foreach ($columnTitles as $index => $title) {
                    $cell = Coordinate::stringFromColumnIndex($index + 1) . $row;
                    $sheet->setCellValue($cell, $title);
                    // Agregar borde exterior a las celdas
                    $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
                    // Ajustar el ancho de la columna "HORAS"
                    if ($title === 'HORAS') {
                        $columnIndex = Coordinate::stringFromColumnIndex($index + 1);
                        $columnDimension = $sheet->getColumnDimension($columnIndex);
                        $columnDimension->setAutoSize(true);
                    }
                }




                $row = 2;
                $secuenciaHoras = ['Convenio', 'Trabajadas', 'Ordinarias', 'Extraord.', 'Festivas'];

                foreach ($empleados as $nombre => $fechas) {
                    $horas_totales = 0;
                    $horas_a_parte_totales = 0;
                    $horas_festivas_totales = 0;
                    $horas_trabajadas_totales = 0;
                    $horas_convenio_totales = 0;

                    // Generar las filas para cada empleado
                    for ($j = 0; $j < 6; $j++) {
                        $column = 1;

                        if ($j === 0) {
                            $startCell = Coordinate::stringFromColumnIndex($column) . $row;
                            $endCell = Coordinate::stringFromColumnIndex($column) . ($row + 4);
                            $sheet->mergeCells("$startCell:$endCell");

                            $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                            $sheet->setCellValue($cellReference, "$nombre {$fechas[$fecha]['apellidos_emp']} {$fechas[$fecha]['dni_emp']} {$fechas[$fecha]['empresa_emp']}");


                            // Incrementar el valor de $row para ocupar 5 filas
                            $row += 4;
                        } elseif ($j === 1) {
                            $column++; // Mover una columna hacia la derecha
                            $row -= 5; // Subir cinco filas
                            for ($k = 26; $k <= $ultimoDia; $k++) {
                                $dia = str_pad($k, 2, "0", STR_PAD_LEFT); // Agregar ceros a la izquierda si es necesario
                                $diaSemana = date('N', strtotime("$año-$mesAnterior-$dia")); // Obtener el número de día de la semana (1: lunes, 7: domingo)
                                $festivo = isset($fechas[$dia]['festivo']) ? $fechas[$dia]['festivo'] : 'no'; // Verificar si la fecha es festiva
                                $horasconvenio = $festivo === 'sí' || $diaSemana > 5 ? 0 : 8; // Establecer horas de convenio (0 para festivos y días sábado y domingo)

                                $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                                $sheet->setCellValue($cellReference, $horasconvenio);
                                $horas_convenio_totales += $horasconvenio;

                                // Verificar si el día es sábado o domingo
                                if ($diaSemana == 6 || $diaSemana == 7) {
                                    // Calcular el rango de celdas para la columna
                                    $rangoCeldas = Coordinate::stringFromColumnIndex($column) . '1:' . Coordinate::stringFromColumnIndex($column) . $sheet->getHighestRow();

                                    // Configurar el estilo de relleno para el rango de celdas
                                    $style = $sheet->getStyle($rangoCeldas);
                                    $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
                                }

                                $column++;
                            }

                            for ($k = 1; $k <= 25; $k++) {
                                $dia = str_pad($k, 2, "0", STR_PAD_LEFT); // Agregar ceros a la izquierda si es necesario
                                $diaSemana = date('N', strtotime("$año-$mes-$dia")); // Obtener el número de día de la semana (1: lunes, 7: domingo)
                                $festivo = isset($fechas[$dia]['festivo']) ? $fechas[$dia]['festivo'] : 'no'; // Verificar si la fecha es festiva
                                $horasconvenio = $festivo === 'sí' ? 0 : ($diaSemana > 5 ? 0 : 8);
                                $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                                $sheet->setCellValue($cellReference, $horasconvenio);
                                $horas_convenio_totales += $horasconvenio;

                                // Verificar si el día es sábado o domingo
                                if ($diaSemana == 6 || $diaSemana == 7) {
                                    // Calcular el rango de celdas para la columna
                                    $rangoCeldas = Coordinate::stringFromColumnIndex($column) . '1:' . Coordinate::stringFromColumnIndex($column) . $sheet->getHighestRow();

                                    // Configurar el estilo de relleno para el rango de celdas
                                    $style = $sheet->getStyle($rangoCeldas);
                                    $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
                                }

                                $column++;
                            }

                            $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                            $sheet->setCellValue($cellReference, 'Convenio');
                            $column++;
                            $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                            $sheet->setCellValue($cellReference, $horas_convenio_totales);
                            $column++;
                        } elseif ($j === 2) {
                            $column++; // Mover una columna hacia la derecha
                            for ($k = 26; $k <= $ultimoDia; $k++) {
                                $dia = str_pad($k, 2, "0", STR_PAD_LEFT);
                                $horas = 0;
                                $horas_a_parte = 0;
                                $horas_festivas = 0;
                                $horas_trabajadas = 0;

                                if (isset($fechas[$dia])) {
                                    $horas = $fechas[$dia]['horas'];
                                    $horas_a_parte = $fechas[$dia]['horas_a_parte'];
                                    $festivo = $fechas[$dia]['festivo'];
                                    $vacaciones = $fechas[$dia]['vacaciones'];
                                    $falta = $fechas[$dia]['falta'];
                                    $baja = $fechas[$dia]['baja'];

                                    if ($festivo == 'sí') {
                                        $horas = 0;
                                        $horas_a_parte = 0;
                                        $horas_festivas = $horas + $horas_a_parte;
                                    }

                                    if ($vacaciones == 'sí') {
                                        $horas_trabajadas = "V";
                                    } elseif ($baja == 'sí') {
                                        $horas_trabajadas = "BM";
                                    } elseif ($falta == 'sí') {
                                        $horas_trabajadas = "FJ";
                                    } else {
                                        $horas_trabajadas = $horas + $horas_a_parte;
                                        $horas_trabajadas_totales += $horas_trabajadas;
                                    }
                                }

                                $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                                $sheet->setCellValue($cellReference, $horas_trabajadas);
                                $column++;
                            }

                            for ($k = 1; $k <= 25; $k++) {
                                $dia = str_pad($k, 2, "0", STR_PAD_LEFT);
                                $horas = 0;
                                $horas_a_parte = 0;
                                $horas_festivas = 0;
                                $horas_trabajadas = 0;

                                if (isset($fechas[$dia])) {
                                    $horas = $fechas[$dia]['horas'];
                                    $horas_a_parte = $fechas[$dia]['horas_a_parte'];
                                    $festivo = $fechas[$dia]['festivo'];
                                    $vacaciones = $fechas[$dia]['vacaciones'];
                                    $falta = $fechas[$dia]['falta'];
                                    $baja = $fechas[$dia]['baja'];

                                    if ($festivo == 'sí') {
                                        $horas = 0;
                                        $horas_a_parte = 0;
                                        $horas_festivas = $horas + $horas_a_parte;
                                    }

                                    if ($vacaciones == 'sí') {
                                        $horas_trabajadas = "V";
                                    } elseif ($baja == 'sí') {
                                        $horas_trabajadas = "BM";
                                    } elseif ($falta == 'sí') {
                                        $horas_trabajadas = "FJ";
                                    } else {
                                        $horas_trabajadas = $horas + $horas_a_parte;
                                        $horas_trabajadas_totales += $horas_trabajadas;
                                    }
                                }

                                $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                                $sheet->setCellValue($cellReference, $horas_trabajadas);
                                $column++;
                            }

                            $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                            $sheet->setCellValue($cellReference, 'Trabajadas');
                            $column++;
                            $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                            $sheet->setCellValue($cellReference, $horas_trabajadas_totales);
                            $column++;
                        } elseif ($j === 3) {
                            $column++; // Mover una columna hacia la derecha
                            for ($k = 26; $k <= $ultimoDia; $k++) {
                                $dia = str_pad($k, 2, "0", STR_PAD_LEFT);
                                $horas = 0;

                                if (isset($fechas[$dia])) {
                                    $festivo = $fechas[$dia]['festivo'];

                                    if ($festivo == 'sí') {
                                        $horas = 0;
                                    } else {
                                        $horas = $fechas[$dia]['horas'];
                                    }
                                }

                                $horas_totales += $horas;
                                $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                                $sheet->setCellValue($cellReference, $horas);
                                $column++;
                            }

                            for ($k = 1; $k <= 25; $k++) {
                                $dia = str_pad($k, 2, "0", STR_PAD_LEFT);
                                $horas = 0;

                                if (isset($fechas[$dia])) {
                                    $festivo = $fechas[$dia]['festivo'];

                                    if ($festivo == 'sí') {
                                        $horas = 0;
                                    } else {
                                        $horas = $fechas[$dia]['horas'];
                                    }
                                }

                                $horas_totales += $horas;
                                $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                                $sheet->setCellValue($cellReference, $horas);
                                $column++;
                            }

                            $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                            $sheet->setCellValue($cellReference, 'Ordinarias');
                            $column++;
                            $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                            $sheet->setCellValue($cellReference, $horas_totales);
                            $column++;
                        } elseif ($j === 4) {
                            $column++; // Mover una columna hacia la derecha
                            for ($k = 26; $k <= $ultimoDia; $k++) {
                                $dia = str_pad($k, 2, "0", STR_PAD_LEFT);
                                $horas_a_parte = 0;

                                if (isset($fechas[$dia])) {
                                    $festivo = $fechas[$dia]['festivo'];

                                    if ($festivo == 'sí') {
                                        $horas_a_parte = 0;
                                    } else {
                                        $horas_a_parte = $fechas[$dia]['horas_a_parte'];
                                    }
                                }

                                $horas_a_parte_totales += $horas_a_parte;
                                $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                                $sheet->setCellValue($cellReference, $horas_a_parte);
                                $column++;
                            }

                            for ($k = 1; $k <= 25; $k++) {
                                $dia = str_pad($k, 2, "0", STR_PAD_LEFT);
                                $horas_a_parte = 0;

                                if (isset($fechas[$dia])) {
                                    $festivo = $fechas[$dia]['festivo'];

                                    if ($festivo == 'sí') {
                                        $horas_a_parte = 0;
                                    } else {
                                        $horas_a_parte = $fechas[$dia]['horas_a_parte'];
                                    }
                                }

                                $horas_a_parte_totales += $horas_a_parte;
                                $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                                $sheet->setCellValue($cellReference, $horas_a_parte);
                                $column++;
                            }

                            $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                            $sheet->setCellValue($cellReference, 'Extraordinarias');
                            $column++;
                            $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                            $sheet->setCellValue($cellReference, $horas_a_parte_totales);
                            $column++;
                        } elseif ($j === 5) {
                            $column++; // Mover una columna hacia la derecha
                            for ($k = 26; $k <= $ultimoDia; $k++) {
                                $dia = str_pad($k, 2, "0", STR_PAD_LEFT);
                                $horas_festivas = 0;

                                if (isset($fechas[$dia])) {
                                    $festivo = $fechas[$dia]['festivo'];

                                    if ($festivo == 'sí') {
                                        $horas = 0;
                                        $horas_a_parte = 0;
                                        $horas_festivas = $fechas[$dia]['horas'] + $fechas[$dia]['horas_a_parte'];
                                    }
                                }

                                $horas_festivas_totales += $horas_festivas;
                                $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                                $sheet->setCellValue($cellReference, $horas_festivas);
                                $column++;
                            }

                            for ($k = 1; $k <= 25; $k++) {
                                $dia = str_pad($k, 2, "0", STR_PAD_LEFT);
                                $horas_festivas = 0;

                                if (isset($fechas[$dia])) {
                                    $festivo = $fechas[$dia]['festivo'];

                                    if ($festivo == 'sí') {
                                        $horas = 0;
                                        $horas_a_parte = 0;
                                        $horas_festivas = $fechas[$dia]['horas'] + $fechas[$dia]['horas_a_parte'];
                                    }
                                }

                                $horas_festivas_totales += $horas_festivas;
                                $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                                $sheet->setCellValue($cellReference, $horas_festivas);
                                $column++;
                            }

                            $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                            $sheet->setCellValue($cellReference, 'Festivas');
                            $column++;
                            $cellReference = Coordinate::stringFromColumnIndex($column) . $row;
                            $sheet->setCellValue($cellReference, $horas_festivas_totales);
                            $column++;
                        }

                        $row++;

                        // // Establecer el borde que abarque desde la fila 2 hasta la fila final
                        $range = 'A2:' . Coordinate::stringFromColumnIndex(count($columnTitles)) . $row;

                        // Establecer la alineación centrada para todas las celdas en la columna A
                        $columns = 'A:AH';
                        $sheet->getStyle($columns)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle($cellReference)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                        // Ajustar automáticamente el ancho de la columna EMPLEADO a partir de A2
                        $column = 'A';
                        $lastRow = $sheet->getHighestRow();
                        $lastColumn = $sheet->getHighestColumn();
                        $sheet->getColumnDimension($column)->setAutoSize(true);

                        // Obtener el último índice de fila con contenido para cada columna
                        $lastRowIndex = $row - 1;

                        // Establecer el borde vertical para cada celda individualmente
                        for ($col = 1; $col <= count($columnTitles); $col++) {
                            $cellReference = Coordinate::stringFromColumnIndex($col) . '2:' . Coordinate::stringFromColumnIndex($col) . $lastRowIndex;
                            $sheet->getStyle($cellReference)->getBorders()->getRight()->setBorderStyle(Border::BORDER_MEDIUM);
                        }

                        // Verificar si se debe agregar un borde grueso
                        if (($row - 1) % 5 === 0) {
                            // Establecer un borde grueso en la parte inferior
                            $sheet->getStyle($range)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
                        }
                    }
                }

                // Definir el nombre del archivo de descarga
                $filename = "HORAS_$nombresMeses[$mes]_$año$nombre_obra$codigo_obra$empresa_obra.xlsx";

                // Configurar las cabeceras para la descarga del archivo
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');

                // Limpiar el búfer de salida
                ob_clean();

                // Enviar el contenido del archivo al navegador
                $writer = new Xlsx($spreadsheet3);
                $writer->save('php://output');

                // Detener la ejecución del script
                exit();
            }

            // Limpia los datos de la sesión
            unset($_SESSION['datosmensual']);
        }
        ?>
    </div>
    <form action="registromensual.php" method="post">
        <br /><input type="submit" name="volver_index" value="VOLVER" class="btn btn-secondary">
    </form>
    <script>
        $(document).ready(function() {
            $('.columna-encabezado').each(function() {
                if ($(this).hasClass('columna-gris')) {
                    var columnIndex = $(this).index();
                    $('table tr td:nth-child(' + (columnIndex) + ')').addClass('columna-gris');
                }
            });

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