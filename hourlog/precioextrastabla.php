<?php
session_start(); //Inicio la sesión
require("database.php"); //Traigo la base de datos
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


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
        <div class="d-flex align-items-center justify-content-center flex-column">

            <?php
            if (isset($_POST['mostrar'])) {
            }
            if (isset($_POST['datosmensual'])) {
                $codigos = $_POST['datosmensual'];
                $_SESSION['datosmensual'] = $codigos;


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
                    echo "Año: $año<br/>";

                    $mesAnterior = $mes - 1;
                    if ($mesAnterior == 0) {
                        $mesAnterior = 12;
                    }

                    $consulta = "SELECT nombre_emp, apellidos_emp, empresa_emp, dni_emp, fecha, horas, horas_a_parte, festivo, provincia FROM horascuerpo WHERE ((MONTH(fecha) = '$mesAnterior' AND DAY(fecha) >= 26) OR (MONTH(fecha) = '$mes' AND DAY(fecha) <= 25)) 
                    AND YEAR(fecha) = '$año'  ORDER BY nombre_emp, fecha";

                    $resultado = mysqli_query($con, $consulta);

                    $empleados = array();
                    while ($row = mysqli_fetch_assoc($resultado)) {
                        $nombre = $row['nombre_emp'];
                        $fecha = $row['fecha'];
                        $horas = $row['horas'];

                        if (!isset($empleados[$nombre])) {
                            $empleados[$nombre] = array();
                        }
                        if (!isset($empleados[$nombre][$fecha])) {
                            $empleados[$nombre][$fecha] = array(
                                'horas' => 0,
                                'horas_a_parte' => 0,
                                'festivo' => '',
                                'provincia' => '',
                                'apellidos_emp' => '',
                                'empresa_emp' => '',
                                'dni_emp' => ''
                            );
                        }
                        $empleados[$nombre][$fecha]['horas'] += $horas;
                        $empleados[$nombre][$fecha]['horas_a_parte'] = $row['horas_a_parte'];
                        $empleados[$nombre][$fecha]['festivo'] = $row['festivo'];
                        $empleados[$nombre][$fecha]['provincia'] = $row['provincia'];
                        $empleados[$nombre][$fecha]['apellidos_emp'] = $row['apellidos_emp'];
                        $empleados[$nombre][$fecha]['empresa_emp'] = $row['empresa_emp'];
                        $empleados[$nombre][$fecha]['dni_emp'] = $row['dni_emp'];
                    }

                    $consulta_precios = "SELECT preciohorasextra, preciohorasextrafestivo FROM provincias WHERE provincia = (SELECT provincia FROM horascuerpo WHERE MONTH(fecha) = '$mes' AND YEAR(fecha) = '$año' LIMIT 1)";
                    $resultado_precios = mysqli_query($con, $consulta_precios);
                    $precios = mysqli_fetch_assoc($resultado_precios);
                    if ($precios) {
                        $phe = $precios['preciohorasextra'];
                        $phef = $precios['preciohorasextrafestivo'];

                        echo '<br/><table style="border-collapse: collapse; width: 100%;">';
                        echo '<tr>';
                        echo '<th style="border: 1px solid black; text-align: center;  width: auto;">NOMBRE TRABAJADOR</th>';
                        echo '<th style="border: 1px solid black; text-align: center;  width: auto;">PRECIO H.EX</th>';
                        echo '<th style="border: 1px solid black; text-align: center;  width: auto;">Nº H.EX</th>';
                        echo '<th style="border: 1px solid black; text-align: center;  width: auto;">I.H.EX</th>';
                        echo '<th style="border: 1px solid black; text-align: center;  width: auto;">PRECIO H.E.F</th>';
                        echo '<th style="border: 1px solid black; text-align: center;  width: auto;">Nº H.E.F</th>';
                        echo '<th style="border: 1px solid black; text-align: center;  width: auto;">I.H.E.F</th>';
                        echo '<th style="border: 1px solid black; text-align: center;  width: auto;" class="bg-warning">TOTAL EXTRAS</th>';
                        echo '</tr>';

                        foreach ($empleados as $nombre => $fechas) {
                            $horas_totales = 0;
                            $horas_a_parte_totales = 0;
                            $ihex_totales = 0;
                            $ihexf_totales = 0;
                            $totalextras_totales = 0;

                            $horas_festivas_totales = 0;

                            foreach ($fechas as $fecha => $datos) {
                                $apellidos_emp = $datos['apellidos_emp'];
                                $empresa_emp = $datos['empresa_emp'];

                                $dia = date("d", strtotime($fecha));
                                $horas = $datos['horas'];
                                $horas_a_parte = $datos['horas_a_parte'];
                                $festivo = $datos['festivo'];
                                $provincia = $datos['provincia'];
                                $dni_emp = $datos['dni_emp'];

                                if ($festivo == 'sí') {
                                    $horas_festivas_totales += $horas + $horas_a_parte;
                                }

                                $horas_totales += $horas;
                                $horas_a_parte_totales += $horas_a_parte;
                                $ihex_totales += $horas_a_parte * $phe;
                            }

                            $ihexf_totales = $horas_festivas_totales * $phef;

                            $totalextras_totales = $ihex_totales + $ihexf_totales;

                            echo '<tr>';
                            echo "<td style='border: 1px solid black; text-align: center;'>$nombre $apellidos_emp $dni_emp $empresa_emp</td>";
                            echo "<td style='border: 1px solid black; text-align: center;' class='text-danger'>$phe</td>";
                            echo "<td style='border: 1px solid black; text-align: center;' class='bg-info'>$horas_a_parte_totales</td>";
                            echo "<td style='border: 1px solid black; text-align: center;'>$ihex_totales</td>";
                            echo "<td style='border: 1px solid black; text-align: center;' class='text-danger'>$phef</td>";
                            echo "<td style='border: 1px solid black; text-align: center;' class='bg-info'>$horas_festivas_totales</td>";
                            echo "<td style='border: 1px solid black; text-align: center;'>$ihexf_totales</td>";
                            echo "<td style='border: 1px solid black; text-align: center;'class='bg-warning'>$totalextras_totales</td>";
                            echo '</tr>';
                        }

                        echo '</table>';
                    } else {
                        echo "No se encontraron precios para las horas extra y horas extra festivas.";
                    }
                }
            }
            echo "<form action='precioextrastabla.php' method='post'>
                            <br>
                            <div class='d-grid gap-2'>
                            <input type='submit' name='exportar' value='EXPORTAR' class='btn btn-success'/>
                            <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                            <div class='hidden-text'>Pulsa aquí para EXPORTAR a Excel.</div>
                            </div>

                        </form>";


            if (isset($_POST['exportar'])) {
                if (isset($_SESSION['datosmensual'])) {
                    $codigos = $_SESSION['datosmensual'];

                    // Crear el objeto Spreadsheet
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();

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

                        $consulta = "SELECT nombre_emp, apellidos_emp, empresa_emp, dni_emp, fecha, horas, horas_a_parte, festivo, provincia FROM horascuerpo WHERE ((MONTH(fecha) = '$mesAnterior' AND DAY(fecha) >= 26) OR (MONTH(fecha) = '$mes' AND DAY(fecha) <= 25)) 
                    AND YEAR(fecha) = '$año'  ORDER BY nombre_emp, fecha";

                        $resultado = mysqli_query($con, $consulta);

                        $empleados = array();
                        while ($row = mysqli_fetch_assoc($resultado)) {
                            $nombre = $row['nombre_emp'];
                            $fecha = $row['fecha'];
                            $horas = $row['horas'];

                            if (!isset($empleados[$nombre])) {
                                $empleados[$nombre] = array();
                            }
                            if (!isset($empleados[$nombre][$fecha])) {
                                $empleados[$nombre][$fecha] = array(
                                    'horas' => 0,
                                    'horas_a_parte' => 0,
                                    'festivo' => '',
                                    'provincia' => '',
                                    'apellidos_emp' => '',
                                    'empresa_emp' => '',
                                    'dni_emp' => ''
                                );
                            }
                            $empleados[$nombre][$fecha]['horas'] += $horas;
                            $empleados[$nombre][$fecha]['horas_a_parte'] = $row['horas_a_parte'];
                            $empleados[$nombre][$fecha]['festivo'] = $row['festivo'];
                            $empleados[$nombre][$fecha]['provincia'] = $row['provincia'];
                            $empleados[$nombre][$fecha]['apellidos_emp'] = $row['apellidos_emp'];
                            $empleados[$nombre][$fecha]['empresa_emp'] = $row['empresa_emp'];
                            $empleados[$nombre][$fecha]['dni_emp'] = $row['dni_emp'];
                        }

                        $consulta_precios = "SELECT preciohorasextra, preciohorasextrafestivo FROM provincias WHERE provincia = (SELECT provincia FROM horascuerpo WHERE MONTH(fecha) = '$mes' AND YEAR(fecha) = '$año' LIMIT 1)";
                        $resultado_precios = mysqli_query($con, $consulta_precios);
                        $precios = mysqli_fetch_assoc($resultado_precios);

                        $phe = $precios['preciohorasextra'];
                        $phef = $precios['preciohorasextrafestivo'];

                        $sheet->setCellValue('A1', "Mes: " . $nombresMeses[$mes]);
                        $sheet->setCellValue('A2', "Año: " . $año);
                        $sheet->setCellValue('A4', 'NOMBRE TRABAJADOR');
                        $sheet->setCellValue('B4', 'PRECIO H.EX');
                        $sheet->setCellValue('C4', 'Nº H.EX');
                        $sheet->setCellValue('D4', 'I.H.EX');
                        $sheet->setCellValue('E4', 'PRECIO H.E.F');
                        $sheet->setCellValue('F4', 'Nº H.E.F');
                        $sheet->setCellValue('G4', 'I.H.E.F');
                        $sheet->setCellValue('H4', 'TOTAL EXTRAS');

                        $rowIndex = 5;
                        foreach ($empleados as $nombre => $fechas) {
                            $horas_totales = 0;
                            $horas_a_parte_totales = 0;
                            $ihex_totales = 0;
                            $ihexf_totales = 0;
                            $totalextras_totales = 0;

                            $horas_festivas_totales = 0;

                            foreach ($fechas as $fecha => $datos) {
                                $apellidos_emp = $datos['apellidos_emp'];
                                $empresa_emp = $datos['empresa_emp'];

                                $dia = date("d", strtotime($fecha));
                                $horas = $datos['horas'];
                                $horas_a_parte = $datos['horas_a_parte'];
                                $festivo = $datos['festivo'];
                                $provincia = $datos['provincia'];
                                $dni_emp = $datos['dni_emp'];

                                if ($festivo == 'sí') {
                                    $horas_festivas_totales += $horas + $horas_a_parte;
                                }

                                $horas_totales += $horas;
                                $horas_a_parte_totales += $horas_a_parte;
                                $ihex_totales += $horas_a_parte * $phe;
                            }

                            $ihexf_totales = $horas_festivas_totales * $phef;

                            $totalextras_totales = $ihex_totales + $ihexf_totales;

                            $sheet->setCellValue('A' . $rowIndex, "$nombre $apellidos_emp $dni_emp $empresa_emp");
                            $sheet->setCellValue('B' . $rowIndex, $phe);
                            $sheet->setCellValue('C' . $rowIndex, $horas_a_parte_totales);
                            $sheet->setCellValue('D' . $rowIndex, $ihex_totales);
                            $sheet->setCellValue('E' . $rowIndex, $phef);
                            $sheet->setCellValue('F' . $rowIndex, $horas_festivas_totales);
                            $sheet->setCellValue('G' . $rowIndex, $ihexf_totales);
                            $sheet->setCellValue('H' . $rowIndex, $totalextras_totales);

                            $rowIndex++;
                        }

                        // Ajustar automáticamente el ancho de las columnas
                        $columnIterator = $sheet->getColumnIterator();
                        foreach ($columnIterator as $column) {
                            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                        }

                        // Establecer estilo a la tabla
                        $lastRowIndex = $rowIndex - 1;

                        $sheet->getStyle('A1')->getFont()->setBold(true);
                        $sheet->getStyle('A2')->getFont()->setBold(true);
                        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
                        $sheet->getStyle('A4:H4')->getAlignment()->setHorizontal('center');
                        $sheet->getStyle('A4:H' . $lastRowIndex)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                        $sheet->getStyle('B5:C' . $lastRowIndex)->getAlignment()->setHorizontal('center');
                        $sheet->getStyle('D5:H' . $lastRowIndex)->getAlignment()->setHorizontal('right');
                        $sheet->getStyle('B5:C' . $lastRowIndex)->getNumberFormat()->setFormatCode('#,##0.00');
                        $sheet->getStyle('D5:H' . $lastRowIndex)->getNumberFormat()->setFormatCode('#,##0.00');

                        // Centrar contenido de todas las celdas
                        $cellRange = 'A4:H' . $lastRowIndex;
                        $sheet->getStyle($cellRange)->getAlignment()->setHorizontal('center');

                        //Cambiar el color de la letra en las columnas B y F
                        $columnBRange = 'B2:B' . $lastRowIndex;
                        $columnFRange = 'E2:E' . $lastRowIndex;

                        $redColor = 'FF0000';

                        $sheet->getStyle($columnBRange)->getFont()->getColor()->setRGB($redColor);
                        $sheet->getStyle($columnFRange)->getFont()->getColor()->setRGB($redColor);

                        $blackColor = '000000';

                        $sheet->getStyle('B4')->getFont()->getColor()->setRGB($blackColor);
                        $sheet->getStyle('E4')->getFont()->getColor()->setRGB($blackColor);

                        $lightBlueColor = 'BFEFFF';


                        // Rellenar las celdas de las columnas C y F con color azul celeste
                        $columnCRange = 'C5:C' . $lastRowIndex;
                        $columnFRange = 'F5:F' . $lastRowIndex;
                        $columnHRange = 'H4:H' . $lastRowIndex;

                        $sheet->getStyle($columnCRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($lightBlueColor);
                        $sheet->getStyle($columnFRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($lightBlueColor);
                        $sheet->getStyle($columnHRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFC107');

                        // Quitar el relleno de color azul celeste en las celdas C4 y F4
                        $sheet->getStyle('C4')->getFill()->setFillType(Fill::FILL_NONE);
                        $sheet->getStyle('F4')->getFill()->setFillType(Fill::FILL_NONE);

                        // Definir el nombre del archivo de descarga
                        $filename = "EXTRAS_$nombresMeses[$mes]_$año.xlsx";

                        // Configurar las cabeceras para la descarga del archivo
                        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                        header('Content-Disposition: attachment; filename="' . $filename . '"');

                        // Limpiar el búfer de salida
                        ob_clean();

                        // Enviar el contenido del archivo al navegador
                        $writer = new Xlsx($spreadsheet);
                        $writer->save('php://output');

                        // Detener la ejecución del script
                        exit();
                    }

                    // Limpia los datos de la sesión
                    unset($_SESSION['datosmensual']);
                }
            }



            ?>
        </div>

        <form action="precioextras.php" method="post">
            <input type="submit" name="volver_index" value="VOLVER" class="btn btn-secondary">
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

        });
    </script>
</body>

</html>