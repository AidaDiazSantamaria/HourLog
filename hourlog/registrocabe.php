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
    <div class="container vh-100 d-flex align-items-center justify-content-center">

        <div class="d-flex flex-column align-items-center">


            <?php

            if (isset($_POST['cargar'])) {
                $id_usuario = $_SESSION['id_usuario'];

                $resultado = obtener_ulregistrocabe($con, $id_usuario);
                $num_filas = obtener_num_filas($resultado);
                if ($num_filas == 0) {
                    $id_usuario = $_SESSION['id_usuario'];
                    $fecha = date("d-m-Y");
                    echo "<form method='post' action='registrocabe.php'>";
                    echo "<br><table id='fecha' border='1'  style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> FECHA </td>
                    <td>$fecha</td>
                    <td>
                    <div class='d-grid gap-2'>                    
                    <input type='submit' name='selecfecha' value='FECHA MANUAL' class='btn btn-secondary'/>
                    </div>
                    </td>
                    </tr>
                    </table>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA MANUAL si quieres introducir una fecha diferente.</div>
                    </form>
                    ";

                    echo "<form method='post' action='registrocabe.php'>";

                    echo "<br><table id='tabla' border='1'  style='width: 454px; margin: 0 auto;'>
                        <tr>
                        <td class='form-control text-center'> JEFE DE OBRA </td>
                        <td>";
                    echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                    echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                    while ($fila = obtener_resultados($jefesobra)) {
                        extract($fila);
                        $valor = "$nombre_jo|$apellidos_jo";
                        echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                    }
                    echo "</select>
                        </td>
                        </tr>

                        <tr>
                        <td class='form-control text-center'> ENCARGADO </td>
                        <td>";
                    echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                    echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                    while ($fila = obtener_resultados($encargados)) {
                        extract($fila);
                        $valor = "$nombre_enc|$apellidos_enc";
                        echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                    }
                    echo "</select>
                        </td>
                        </tr>

                        <tr>
                        <td class='form-control text-center'> OBRA </td>
                        <td>";
                    echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                    echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                    while ($fila = obtener_resultados($obras)) {
                        extract($fila);
                        $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                        echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                    }
                    echo "</select>
                        </td>
                        </tr>
                        </table>
                        <div class='d-grid gap-2'>                    
                        <br><input type='submit' name='seleccionar' value='SELECCIONAR' class='btn btn-secondary' />
                        <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                        <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                        </div>
                        </form>
                        ";
                } else {


                    $id_usuario = $_SESSION['id_usuario'];
                    $_SESSION['fechaultimoregistro'] = obtener_fechaultimoregistro($con, $id_usuario);
                    // $fechaultimoregistro = $_SESSION['fechaultimoregistro'];

                    $id_usuario = $_SESSION['id_usuario'];
                    $ultregistro = obtener_ulregistrocabe($con, $id_usuario);
                    while ($registro = obtener_resultados($ultregistro)) {
                        extract($registro);
                        $id_usuario = $_SESSION['id_usuario'];

                        $valor_registro = explode('|', $registro[0]);
                        $fecha = date("d-m-Y");
                        $_SESSION['fecha'] = $fecha;
                        $nombre_obra = $registro[2];
                        $codigo_obra = $registro[3];
                        $empresa_obra = $registro[4];

                        $nombre_jo = $registro[5];
                        $apellidos_jo = $registro[6];

                        $nombre_enc = $registro[7];
                        $apellidos_enc = $registro[8];
                    }


                    echo "<form method='post' action='registrocabe.php'>";
                    echo "<br><table id='fecha' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> FECHA </td>
                    <td>$fecha</td>
                    <td>
                    <div class='d-grid gap-2'>                    
                    <input type='submit' name='selecfecha' value='FECHA MANUAL' class='btn btn-secondary'/>
                    </div>
                    </td>
                    </tr>
                    </table>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA MANUAL si quieres introducir una fecha diferente.</div>
                    </form>
                    ";

                    echo "<form method='post' action='registrocabe.php'>";

                    echo "<br><table id='tabla' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> JEFE DE OBRA </td>
                    <td>";
                    echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                    echo "<option selected='selected' value='$nombre_jo|$apellidos_jo'>$nombre_jo $apellidos_jo</option>";

                    while ($fila = obtener_resultados($jefesobra)) {
                        extract($fila);
                        $valor = "$nombre_jo|$apellidos_jo";
                        echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                    }
                    echo "</select>
                    </td>
                    </tr>

                    <tr>
                    <td class='form-control text-center'> ENCARGADO </td>
                    <td>";
                    echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                    echo "<option selected='selected'value='$nombre_enc|$apellidos_enc'>$nombre_enc $apellidos_enc</option>";

                    while ($fila = obtener_resultados($encargados)) {
                        extract($fila);
                        $valor = "$nombre_enc|$apellidos_enc";
                        echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                    }
                    echo "</select>
                    </td>
                    </tr>

                    <tr>
                    <td class='form-control text-center'> OBRA </td>
                    <td>";
                    echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                    echo "<option selected='selected' value='$nombre_obra|$codigo_obra|$empresa_obra'>$nombre_obra $codigo_obra $empresa_obra</option>";

                    while ($fila = obtener_resultados($obras)) {
                        extract($fila);
                        $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                        echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                    }
                    echo "</select>
                    </td>
                    </tr>
                    </table>
                    <div class='d-grid gap-2'>                    
                                            <br><input type='submit' name='seleccionar' value='SELECCIONAR' class='btn btn-secondary' />
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                    </div>
                    </form>
                    ";
                }
            }

            if (isset($_POST['seleccionar'])) {

                if (
                    empty($_POST["datosdelaobra"]) || empty($_POST["datosdeljo"]) || empty($_POST["datosdelenc"])
                ) {
                    $id_usuario = $_SESSION['id_usuario'];

                    $resultado = obtener_ulregistrocabe($con, $id_usuario);
                    $num_filas = obtener_num_filas($resultado);
                    if ($num_filas == 0) {
                        $id_usuario = $_SESSION['id_usuario'];
                        $fecha = date("d-m-Y");
                        echo "<form method='post' action='registrocabe.php'>";
                        echo "<br><table id='fecha' border='1'  style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> FECHA </td>
                    <td>$fecha</td>
                    <td>
                    <div class='d-grid gap-2'>                    
                    <input type='submit' name='selecfecha' value='FECHA MANUAL' class='btn btn-secondary'/>
                    </div>
                    </td>
                    </tr>
                    </table>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA MANUAL si quieres introducir una fecha diferente.</div>
                    </form>
                    ";

                        echo "<form method='post' action='registrocabe.php'>";

                        echo "<br><table id='tabla' border='1'  style='width: 454px; margin: 0 auto;'>
                        <tr>
                        <td class='form-control text-center'> JEFE DE OBRA </td>
                        <td>";
                        echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                        echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                        while ($fila = obtener_resultados($jefesobra)) {
                            extract($fila);
                            $valor = "$nombre_jo|$apellidos_jo";
                            echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                        }
                        echo "</select>
                        </td>
                        </tr>

                        <tr>
                        <td class='form-control text-center'> ENCARGADO </td>
                        <td>";
                        echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                        echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                        while ($fila = obtener_resultados($encargados)) {
                            extract($fila);
                            $valor = "$nombre_enc|$apellidos_enc";
                            echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                        }
                        echo "</select>
                        </td>
                        </tr>

                        <tr>
                        <td class='form-control text-center'> OBRA </td>
                        <td>";
                        echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                        echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                        while ($fila = obtener_resultados($obras)) {
                            extract($fila);
                            $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                            echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                        }
                        echo "</select>
                        </td>
                        </tr>
                        </table>
                        <div class='d-grid gap-2'>                    
                        <br><input type='submit' name='seleccionar' value='SELECCIONAR' class='btn btn-secondary' />
                        <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                        <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                        </div>
                        </form>
                        ";
                    } else {


                        $id_usuario = $_SESSION['id_usuario'];
                        $_SESSION['fechaultimoregistro'] = obtener_fechaultimoregistro($con, $id_usuario);
                        // $fechaultimoregistro = $_SESSION['fechaultimoregistro'];

                        $id_usuario = $_SESSION['id_usuario'];
                        $ultregistro = obtener_ulregistrocabe($con, $id_usuario);
                        while ($registro = obtener_resultados($ultregistro)) {
                            extract($registro);
                            $id_usuario = $_SESSION['id_usuario'];

                            $valor_registro = explode('|', $registro[0]);
                            $fecha = date("d-m-Y");
                            $_SESSION['fecha'] = $fecha;
                            $nombre_obra = $registro[2];
                            $codigo_obra = $registro[3];
                            $empresa_obra = $registro[4];

                            $nombre_jo = $registro[5];
                            $apellidos_jo = $registro[6];

                            $nombre_enc = $registro[7];
                            $apellidos_enc = $registro[8];
                        }


                        echo "<form method='post' action='registrocabe.php'>";
                        echo "<br><table id='fecha' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> FECHA </td>
                    <td>$fecha</td>
                    <td>
                    <div class='d-grid gap-2'>                    
                    <input type='submit' name='selecfecha' value='FECHA MANUAL' class='btn btn-secondary'/>
                    </div>
                    </td>
                    </tr>
                    </table>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA MANUAL si quieres introducir una fecha diferente.</div>
                    </form>
                    ";

                        echo "<form method='post' action='registrocabe.php'>";

                        echo "<br><table id='tabla' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> JEFE DE OBRA </td>
                    <td>";
                        echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                        echo "<option selected='selected' value='$nombre_jo|$apellidos_jo'>$nombre_jo $apellidos_jo</option>";

                        while ($fila = obtener_resultados($jefesobra)) {
                            extract($fila);
                            $valor = "$nombre_jo|$apellidos_jo";
                            echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                        }
                        echo "</select>
                    </td>
                    </tr>

                    <tr>
                    <td class='form-control text-center'> ENCARGADO </td>
                    <td>";
                        echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                        echo "<option selected='selected'value='$nombre_enc|$apellidos_enc'>$nombre_enc $apellidos_enc</option>";

                        while ($fila = obtener_resultados($encargados)) {
                            extract($fila);
                            $valor = "$nombre_enc|$apellidos_enc";
                            echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                        }
                        echo "</select>
                    </td>
                    </tr>

                    <tr>
                    <td class='form-control text-center'> OBRA </td>
                    <td>";
                        echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                        echo "<option selected='selected' value='$nombre_obra|$codigo_obra|$empresa_obra'>$nombre_obra $codigo_obra $empresa_obra</option>";

                        while ($fila = obtener_resultados($obras)) {
                            extract($fila);
                            $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                            echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                        }
                        echo "</select>
                    </td>
                    </tr>
                    </table>
                    <div class='d-grid gap-2'>                    
                                            <br><input type='submit' name='seleccionar' value='SELECCIONAR' class='btn btn-secondary' />
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                    </div>
                    </form>
                    ";
                    }
                    echo "<br>Debes rellenar todos los datos.</br>";
                } else if (
                    isset($_POST["datosdelaobra"]) && isset($_POST["datosdeljo"]) && isset($_POST["datosdelenc"])
                ) {

                    $id_usuario = $_SESSION['id_usuario'];

                    $fecha = date("Y-m-d");
                    $_SESSION['selecfecha'] = $fecha;
                    $_SESSION['fecha'] = $fecha;
                    $obra = $_POST['datosdelaobra'];
                    $valor_obra = explode('|', $obra[0]);
                    $codigo_obra = $valor_obra[1];

                    $_SESSION['obra'] = $obra;
                    $_SESSION['codigo_obra'] = $codigo_obra;

                    $valor_obra = explode('|', $obra[0]);
                    $nombre_obra = $valor_obra[0];
                    $codigo_obra = $valor_obra[1];
                    $empresa_obra = $valor_obra[2];

                    $jefe_obra = $_POST['datosdeljo'];

                    $_SESSION['jefe_obra'] = $jefe_obra;

                    $valor_jo = explode('|', $jefe_obra[0]);
                    $nombre_jo = $valor_jo[0];
                    $apellidos_jo = $valor_jo[1];

                    $encargado = $_POST['datosdelenc'];

                    $_SESSION['encargado'] = $encargado;

                    $valor_enc = explode('|', $encargado[0]);
                    $nombre_enc = $valor_enc[0];
                    $apellidos_enc = $valor_enc[1];

                    $resultado = guardar_horascabecera($con, $fecha, $nombre_obra, $codigo_obra, $empresa_obra, $nombre_jo, $apellidos_jo, $nombre_enc, $apellidos_enc, $id_usuario);

                    if ($resultado) {
                        // Llamada a la función obtener_id_registro_insertado
                        $idRegistro = obtener_id_registro_insertado($con);
                        $_SESSION['idRegistro'] = $idRegistro;

                        $horascabecera = obtener_horascabecera($con);
                        while ($fila = obtener_resultados($horascabecera)) {
                            extract($fila);
                            $fecha = date("d-m-Y");
                            $tabla3 =  "<form method='post' action='registrocabe.php'>
                <br>
                <table id='tabla3' border='1' style='width: 454px; margin: 0 auto; border-collapse: collapse;'>
                    <tr>
                        <td style='border: 1px solid gray; padding: 8px;'>FECHA</td>
                        <td style='border: 1px solid gray; padding: 8px;'>$fecha</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid gray; padding: 8px;'>JEFE DE OBRA</td>
                        <td style='border: 1px solid gray; padding: 8px;'>$nombre_jo $apellidos_jo</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid gray; padding: 8px;'>ENCARGADO</td>
                        <td style='border: 1px solid gray; padding: 8px;'>$nombre_enc $apellidos_enc</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid gray; padding: 8px;'>OBRA</td>
                        <td style='border: 1px solid gray; padding: 8px;'>$nombre_obra $codigo_obra $empresa_obra</td>
                    </tr>
                </table>
                
                </form>
            
                        ";
                            echo "<form method='post' action='registrocabe.php'>
                <br>
                <table id='tabla3' border='1' style='width: 454px; margin: 0 auto; border-collapse: collapse;'>
                    <tr>
                        <td style='border: 1px solid gray; padding: 8px;'>FECHA</td>
                        <td style='border: 1px solid gray; padding: 8px;'>$fecha</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid gray; padding: 8px;'>JEFE DE OBRA</td>
                        <td style='border: 1px solid gray; padding: 8px;'>$nombre_jo $apellidos_jo</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid gray; padding: 8px;'>ENCARGADO</td>
                        <td style='border: 1px solid gray; padding: 8px;'>$nombre_enc $apellidos_enc</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid gray; padding: 8px;'>OBRA</td>
                        <td style='border: 1px solid gray; padding: 8px;'>$nombre_obra $codigo_obra $empresa_obra</td>
                    </tr>
                </table>
                <div class='d-grid gap-2'>
                    <br><input type='submit' name='modificar' value='MODIFICAR' class='btn btn-secondary'/>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                        <div class='hidden-text'>Pulsa MODIFICAR si quieres corregir algo.</div>
                </div>
                <div class='d-grid gap-2'>
                    <br><input type='submit' name='validar' value='VALIDAR' class='btn btn-secondary'/>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                        <div class='hidden-text'>Pulsa VALIDAR para continuar.</div>
                </div>
                </form>
            
                        ";
                        }
                        $_SESSION['tabla3'] = $tabla3;
                        echo "Los datos se han insertado correctamente.";
                    } else {
                        $id_usuario = $_SESSION['id_usuario'];

                        $resultado = obtener_ulregistrocabe($con, $id_usuario);
                        $num_filas = obtener_num_filas($resultado);
                        if ($num_filas == 0) {
                            $id_usuario = $_SESSION['id_usuario'];
                            $fecha = date("d-m-Y");
                            echo "<form method='post' action='registrocabe.php'>";
                            echo "<br><table id='fecha' border='1'  style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> FECHA </td>
                    <td>$fecha</td>
                    <td>
                    <div class='d-grid gap-2'>                    
                    <input type='submit' name='selecfecha' value='FECHA MANUAL' class='btn btn-secondary'/>
                    </div>
                    </td>
                    </tr>
                    </table>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA MANUAL si quieres introducir una fecha diferente.</div>
                    </form>
                    ";

                            echo "<form method='post' action='registrocabe.php'>";

                            echo "<br><table id='tabla' border='1'  style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> JEFE DE OBRA </td>
                    <td>";
                            echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                            echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                            while ($fila = obtener_resultados($jefesobra)) {
                                extract($fila);
                                $valor = "$nombre_jo|$apellidos_jo";
                                echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                            }
                            echo "</select>
                    </td>
                    </tr>
        
                    <tr>
                    <td class='form-control text-center'> ENCARGADO </td>
                    <td>";
                            echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                            echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                            while ($fila = obtener_resultados($encargados)) {
                                extract($fila);
                                $valor = "$nombre_enc|$apellidos_enc";
                                echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                            }
                            echo "</select>
                    </td>
                    </tr>
        
                    <tr>
                    <td class='form-control text-center'> OBRA </td>
                    <td>";
                            echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                            echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                            while ($fila = obtener_resultados($obras)) {
                                extract($fila);
                                $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                                echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                            }
                            echo "</select>
                    </td>
                    </tr>
                    </table>
                    <div class='d-grid gap-2'>                    
                    <br><input type='submit' name='seleccionar' value='SELECCIONAR' class='btn btn-secondary' />
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                    </div>
                    </form>
                    ";
                        } else {


                            $id_usuario = $_SESSION['id_usuario'];
                            $_SESSION['fechaultimoregistro'] = obtener_fechaultimoregistro($con, $id_usuario);
                            // $fechaultimoregistro = $_SESSION['fechaultimoregistro'];

                            $id_usuario = $_SESSION['id_usuario'];
                            $ultregistro = obtener_ulregistrocabe($con, $id_usuario);
                            while ($registro = obtener_resultados($ultregistro)) {
                                extract($registro);
                                $id_usuario = $_SESSION['id_usuario'];

                                $valor_registro = explode('|', $registro[0]);
                                $fecha = date("d-m-Y");
                                $_SESSION['fecha'] = $fecha;
                                $nombre_obra = $registro[2];
                                $codigo_obra = $registro[3];
                                $empresa_obra = $registro[4];

                                $nombre_jo = $registro[5];
                                $apellidos_jo = $registro[6];

                                $nombre_enc = $registro[7];
                                $apellidos_enc = $registro[8];
                            }


                            echo "<form method='post' action='registrocabe.php'>";
                            echo "<br><table id='fecha' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> FECHA </td>
                    <td>$fecha</td>
                    <td>
                    <div class='d-grid gap-2'>                    
                    <input type='submit' name='selecfecha' value='FECHA MANUAL' class='btn btn-secondary'/>
                    </div>
                    </td>
                    </tr>
                    </table>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA MANUAL si quieres introducir una fecha diferente.</div>
                    </form>
                    ";

                            echo "<form method='post' action='registrocabe.php'>";

                            echo "<br><table id='tabla' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> JEFE DE OBRA </td>
                    <td>";
                            echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                            echo "<option selected='selected' value='$nombre_jo|$apellidos_jo'>$nombre_jo $apellidos_jo</option>";

                            while ($fila = obtener_resultados($jefesobra)) {
                                extract($fila);
                                $valor = "$nombre_jo|$apellidos_jo";
                                echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                            }
                            echo "</select>
                    </td>
                    </tr>
        
                    <tr>
                    <td class='form-control text-center'> ENCARGADO </td>
                    <td>";
                            echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                            echo "<option selected='selected'value='$nombre_enc|$apellidos_enc'>$nombre_enc $apellidos_enc</option>";

                            while ($fila = obtener_resultados($encargados)) {
                                extract($fila);
                                $valor = "$nombre_enc|$apellidos_enc";
                                echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                            }
                            echo "</select>
                    </td>
                    </tr>
        
                    <tr>
                    <td class='form-control text-center'> OBRA </td>
                    <td>";
                            echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                            echo "<option selected='selected' value='$nombre_obra|$codigo_obra|$empresa_obra'>$nombre_obra $codigo_obra $empresa_obra</option>";

                            while ($fila = obtener_resultados($obras)) {
                                extract($fila);
                                $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                                echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                            }
                            echo "</select>
                    </td>
                    </tr>
                    </table>
                    <div class='d-grid gap-2'>                    
                    <br><input type='submit' name='seleccionar' value='SELECCIONAR' class='btn btn-secondary' />
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                    </div>
                    </form>
                    ";
                        }
                    }
                }
            }


            if (isset($_POST['modificar'])) {
                $idRegistro = $_SESSION['idRegistro'];
                eliminar_registroHCAB($con, $idRegistro);

                $id_usuario = $_SESSION['id_usuario'];
                $ultregistro = obtener_ulregistrocabe($con, $id_usuario);
                while ($registro = obtener_resultados($ultregistro)) {
                    extract($registro);
                    $id_usuario = $_SESSION['id_usuario'];

                    $valor_registro = explode('|', $registro[0]);
                    $fecha = date("d-m-Y");
                    $_SESSION['fecha'] = $fecha;

                    $nombre_obra = $registro[2];
                    $codigo_obra = $registro[3];
                    $empresa_obra = $registro[4];

                    $nombre_jo = $registro[5];
                    $apellidos_jo = $registro[6];

                    $nombre_enc = $registro[7];
                    $apellidos_enc = $registro[8];
                }
                $fecha = date("d-m-Y");

                echo "<form method='post' action='registrocabe.php'>";
                echo "<br><table id='fecha' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> FECHA </td>
                    <td>$fecha</td>
                    <td>
                    <div class='d-grid gap-2'>                    
                    <input type='submit' name='selecfecha' value='FECHA MANUAL' class='btn btn-secondary'/>
                    </div>
                    </td>     
                    </tr>
                    </table>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA MANUAL si quieres introducir una fecha diferente.</div>
                    </form>
                    ";

                echo "<form method='post' action='registrocabe.php'>";

                echo "<br><table id='tabla' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> JEFE DE OBRA </td>
                    <td>";
                echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                echo "<option selected='selected' value='$nombre_jo|$apellidos_jo'>$nombre_jo $apellidos_jo</option>";

                while ($fila = obtener_resultados($jefesobra)) {
                    extract($fila);
                    $valor = "$nombre_jo|$apellidos_jo";
                    echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                }
                echo "</select>
                    </td>
                    </tr>

                    <tr>
                    <td class='form-control text-center'> ENCARGADO </td>
                    <td>";
                echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                echo "<option selected='selected'value='$nombre_enc|$apellidos_enc'>$nombre_enc $apellidos_enc</option>";

                while ($fila = obtener_resultados($encargados)) {
                    extract($fila);
                    $valor = "$nombre_enc|$apellidos_enc";
                    echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                }
                echo "</select>
                    </td>
                    </tr>

                    <tr>
                    <td class='form-control text-center'> OBRA </td>
                    <td>";
                echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                echo "<option selected='selected' value='$nombre_obra|$codigo_obra|$empresa_obra'>$nombre_obra $codigo_obra $empresa_obra</option>";

                while ($fila = obtener_resultados($obras)) {
                    extract($fila);
                    $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                    echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                }
                echo "</select>
                    </td>
                    </tr>
                    </table>
                    <div class='d-grid gap-2'>                    
                                            <br><input type='submit' name='seleccionar' value='SELECCIONAR' class='btn btn-secondary' />
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                    </div>
                    </form>
                    ";
            }

            if (isset($_POST['selecfecha'])) {
                $idRegistro = obtener_id_registro_insertado($con);
                $_SESSION['idRegistro'] = $idRegistro;

                $id_usuario = $_SESSION['id_usuario'];
                $ultregistro = obtener_ulregistrocabe($con, $id_usuario);
                while ($registro = obtener_resultados($ultregistro)) {
                    extract($registro);
                    $id_usuario = $_SESSION['id_usuario'];

                    $valor_registro = explode('|', $registro[0]);

                    $nombre_obra = $registro[2];
                    $codigo_obra = $registro[3];
                    $empresa_obra = $registro[4];

                    $nombre_jo = $registro[5];
                    $apellidos_jo = $registro[6];

                    $nombre_enc = $registro[7];
                    $apellidos_enc = $registro[8];
                }
                echo "<form method='post' action='registrocabe.php'>";
                echo "<br><table id='fecha' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> FECHA </td>
                    <td><input type='text' name='fechamanual' placeholder= 'DD/MM/AAAA' id='fechamanual' class='form-control'/></td>
                    <td>
                    <div class='d-grid gap-2'>                    
                    <input type='submit' name='volverfechahoy' value='FECHA AUTOMÁTICA' class='btn btn-secondary'/>     
                    </div>
                    </td>
                    </tr>
                    </table>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA AUTOMÁTICA si quieres que se cargue la fecha de hoy.</div>
                    ";

                echo "<br><table id='tabla' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> JEFE DE OBRA </td>
                    <td>";
                echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                echo "<option selected='selected' value='$nombre_jo|$apellidos_jo'>$nombre_jo $apellidos_jo</option>";

                while ($fila = obtener_resultados($jefesobra)) {
                    extract($fila);
                    $valor = "$nombre_jo|$apellidos_jo";
                    echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                }
                echo "</select>
                    </td>
                    </tr>

                    <tr>
                    <td class='form-control text-center'> ENCARGADO </td>
                    <td>";
                echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                echo "<option selected='selected'value='$nombre_enc|$apellidos_enc'>$nombre_enc $apellidos_enc</option>";

                while ($fila = obtener_resultados($encargados)) {
                    extract($fila);
                    $valor = "$nombre_enc|$apellidos_enc";
                    echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                }
                echo "</select>
                    </td>
                    </tr>

                    <tr>
                    <td class='form-control text-center'> OBRA </td>
                    <td>";
                echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                echo "<option selected='selected' value='$nombre_obra|$codigo_obra|$empresa_obra'>$nombre_obra $codigo_obra $empresa_obra</option>";

                while ($fila = obtener_resultados($obras)) {
                    extract($fila);
                    $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                    echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                }
                echo "</select>
                    </td>
                    </tr>
                    </table>
                    <div class='d-grid gap-2'>                    
                    <br><input type='submit' name='seleccionarmanual' value='SELECCIONAR' class='btn btn-secondary'/>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                    </div>
                    </form>
                    ";
            }

            if (isset($_POST['seleccionarmanual'])) {
                if (
                    empty($_POST["datosdelaobra"]) || empty($_POST["datosdeljo"]) || empty($_POST["datosdelenc"]) || empty($_POST["fechamanual"])
                ) {
                    $id_usuario = $_SESSION['id_usuario'];

                    $resultado = obtener_ulregistrocabe($con, $id_usuario);
                    $num_filas = obtener_num_filas($resultado);
                    if ($num_filas == 0) {
                        $id_usuario = $_SESSION['id_usuario'];
                        $fecha = date("d-m-Y");
                        echo "<form method='post' action='registrocabe.php'>";
                        echo "<br><table id='fecha' border='1'  style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> FECHA </td>
                    <td>$fecha</td>
                    <td>
                    <div class='d-grid gap-2'>                    
                    <input type='submit' name='selecfecha' value='FECHA MANUAL' class='btn btn-secondary'/>
                    </div>
                    </td>
                    </tr>
                    </table>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA MANUAL si quieres introducir una fecha diferente.</div>
                    </form>
                    ";

                        echo "<form method='post' action='registrocabe.php'>";

                        echo "<br><table id='tabla' border='1'  style='width: 454px; margin: 0 auto;'>
                        <tr>
                        <td class='form-control text-center'> JEFE DE OBRA </td>
                        <td>";
                        echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                        echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                        while ($fila = obtener_resultados($jefesobra)) {
                            extract($fila);
                            $valor = "$nombre_jo|$apellidos_jo";
                            echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                        }
                        echo "</select>
                        </td>
                        </tr>

                        <tr>
                        <td class='form-control text-center'> ENCARGADO </td>
                        <td>";
                        echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                        echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                        while ($fila = obtener_resultados($encargados)) {
                            extract($fila);
                            $valor = "$nombre_enc|$apellidos_enc";
                            echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                        }
                        echo "</select>
                        </td>
                        </tr>

                        <tr>
                        <td class='form-control text-center'> OBRA </td>
                        <td>";
                        echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                        echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                        while ($fila = obtener_resultados($obras)) {
                            extract($fila);
                            $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                            echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                        }
                        echo "</select>
                        </td>
                        </tr>
                        </table>
                        <div class='d-grid gap-2'>                    
                        <br><input type='submit' name='seleccionar' value='SELECCIONAR' class='btn btn-secondary' />
                        <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                        <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                        </div>
                        </form>
                        ";
                    } else {


                        $id_usuario = $_SESSION['id_usuario'];
                        $_SESSION['fechaultimoregistro'] = obtener_fechaultimoregistro($con, $id_usuario);
                        // $fechaultimoregistro = $_SESSION['fechaultimoregistro'];

                        $id_usuario = $_SESSION['id_usuario'];
                        $ultregistro = obtener_ulregistrocabe($con, $id_usuario);
                        while ($registro = obtener_resultados($ultregistro)) {
                            extract($registro);
                            $id_usuario = $_SESSION['id_usuario'];

                            $valor_registro = explode('|', $registro[0]);
                            $fecha = date("d-m-Y");
                            $_SESSION['fecha'] = $fecha;
                            $nombre_obra = $registro[2];
                            $codigo_obra = $registro[3];
                            $empresa_obra = $registro[4];

                            $nombre_jo = $registro[5];
                            $apellidos_jo = $registro[6];

                            $nombre_enc = $registro[7];
                            $apellidos_enc = $registro[8];
                        }


                        echo "<form method='post' action='registrocabe.php'>";
                        echo "<br><table id='fecha' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> FECHA </td>
                    <td>$fecha</td>
                    <td>
                    <div class='d-grid gap-2'>                    
                    <input type='submit' name='selecfecha' value='FECHA MANUAL' class='btn btn-secondary'/>
                    </div>
                    </td>
                    </tr>
                    </table>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA MANUAL si quieres introducir una fecha diferente.</div>
                    </form>
                    ";

                        echo "<form method='post' action='registrocabe.php'>";

                        echo "<br><table id='tabla' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> JEFE DE OBRA </td>
                    <td>";
                        echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                        echo "<option selected='selected' value='$nombre_jo|$apellidos_jo'>$nombre_jo $apellidos_jo</option>";

                        while ($fila = obtener_resultados($jefesobra)) {
                            extract($fila);
                            $valor = "$nombre_jo|$apellidos_jo";
                            echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                        }
                        echo "</select>
                    </td>
                    </tr>

                    <tr>
                    <td class='form-control text-center'> ENCARGADO </td>
                    <td>";
                        echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                        echo "<option selected='selected'value='$nombre_enc|$apellidos_enc'>$nombre_enc $apellidos_enc</option>";

                        while ($fila = obtener_resultados($encargados)) {
                            extract($fila);
                            $valor = "$nombre_enc|$apellidos_enc";
                            echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                        }
                        echo "</select>
                    </td>
                    </tr>

                    <tr>
                    <td class='form-control text-center'> OBRA </td>
                    <td>";
                        echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                        echo "<option selected='selected' value='$nombre_obra|$codigo_obra|$empresa_obra'>$nombre_obra $codigo_obra $empresa_obra</option>";

                        while ($fila = obtener_resultados($obras)) {
                            extract($fila);
                            $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                            echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                        }
                        echo "</select>
                    </td>
                    </tr>
                    </table>
                    <div class='d-grid gap-2'>                    
                                            <br><input type='submit' name='seleccionar' value='SELECCIONAR' class='btn btn-secondary' />
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                    </div>
                    </form>
                    ";
                    }
                    echo "<br>Debes rellenar todos los datos.</br>";
                } else if (
                    isset($_POST["datosdelaobra"]) && isset($_POST["datosdeljo"]) && isset($_POST["datosdelenc"]) && isset($_POST["fechamanual"])
                ) {


                    $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fechamanual'])));
                    $_SESSION['fecha'] = $fecha;
                    $id_usuario = $_SESSION['id_usuario'];

                    $obra = $_POST['datosdelaobra'];
                    $_SESSION['obra'] = $obra;

                    $valor_obra = explode('|', $obra[0]);
                    $nombre_obra = $valor_obra[0];
                    $codigo_obra = $valor_obra[1];
                    $empresa_obra = $valor_obra[2];
                    $_SESSION['codigo_obra'] = $codigo_obra;

                    $jefe_obra = $_POST['datosdeljo'];
                    $_SESSION['jefe_obra'] = $jefe_obra;

                    $valor_jo = explode('|', $jefe_obra[0]);
                    $nombre_jo = $valor_jo[0];
                    $apellidos_jo = $valor_jo[1];

                    $encargado = $_POST['datosdelenc'];
                    $_SESSION['encargado'] = $encargado;

                    $valor_enc = explode('|', $encargado[0]);
                    $nombre_enc = $valor_enc[0];
                    $apellidos_enc = $valor_enc[1];

                    $resultado = guardar_horascabecera($con, $fecha, $nombre_obra, $codigo_obra, $empresa_obra, $nombre_jo, $apellidos_jo, $nombre_enc, $apellidos_enc, $id_usuario);
                    // Llamada a la función obtener_id_registro_insertado
                    $idRegistro = obtener_id_registro_insertado($con);
                    $_SESSION['idRegistro'] = $idRegistro;
                    if ($resultado) {
                        $_SESSION['selecfecha'] = $_POST['fechamanual'];

                        $horascabecera = obtener_horascabecera($con);
                        while ($fila = obtener_resultados($horascabecera)) {
                            extract($fila);
                            $fecha = $_POST['fechamanual'];
                            $tabla3 =  "<form method='post' action='registrocabe.php'>

                        <br>
                        <table id='tabla3' border='1' style='width: 454px; margin: 0 auto; border-collapse: collapse;'>
                            <tr>
                                <td style='border: 1px solid gray; padding: 8px;'>FECHA</td>
                                <td style='border: 1px solid gray; padding: 8px;'>$fecha</td>
                            </tr>
                            <tr>
                                <td style='border: 1px solid gray; padding: 8px;'>JEFE DE OBRA</td>
                                <td style='border: 1px solid gray; padding: 8px;'>$nombre_jo $apellidos_jo</td>
                            </tr>
                            <tr>
                                <td style='border: 1px solid gray; padding: 8px;'>ENCARGADO</td>
                                <td style='border: 1px solid gray; padding: 8px;'>$nombre_enc $apellidos_enc</td>
                            </tr>
                            <tr>
                                <td style='border: 1px solid gray; padding: 8px;'>OBRA</td>
                                <td style='border: 1px solid gray; padding: 8px;'>$nombre_obra $codigo_obra $empresa_obra</td>
                            </tr>
                        </table>

                </form>
                ";
                            echo "<form method='post' action='registrocabe.php'>
                <br>
                <table id='tabla3' border='1' style='width: 454px; margin: 0 auto; border-collapse: collapse;'>
                    <tr>
                        <td style='border: 1px solid gray; padding: 8px;'>FECHA</td>
                        <td style='border: 1px solid gray; padding: 8px;'>$fecha</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid gray; padding: 8px;'>JEFE DE OBRA</td>
                        <td style='border: 1px solid gray; padding: 8px;'>$nombre_jo $apellidos_jo</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid gray; padding: 8px;'>ENCARGADO</td>
                        <td style='border: 1px solid gray; padding: 8px;'>$nombre_enc $apellidos_enc</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid gray; padding: 8px;'>OBRA</td>
                        <td style='border: 1px solid gray; padding: 8px;'>$nombre_obra $codigo_obra $empresa_obra</td>
                    </tr>
                </table>
                <div class='d-grid gap-2'>
                    <br><input type='submit' name='modificar' value='MODIFICAR' class='btn btn-secondary'/>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                        <div class='hidden-text'>Pulsa MODIFICAR si quieres corregir algo.</div>
                </div>
                <div class='d-grid gap-2'>
                    <br><input type='submit' name='validar' value='VALIDAR' class='btn btn-secondary'/>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                        <div class='hidden-text'>Pulsa VALIDAR para continuar.</div>
                </div>
                    </form>
            
                        ";
                        }
                        $_SESSION['tabla3'] = $tabla3;
                        echo "Los datos se han insertado correctamente.";
                    } else {
                        $id_usuario = $_SESSION['id_usuario'];

                        $resultado = obtener_ulregistrocabe($con, $id_usuario);
                        $num_filas = obtener_num_filas($resultado);
                        if ($num_filas == 0) {
                            $id_usuario = $_SESSION['id_usuario'];
                            $fecha = date("d-m-Y");
                            echo "<form method='post' action='registrocabe.php'>";
                            echo "<br><table id='fecha' border='1'  style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> FECHA </td>
                    <td>$fecha</td>
                    <td>
                    <div class='d-grid gap-2'>                    
                    <input type='submit' name='selecfecha' value='FECHA MANUAL' class='btn btn-secondary'/>                    
                    </div>
                    </td>
                    </tr>
                    </table>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA MANUAL si quieres introducir una fecha diferente.</div>
                    </form>
                    ";

                            echo "<form method='post' action='registrocabe.php'>";

                            echo "<br><table id='tabla' border='1'  style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> JEFE DE OBRA </td>
                    <td>";
                            echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                            echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                            while ($fila = obtener_resultados($jefesobra)) {
                                extract($fila);
                                $valor = "$nombre_jo|$apellidos_jo";
                                echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                            }
                            echo "</select>
                    </td>
                    </tr>
        
                    <tr>
                    <td class='form-control text-center'> ENCARGADO </td>
                    <td>";
                            echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                            echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                            while ($fila = obtener_resultados($encargados)) {
                                extract($fila);
                                $valor = "$nombre_enc|$apellidos_enc";
                                echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                            }
                            echo "</select>
                    </td>
                    </tr>
        
                    <tr>
                    <td class='form-control text-center'> OBRA </td>
                    <td>";
                            echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                            echo "<option selected='selected' disabled='disabled' value='Selecciona'></option>";

                            while ($fila = obtener_resultados($obras)) {
                                extract($fila);
                                $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                                echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                            }
                            echo "</select>
                    </td>
                    </tr>
                    </table>
                    <div class='d-grid gap-2'>                    
                                            <br><input type='submit' name='seleccionar' value='SELECCIONAR' class='btn btn-secondary' disabled/>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                    </div>
                    </form>
                    ";
                        } else {


                            $id_usuario = $_SESSION['id_usuario'];
                            $_SESSION['fechaultimoregistro'] = obtener_fechaultimoregistro($con, $id_usuario);
                            // $fechaultimoregistro = $_SESSION['fechaultimoregistro'];

                            $id_usuario = $_SESSION['id_usuario'];
                            $ultregistro = obtener_ulregistrocabe($con, $id_usuario);
                            while ($registro = obtener_resultados($ultregistro)) {
                                extract($registro);
                                $id_usuario = $_SESSION['id_usuario'];

                                $valor_registro = explode('|', $registro[0]);
                                $fecha = date("d-m-Y");
                                $_SESSION['fecha'] = $fecha;
                                $nombre_obra = $registro[2];
                                $codigo_obra = $registro[3];
                                $empresa_obra = $registro[4];

                                $nombre_jo = $registro[5];
                                $apellidos_jo = $registro[6];

                                $nombre_enc = $registro[7];
                                $apellidos_enc = $registro[8];
                            }


                            echo "<form method='post' action='registrocabe.php'>";
                            echo "<br><table id='fecha' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> FECHA </td>
                    <td>$fecha</td>
                    <td>
                    <div class='d-grid gap-2'>                    
                    <input type='submit' name='selecfecha' value='FECHA MANUAL' class='btn btn-secondary'/>
                    </div>
                    </td>
                    </tr>
                    </table>
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA MANUAL si quieres introducir una fecha diferente.</div>
                    </form>
                    ";

                            echo "<form method='post' action='registrocabe.php'>";

                            echo "<br><table id='tabla' border='1' style='width: 454px; margin: 0 auto;'>
                    <tr>
                    <td class='form-control text-center'> JEFE DE OBRA </td>
                    <td>";
                            echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                            echo "<option selected='selected' value='$nombre_jo|$apellidos_jo'>$nombre_jo $apellidos_jo</option>";

                            while ($fila = obtener_resultados($jefesobra)) {
                                extract($fila);
                                $valor = "$nombre_jo|$apellidos_jo";
                                echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                            }
                            echo "</select>
                    </td>
                    </tr>
        
                    <tr>
                    <td class='form-control text-center'> ENCARGADO </td>
                    <td>";
                            echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                            echo "<option selected='selected'value='$nombre_enc|$apellidos_enc'>$nombre_enc $apellidos_enc</option>";

                            while ($fila = obtener_resultados($encargados)) {
                                extract($fila);
                                $valor = "$nombre_enc|$apellidos_enc";
                                echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                            }
                            echo "</select>
                    </td>
                    </tr>
        
                    <tr>
                    <td class='form-control text-center'> OBRA </td>
                    <td>";
                            echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                            echo "<option selected='selected' value='$nombre_obra|$codigo_obra|$empresa_obra'>$nombre_obra $codigo_obra $empresa_obra</option>";

                            while ($fila = obtener_resultados($obras)) {
                                extract($fila);
                                $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                                echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                            }
                            echo "</select>
                    </td>
                    </tr>
                    </table>
                    <div class='d-grid gap-2'>                    
                                            <br><input type='submit' name='seleccionar' value='SELECCIONAR' class='btn btn-secondary' />
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                    </div>
                    </form>
                    ";
                        }
                    }
                }
            }


            if (isset($_POST['volverfechahoy'])) {
                $id_usuario = $_SESSION['id_usuario'];
                $ultregistro = obtener_ulregistrocabe($con, $id_usuario);
                while ($registro = obtener_resultados($ultregistro)) {
                    extract($registro);
                    $id_usuario = $_SESSION['id_usuario'];

                    $valor_registro = explode('|', $registro[0]);
                    $fecha = date("d-m-Y");
                    $_SESSION['fecha'] = $fecha;

                    $nombre_obra = $registro[2];
                    $codigo_obra = $registro[3];
                    $empresa_obra = $registro[4];

                    $nombre_jo = $registro[5];
                    $apellidos_jo = $registro[6];

                    $nombre_enc = $registro[7];
                    $apellidos_enc = $registro[8];
                }

                $fecha = date("d-m-Y");
                echo "<form method='post' action='registrocabe.php'>";
                echo "<br><table id='fecha' border='1'  style='width: 454px; margin: 0 auto;'>
                <tr>
                <td class='form-control text-center'> FECHA </td>
                <td>$fecha</td>
                <td>
                <div class='d-grid gap-2'>                    
                <input type='submit' name='selecfecha' value='FECHA MANUAL' class='btn btn-secondary'/>                
                </div>
                </td>
                </tr>
                </table>
                <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Pulsa FECHA MANUAL si quieres introducir una fecha diferente.</div>
                </form>
                ";

                echo "<form method='post' action='registrocabe.php'>";

                echo "<br><table id='tabla' border='1' style='width: 454px; margin: 0 auto;'>
                <tr>
                <td class='form-control text-center'> JEFE DE OBRA </td>
                <td>";
                echo "<select name='datosdeljo[]' class='form-select mb-3'>";
                echo "<option selected='selected' value='$nombre_jo|$apellidos_jo'>$nombre_jo $apellidos_jo</option>";

                while ($fila = obtener_resultados($jefesobra)) {
                    extract($fila);
                    $valor = "$nombre_jo|$apellidos_jo";
                    echo "<option value='$valor'>$nombre_jo $apellidos_jo</option>"; //Doy a escoger los jefes de obra
                }
                echo "</select>
                </td>
                </tr>

                <tr>
                <td class='form-control text-center'> ENCARGADO </td>
                <td>";
                echo "<select name='datosdelenc[]' class='form-select mb-3'>";
                echo "<option selected='selected'value='$nombre_enc|$apellidos_enc'>$nombre_enc $apellidos_enc</option>";

                while ($fila = obtener_resultados($encargados)) {
                    extract($fila);
                    $valor = "$nombre_enc|$apellidos_enc";
                    echo "<option value='$valor'>$nombre_enc $apellidos_enc</option>"; //Doy a escoger los encargados
                }
                echo "</select>
                    </td>
                    </tr>

                    <tr>
                    <td class='form-control text-center'> OBRA </td>
                    <td>";
                echo "<select name='datosdelaobra[]' class='form-select mb-3'>";
                echo "<option selected='selected' value='$nombre_obra|$codigo_obra|$empresa_obra'>$nombre_obra $codigo_obra $empresa_obra</option>";

                while ($fila = obtener_resultados($obras)) {
                    extract($fila);
                    $valor = "$nombre_obra|$codigo_obra|$empresa_obra";
                    echo "<option value='$valor'>$nombre_obra $codigo_obra $empresa_obra</option>"; //Doy a escoger las obras
                }
                echo "</select>
                    </td>
                    </tr>
                    </table>
                    <div class='d-grid gap-2'>                    
                                            <br><input type='submit' name='seleccionar' value='SELECCIONAR' class='btn btn-secondary' />
                    <img src='IMG/info.png' alt='Icono Información' class='infoIcon' />
                    <div class='hidden-text'>Escoge fecha, jefe de obra, encargado, obra y pulsa SELECCIONAR.</div>
                    </div>
                    </form>
                    ";
            }

            if (isset($_POST['validar'])) {
                $tabla3 = $_SESSION['tabla3'];
                echo '<meta http-equiv="refresh" content="0;URL=añadiremp.php">';
                exit();
            }

            ?>

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
                    $('#fechamanual').datepicker($.datepicker.regional['es']);



                    // Validación de fecha
                    $('#fechamanual').on('change', function() {
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



                });
            </script>

            <form method='post' action='user.php'>
                <br><br>
                <p><input type="submit" name="volver" value="VOLVER" class='btn btn-secondary' />

            </form>
        </div>
    </div>
</body>

</html>