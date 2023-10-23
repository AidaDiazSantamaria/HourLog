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
        <div class="d-flex align-items-center justify-content-center flex-column" style="width: 90vw;">

            <?php
            $user = $_SESSION["usuario"];
            $id_usuario = obtener_idusu($con, $user);

            $_SESSION['id_usuario'] = obtener_idusu($con, $user);
            $jefesobra = obtener_jefesobra($con);
            $encargados = obtener_encargados($con);
            $obras = obtener_obras($con);
            $empleados = obtener_empleados($con);
            $idRegistro = 0;




            if (isset($_POST['añadir'])) {
                echo "<form method='post' action='masemp.php'>";
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
                            <td><select name='datosdelemp[]' class='form-select mb-3'>
                            <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar empleado</option>";
                $empleados = obtener_empleados($con);
                while ($fila = obtener_resultados($empleados)) {
                    extract($fila);
                    $valor = "$nombre_emp|$apellidos_emp|$empresa|$dni|$tel";
                    echo "<option value='$valor'>$nombre_emp $apellidos_emp $empresa $dni $tel</option>";
                }
                echo "</select>
                            </td>
                            <td><select name='horas[]' class='form-select mb-3'>
                            <option selected='selected' disabled='disabled' value='Seleccionar'>Horas</option>
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
                            <option selected='selected' value='0'>0</option>
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
                            <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                            <option value=Sí>Sí</option>
                            <option value=No>No</option>

                            </td>           
                            <td><select name='baja[]' class='form-select mb-3'>
                            <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                            <option value=Sí>Sí</option>
                            <option value=No>No</option>

                            </td> 
                            <td><select name='vacaciones[]' class='form-select mb-3'>
                            <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                            <option value=Sí>Sí</option>
                            <option value=No>No</option>

                            </td>
                            <td><select name='falta[]' class='form-select mb-3'>
                            <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                            <option value=Sí>Sí</option>
                            <option value=No>No</option>

                            </td>
                            <tr>
                            </table>
                            <div class='d-grid gap-2'>
                            <br><input type='submit' name='guardarhoras' value='AÑADIR' class='btn btn-secondary' id='botonañadir' disabled/>
                            <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                            <div class='hidden-text'>Selecciona por lo menos un empleado y las horas base y pulsa AÑADIR.</div>
                            </div>
                            </form>";
            }

            if (isset($_POST['guardarhoras'])) {

                if (
                    empty($_POST["datosdelemp"]) || empty($_POST["horas"])
                ) {

                    echo "<form method='post' action='masemp.php'>";
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
                            <td><select name='datosdelemp[]' class='form-select mb-3'>
                            <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar empleado</option>";
                    $empleados = obtener_empleados($con);
                    while ($fila = obtener_resultados($empleados)) {
                        extract($fila);
                        $valor = "$nombre_emp|$apellidos_emp|$empresa|$dni|$tel";
                        echo "<option value='$valor'>$nombre_emp $apellidos_emp $empresa $dni $tel</option>";
                    }
                    echo "</select>
                            </td>
                            <td><select name='horas[]' class='form-select mb-3'>
                            <option selected='selected' disabled='disabled' value='Seleccionar'>Horas</option>
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
                            <option selected='selected' value='0'>0</option>
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
                            <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                            <option value=Sí>Sí</option>
                            <option value=No>No</option>

                            </td>           
                            <td><select name='baja[]' class='form-select mb-3'>
                            <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                            <option value=Sí>Sí</option>
                            <option value=No>No</option>

                            </td> 
                            <td><select name='vacaciones[]' class='form-select mb-3'>
                            <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                            <option value=Sí>Sí</option>
                            <option value=No>No</option>

                            </td>
                            <td><select name='falta[]' class='form-select mb-3'>
                            <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                            <option value=Sí>Sí</option>
                            <option value=No>No</option>

                            </td>
                            <tr>
                            </table>
                            <div class='d-grid gap-2'>
                            <br><input type='submit' name='guardarhoras' value='AÑADIR' class='btn btn-secondary' id='botonañadir' disabled/>
                            <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                            <div class='hidden-text'>Selecciona por lo menos un empleado y las horas base y pulsa AÑADIR.</div>
                            </div>
                            </form>";

                    echo "<br>Debes rellenar mínimo empleado y horas.<br>";
                } else if (
                    isset($_POST["datosdelemp"]) && isset($_POST["horas"]) || isset($_POST["horas_a_parte"]) || isset($_POST['festivo']) || isset($_POST['baja']) || isset($_POST['vacaciones']) || isset($_POST['falta'])
                ) {
                    $id_usuario = $_SESSION['id_usuario'];

                    $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $_SESSION['selecfecha'])));

                    $obra = $_SESSION['obra'];
                    $valor_obra = explode('|', $obra[0]);
                    $nombre_obra = $valor_obra[0];
                    $codigo_obra = $valor_obra[1];
                    $_SESSION['codigo_obra'] = $codigo_obra;
                    $empresa_obra = $valor_obra[2];
                    $obtenerprovincia = obtener_provincia($con, $codigo_obra);
                    while ($registro = obtener_resultados($obtenerprovincia)) {
                        extract($registro);
                        $valor_registro = explode('|', $registro[0]);
                        $provincia = $registro[0];
                    }

                    $jefe_obra = $_SESSION['jefe_obra'];
                    $valor_jo = explode('|', $jefe_obra[0]);
                    $nombre_jo = $valor_jo[0];
                    $apellidos_jo = $valor_jo[1];

                    $encargado = $_SESSION['encargado'];
                    $valor_enc = explode('|', $encargado[0]);
                    $nombre_enc = $valor_enc[0];
                    $apellidos_enc = $valor_enc[1];

                    $empleado = $_POST['datosdelemp'];
                    $valor_emp = explode('|', $empleado[0]);
                    $nombre_emp = $valor_emp[0];
                    $apellidos_emp = $valor_emp[1];
                    $empresa_emp = $valor_emp[2];
                    $dni_emp = $valor_emp[3];
                    $tel_emp = $valor_emp[4];

                    $jornada1 = $_POST['horas'];
                    $valor_j1 = explode('|', $jornada1[0]);
                    $horas = $valor_j1[0];

                    $jornada2 = $_POST['horas_a_parte'];
                    $valor_j2 = explode('|', $jornada2[0]);
                    $horas_a_parte = $valor_j2[0];

                    $festivo = isset($_POST['festivo']) ? 'Sí' : 'No';
                    $baja = isset($_POST['baja']) ? 'Sí' : 'No';
                    $vacaciones = isset($_POST['vacaciones']) ? 'Sí' : 'No';
                    $falta = isset($_POST['falta']) ? 'Sí' : 'No';




                    $resultado = guardar_horascuerpo($con, $nombre_emp, $apellidos_emp, $empresa_emp, $dni_emp, $tel_emp, $horas, $horas_a_parte, $vacaciones, $falta, $festivo, $baja, $fecha, $provincia, $nombre_obra, $codigo_obra, $empresa_obra, $nombre_jo, $apellidos_jo, $nombre_enc, $apellidos_enc, $id_usuario);
                    $idRegistro = obtener_id_registro_insertado($con);
                    $_SESSION['idRegistro'] = $idRegistro;

                    echo "<form method='post' action='masemp.php'>";
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
                        <td><select name='datosdelemp[]' class='form-select mb-3'>
                        <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar empleado</option>";
                    $empleados = obtener_empleados($con);
                    while ($fila = obtener_resultados($empleados)) {
                        extract($fila);
                        $valor = "$nombre_emp|$apellidos_emp|$empresa|$dni|$tel";
                        echo "<option value='$valor'>$nombre_emp $apellidos_emp $empresa $dni $tel</option>";
                    }
                    echo "</select>
                        </td>
                        <td><select name='horas[]'class='form-select mb-3'>
                        <option selected='selected' disabled='disabled' value='Seleccionar'>Horas</option>
                        <option value=8>8</option>
                        <option value=7>7</option>
                        <option value=6>6</option>            
                        <option value=5>5</option>
                        <option value=4>4</option>
                        <option value=3>3</option>
                        <option value=2>2</option>
                        <option value=1>1</option>
                        </td>           
                        <td><select name='horas_a_parte[]'class='form-select mb-3'>
                        <option selected='selected' value='0'>0</option>
                        <option value=1>1</option>
                        <option value=2>2</option>
                        <option value=3>3</option>
                        <option value=4>4</option>
                        <option value=5>5</option>
                        <option value=6>6</option>
                        <option value=7>7</option>
                        <option value=8>8</option>
                        </td>           
                        <td><select name='festivo[]'class='form-select mb-3'>
                        <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                        <option value=Sí>Sí</option>
                        <option value=No>No</option>
                        </td>           
                        <td><select name='baja[]'class='form-select mb-3'>
                        <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                        <option value=Sí>Sí</option>
                        <option value=No>No</option>
                        </td> 
                        <td><select name='vacaciones[]'class='form-select mb-3'>
                        <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                        <option value=Sí>Sí</option>
                        <option value=No>No</option>
                        </td>
                        <td><select name='falta[]'class='form-select mb-3'>
                        <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                        <option value=Sí>Sí</option>
                        <option value=No>No</option>
                        </td>
                        <tr>
                        </table>
                        <div class='d-grid gap-2'>
                        <br><input type='submit' name='guardarhoras' value='AÑADIR' class='btn btn-secondary' id='botonañadir' disabled/>
                        <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                        <div class='hidden-text'>Selecciona por lo menos un empleado y las horas base y pulsa AÑADIR.</div>
                        </div>
                        </form>";

                    if ($resultado) {

                        echo "<br>
                        <form method='post' action='masemp.php'>";
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
                        echo '<th style="text-align: center; border: 1px solid gray; ">BORRAR</th>';


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
                            echo "<td>
                    <button type='submit' name='borrar' value='$id_horascuerpo' class='btn btn-secondary'>borrar</button></td>
                </tr>";
                        }
                        echo "</table>
                        <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                        <div class='hidden-text'>Pulsa BORRAR para eliminar una fila.<br> Asegúrate de que está todo correcto, después sólo podrás eliminar días enteros.</div>
            
                    </form>
                    ";

                        echo "</br>Los datos se han insertado correctamente.";
                    } else {
                        echo "<br>
                        <form method='post' action='masemp.php'>";
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
                        echo '<th style="text-align: center; border: 1px solid gray; ">BORRAR</th>';


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
                            echo "<td>
                    <button type='submit' name='borrarult' value='$id_horascuerpo' class='btn btn-secondary'>borrar</button></td>
                    
                </tr>";
                        }
                        echo "</table>
                        <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                            <div class='hidden-text'>Pulsa BORRAR para eliminar una fila.<br> Asegúrate de que está todo correcto, después sólo podrás eliminar días enteros.</div>
                
                    
                    </form>
                    ";

                        echo "<div class='text-center'><br/>Ya existe un registro del empleado para la fecha seleccionada.";
                    }
                }
            }


            if (isset($_POST['borrar'])) {
                $idBoton = $_POST['borrar'];
                $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $_SESSION['selecfecha'])));
                eliminar_registroHCUE($con, $idBoton);
                $codigo_obra = $_SESSION['codigo_obra'];

                echo "<form method='post' action='masemp.php'>";
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
                        <td><select name='datosdelemp[]' class='form-select mb-3'>
                        <option selected='selected' disabled='disabled' value='Seleccionar'>Seleccionar empleado</option>";
                $empleados = obtener_empleados($con);
                while ($fila = obtener_resultados($empleados)) {
                    extract($fila);
                    $valor = "$nombre_emp|$apellidos_emp|$empresa|$dni|$tel";
                    echo "<option value='$valor'>$nombre_emp $apellidos_emp $empresa $dni $tel</option>";
                }
                echo "</select>
                        </td>
                        <td><select name='horas[]' class='form-select mb-3'>
                        <option selected='selected' disabled='disabled' value='Seleccionar'>Horas</option>
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
                        <option selected='selected' value='0'>0</option>
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
                        <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                        <option value=Sí>Sí</option>
                        <option value=No>No</option>

                        </td>           
                        <td><select name='baja[]' class='form-select mb-3'>
                        <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                        <option value=Sí>Sí</option>
                        <option value=No>No</option>

                        </td> 
                        <td><select name='vacaciones[]' class='form-select mb-3'>
                        <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                        <option value=Sí>Sí</option>
                        <option value=No>No</option>

                        </td>
                        <td><select name='falta[]' class='form-select mb-3'>
                        <option selected='selected' disabled='disabled' value='S/N'>S/N</option>
                        <option value=Sí>Sí</option>
                        <option value=No>No</option>

                        </td>
                        <tr>
                        </table>
                        <div class='d-grid gap-2'>
                        <br><input type='submit' name='guardarhoras' value='AÑADIR' class='btn btn-secondary' id='botonañadir' disabled/>
                        <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                        <div class='hidden-text'>Selecciona por lo menos un empleado y las horas base y pulsa AÑADIR.</div>
                       
                        </div>                        
                        </form>";

                echo "<br>
                        <form method='post' action='masemp.php'>";
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
                echo '<th style="text-align: center; border: 1px solid gray; ">BORRAR</th>';


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
                    echo "<td>
                    <button type='submit' name='borrar' value='$id_horascuerpo' class='btn btn-secondary'>borrar</button></td>
                </tr>";
                }
                echo "</table>
                <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                <div class='hidden-text'>Pulsa BORRAR para eliminar una fila.<br> Asegúrate de que está todo correcto, después sólo podrás eliminar días enteros.</div>
    
                    </form>
                    ";

                echo "</br>Los datos se han insertado correctamente.";
            }
            ?>
        </div>


        <form method='post' action='añadiremp.php'>
            <br><br>
            <p><input type="submit" name="volver" value="VOLVER" class='btn btn-secondary' />

        </form>

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

            // Obtener las referencias a los campos de selección
            var selectEmp = $("select[name='datosdelemp[]']");
            var selectHoras = $("select[name='horas[]']");



            // Agregar un evento de cambio a ambos campos de selección
            selectEmp.change(function() {
                habilitarBotonborrar();
            });

            selectHoras.change(function() {
                habilitarBotonborrar();
            });

            // Función para habilitar o deshabilitar el botón "MOSTRAR" según las selecciones
            function habilitarBotonborrar() {
                if (selectEmp.val() !== "" && !selectEmp.find(":selected").is(":disabled") && selectHoras.val() !== "" && !selectHoras.find(":selected").is(":disabled")) {
                    $("#botonañadir").prop("disabled", false);
                } else {
                    $("#botonañadir").prop("disabled", true);
                }
            }

            // Verificar si ambos campos de selección tienen opciones seleccionadas al cargar la página
            if (selectEmp.val() !== "" && !selectEmp.find(":selected").is(":disabled") && selectHoras.val() !== "" && !selectHoras.find(":selected").is(":disabled")) {
                $("#botonañadir").prop("disabled", false);
            } else {
                $("#botonañadir").prop("disabled", true);
            }

        });
    </script>
</body>

</html>