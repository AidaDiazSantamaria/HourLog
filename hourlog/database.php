<?php
//Crear una base de datos en MySQL con las siguientes tablas:

$host = "localhost"; //Conectamos
$usu = "user";
$pw = "aid4pp*";
$bdd_nom = "horas";

//Función que conecta con la base de datos según las credenciales indicadas y llama a las funciones necesarias para crear las tablas
function conectar()
{
    $con = mysqli_connect($GLOBALS["host"], $GLOBALS["usu"], $GLOBALS["pw"]) or die("No se ha podido conectar con la base de datos");
    crear_bdd($con);
    mysqli_select_db($con, $GLOBALS["bdd_nom"]);
    crear_tabla_usuario($con);
    crear_tabla_obra($con);
    crear_tabla_empleado($con);
    crear_tabla_encargado($con);
    crear_tabla_jefeobra($con);
    crear_tabla_horascabecera($con);
    crear_tabla_horascuerpo($con);
    crear_tabla_provincias($con);

    return $con;
}

//Función para crear la base de datos, si no existiera
function crear_bdd($con)
{
    mysqli_query($con, "CREATE DATABASE IF NOT EXISTS horas;");
}

//Función que crea la tabla USUARIO
function crear_tabla_usuario($con)
{
    mysqli_query(
        $con,
        "CREATE TABLE IF NOT EXISTS usuario
        (
            id_usuario INT PRIMARY KEY AUTO_INCREMENT,
            nombre_usu VARCHAR(255) BINARY,
            pw VARCHAR(255),
            tipo_usuario TINYINT,
            mail VARCHAR(255)
        );
        "
    ); //1 para admin, 2 para usuarios con acceso limitado

    rellenar_tabla_usuario($con); //Y la rellena
}

//Función que crea la tabla EMPLEADO
function crear_tabla_empleado($con)
{
    mysqli_query(
        $con,
        "CREATE TABLE IF NOT EXISTS empleado
        (
            id_empleado INT PRIMARY KEY AUTO_INCREMENT,
            nombre_emp VARCHAR(255),
            apellidos_emp VARCHAR(255),
            empresa VARCHAR(255),
            dni VARCHAR(255),
            tel INT
        );
        "
    );

    rellenar_tabla_empleado($con); //Y la rellena
}

//Función que crea la tabla ENCARGADO
function crear_tabla_encargado($con)
{
    mysqli_query(
        $con,
        "CREATE TABLE IF NOT EXISTS encargado
        (
            id_encargado INT PRIMARY KEY AUTO_INCREMENT,
            nombre_enc VARCHAR(255),
            apellidos_enc VARCHAR(255)
        );
        "
    );

    rellenar_tabla_encargado($con); //Y la rellena
}

//Función que crea la tabla JEFEOBRA
function crear_tabla_jefeobra($con)
{
    mysqli_query(
        $con,
        "CREATE TABLE IF NOT EXISTS jefeobra
        (
            id_jo INT PRIMARY KEY AUTO_INCREMENT,
            nombre_jo VARCHAR(255),
            apellidos_jo VARCHAR(255)
        );
        "
    );

    rellenar_tabla_jefeobra($con); //Y la rellena
}

//Función que crea la tabla OBRA
function crear_tabla_obra($con)
{
    mysqli_query(
        $con,
        "CREATE TABLE IF NOT EXISTS obra
        (
            id_obra INT PRIMARY KEY AUTO_INCREMENT,
            nombre_obra VARCHAR(255),
            provincia_obra VARCHAR(255),
            codigo_obra VARCHAR(255),
            empresa_obra VARCHAR(255)
        );
        "
    );

    rellenar_tabla_obra($con); //Y la rellena

}

//Función que crea la tabla PROVINCIAS
function crear_tabla_provincias($con)
{
    mysqli_query(
        $con,
        "CREATE TABLE IF NOT EXISTS provincias (
            id_provincia INT PRIMARY KEY AUTO_INCREMENT,
            provincia VARCHAR(255),
            preciohorasextra FLOAT,
            preciohorasextrafestivo FLOAT
        );
        "
    );
}

//Función que crea la tabla HORASCABECERA

function crear_tabla_horascabecera($con)
{
    mysqli_query(
        $con,
        "CREATE TABLE IF NOT EXISTS horascabecera (
            id_horascabe INT PRIMARY KEY AUTO_INCREMENT,
            fecha DATE,
            nombre_obra VARCHAR(255),
            codigo_obra VARCHAR(255),
            empresa_obra VARCHAR(255),
            nombre_jo VARCHAR(255),
            apellidos_jo VARCHAR(255),
            nombre_enc VARCHAR(255),
            apellidos_enc VARCHAR(255),
            id_usuario INT
        );
        "
    );
}

//Función que crea la tabla HORASCUERPO

function crear_tabla_horascuerpo($con)
{
    mysqli_query(
        $con,
        "CREATE TABLE IF NOT EXISTS horascuerpo (
            id_horascuerpo INT PRIMARY KEY AUTO_INCREMENT,
            nombre_emp VARCHAR(255),
            apellidos_emp VARCHAR(255),
            empresa_emp VARCHAR(255),
            dni_emp VARCHAR(255),
            tel_emp INT,
            horas INT,
            horas_a_parte INT,
            vacaciones ENUM('sí', 'no'),
            falta ENUM('sí', 'no'),
            festivo ENUM('sí', 'no'),
            baja ENUM('sí', 'no'),
            fecha DATE,
            provincia VARCHAR(255),
            nombre_obra VARCHAR(255),
            codigo_obra VARCHAR(255),
            empresa_obra VARCHAR(255),
            nombre_jo VARCHAR(255),
            apellidos_jo VARCHAR(255),
            nombre_enc VARCHAR(255),
            apellidos_enc VARCHAR(255),
            id_usuario INT
        );
        "
    );
}
//------CREAR------//

//Función para crear nuevos usuarios
function crear_usuario($con, $nombre_usu, $pw, $tipo_usuario, $mail)
{
    // Verificar si ya existe un usuario con el mismo nombre
    $query = "SELECT COUNT(*) FROM usuario WHERE nombre_usu = '$nombre_usu'";
    $resultado = mysqli_query($con, $query);

    // Obtener el resultado de la consulta
    $row = mysqli_fetch_array($resultado);
    $existeUsuario = $row[0];

    // Si ya existe un usuario con el mismo nombre, mostrar mensaje de error y salir de la función
    if ($existeUsuario > 0) {
        echo "Error: Ya existe un usuario con el nombre $nombre_usu";
        return false;
    }

    // Si no existe un usuario duplicado, realizar la inserción
    $query = "INSERT INTO usuario (nombre_usu, pw, tipo_usuario, mail) VALUES ('$nombre_usu', '$pw', '$tipo_usuario', '$mail')";
    $resultado = mysqli_query($con, $query);
    echo "<br>Usuario creado correctamente:<br>Nombre: $nombre_usu<br>Contraseña: $pw<br> Tipo: $tipo_usuario <br> Correo electrónico: $mail";

    return $resultado;
}


//Función para crear nuevos empleados
function crear_empleado($con, $nombre_emp, $apellidos_emp, $empresa, $dni, $tel)
{
    // Verificar si ya existe un empleado con el mismo nombre
    $query = "SELECT COUNT(*) FROM empleado WHERE dni = '$dni'";
    $resultado = mysqli_query($con, $query);

    // Obtener el resultado de la consulta
    $row = mysqli_fetch_array($resultado);
    $existeEmpleado = $row[0];

    // Si ya existe un empleado con el mismo nombre, mostrar mensaje de error y salir de la función
    if ($existeEmpleado > 0) {
        echo "Error: Ya existe un empleado con el DNI $dni";
        return false;
    }

    // Si no existe un empleado duplicado, realizar la inserción
    $query = "INSERT INTO empleado (nombre_emp, apellidos_emp, empresa, dni, tel) VALUES ('$nombre_emp', '$apellidos_emp', '$empresa', '$dni', '$tel')";
    $resultado = mysqli_query($con, $query);
    echo "<br>Empleado creado correctamente:<br>Nombre: $nombre_emp<br>Apellidos: $apellidos_emp<br>Empresa: $empresa<br>DNI: $dni<br>Teléfono: $tel";

    return $resultado;
}

// Función para crear nuevos encargados
function crear_encargado($con, $nombre_enc, $apellidos_enc)
{
    // Verificar si ya existe un encargado con el mismo nombre
    $query = "SELECT COUNT(*) FROM encargado WHERE nombre_enc = '$nombre_enc' AND apellidos_enc = '$apellidos_enc'";
    $resultado = mysqli_query($con, $query);

    // Obtener el resultado de la consulta
    $row = mysqli_fetch_array($resultado);
    $existeEncargado = $row[0];

    // Si ya existe un encargado con el mismo nombre, mostrar mensaje de error y salir de la función
    if ($existeEncargado > 0) {
        echo "Error: Ya existe un encargado con el nombre $nombre_enc $apellidos_enc";
        return false;
    }

    // Si no existe un encargado duplicado, realizar la inserción
    $query = "INSERT INTO encargado(nombre_enc, apellidos_enc) VALUES ('$nombre_enc', '$apellidos_enc')";
    $resultado = mysqli_query($con, $query);
    echo "<br>Encargado creado correctamente:<br>Nombre: $nombre_enc<br>Apellidos: $apellidos_enc";

    return $resultado;
}


//Función para crear nuevos jefes de obra
function crear_jefeobra($con, $nombre_jo, $apellidos_jo)
{
    // Verificar si ya existe un jefe de obra con el mismo nombre y apellidos
    $query = "SELECT COUNT(*) FROM jefeobra WHERE nombre_jo = '$nombre_jo' AND apellidos_jo = '$apellidos_jo'";
    $resultado = mysqli_query($con, $query);

    // Obtener el resultado de la consulta
    $row = mysqli_fetch_array($resultado);
    $existeJefeObra = $row[0];

    // Si ya existe un jefe de obra con el mismo nombre y apellidos, mostrar mensaje de error y salir de la función
    if ($existeJefeObra > 0) {
        echo "Error: Ya existe un jefe de obra con el nombre $nombre_jo $apellidos_jo";
        return false;
    }

    // Si no existe un jefe de obra duplicado, realizar la inserción
    $query = "INSERT INTO jefeobra(nombre_jo, apellidos_jo) VALUES ('$nombre_jo', '$apellidos_jo')";
    $resultado = mysqli_query($con, $query);
    echo "<br>Jefe de obra creado correctamente:<br>Nombre: $nombre_jo<br>Apellidos: $apellidos_jo";

    return $resultado;
}


// Función para crear nuevas obras
function crear_obra($con, $nombre_obra, $provincia_obra, $codigo_obra, $empresa_obra)
{
    // Comprobar si ya existe una obra con el mismo código
    $query = "SELECT codigo_obra FROM obra WHERE codigo_obra = '$codigo_obra'";
    $resultado = mysqli_query($con, $query);

    if (mysqli_num_rows($resultado) > 0) {
        // Ya existe una obra con el mismo código
        echo "Error: Ya existe una obra con el código $codigo_obra";

        return false;
    } else {
        // Insertar la nueva obra
        $query = "INSERT INTO obra(nombre_obra, provincia_obra, codigo_obra, empresa_obra) VALUES ('$nombre_obra', '$provincia_obra', '$codigo_obra', '$empresa_obra')";
        $resultado = mysqli_query($con, $query);
        echo "<br>Obra creada correctamente:<br>Nombre: $nombre_obra<br>Provincia: $provincia_obra <br>Código: $codigo_obra <br>Empresa: $empresa_obra";

        return $resultado;
    }
}


// Función para crear nuevas provincias
function crear_provincia($con, $provincia, $preciohorasextra, $preciohorasextrafestivo)
{
    // Comprobar si ya existe una provincia con el mismo nombre
    $query = "SELECT provincia FROM provincias WHERE provincia = '$provincia'";
    $resultado = mysqli_query($con, $query);
    
    if (mysqli_num_rows($resultado) > 0) {
        // Ya existe una provincia con el mismo nombre
        echo "<div class='text-center'><br/>Ya existe otra provincia con el nombre $provincia. <br></div>";
    } else {
        // Insertar la provincia
        $query = "INSERT INTO provincias(provincia, preciohorasextra, preciohorasextrafestivo) VALUES ('$provincia', '$preciohorasextra', '$preciohorasextrafestivo')";
        mysqli_query($con, $query);
        echo "<div class='text-center'><br/>Provincia creada correctamente:<br>Nombre: $provincia<br>Precio hora extra: $preciohorasextra<br>Precio hora extra festivo: $preciohorasextrafestivo</div>";
    }
}


//Función para crear nuevos registros de horas en las tablas HORASCABECERA

function guardar_horascabecera($con, $fecha, $nombre_obra, $codigo_obra, $empresa_obra, $nombre_jo, $apellidos_jo, $nombre_enc, $apellidos_enc, $id_usuario)
{
    // Comprobar si ya existe un registro con la misma fecha y código de obra
    $query = "SELECT * FROM horascabecera WHERE fecha='$fecha' AND codigo_obra='$codigo_obra'";
    $resultado = mysqli_query($con, $query);
    if (mysqli_num_rows($resultado) > 0) {
        // Ya existe un registro con la misma fecha y código de obra, mostrar mensaje de error
        echo "<div class='text-center'><br/>Ya existe un registro para la fecha y código de obra seleccionados.";
    } else {
        // No existe un registro con la misma fecha y código de obra, realizar la inserción en la tabla HORASCABECERA
        $query_insert = "INSERT INTO horascabecera (fecha, nombre_obra, codigo_obra, empresa_obra, nombre_jo, apellidos_jo, nombre_enc, apellidos_enc, id_usuario) 
                         VALUES ('$fecha', '$nombre_obra', '$codigo_obra', '$empresa_obra', '$nombre_jo', '$apellidos_jo', '$nombre_enc', '$apellidos_enc', '$id_usuario')";
        $resultado_cabecera = mysqli_query($con, $query_insert);

        // Devolver el resultado de la inserción en la tabla HORASCABECERA 
        return array($resultado_cabecera);
    }
}


//Función para crear nuevos registros de horas en las tablas HORASCUERPO

function guardar_horascuerpo($con, $nombre_emp, $apellidos_emp, $empresa_emp, $dni_emp, $tel_emp, $horas, $horas_a_parte, $vacaciones, $falta, $festivo, $baja, $fecha, $provincia, $nombre_obra, $codigo_obra, $empresa_obra, $nombre_jo, $apellidos_jo, $nombre_enc, $apellidos_enc, $id_usuario)
{
    // Verificar si ya existe un registro con el mismo dni_emp y fecha
    $existe_registro = mysqli_query($con, "SELECT * FROM horascuerpo WHERE dni_emp = '$dni_emp' AND fecha = '$fecha' AND codigo_obra = '$codigo_obra'");
    $num_registros = mysqli_num_rows($existe_registro);

    // Si no existe ningún registro, realizar la inserción
    if ($num_registros == 0) {
        // insertar en la tabla HORASCUERPO
        $resultado_cuerpo = mysqli_query($con, "INSERT INTO horascuerpo(nombre_emp, apellidos_emp, empresa_emp, dni_emp, tel_emp, horas, horas_a_parte, vacaciones, falta, festivo, baja, fecha, provincia, nombre_obra, codigo_obra, empresa_obra, nombre_jo, apellidos_jo, nombre_enc, apellidos_enc, id_usuario) 
        VALUES ('$nombre_emp', '$apellidos_emp', '$empresa_emp', '$dni_emp', '$tel_emp', '$horas', '$horas_a_parte', '$vacaciones', '$falta', '$festivo', '$baja', '$fecha', '$provincia', '$nombre_obra', '$codigo_obra', '$empresa_obra', '$nombre_jo', '$apellidos_jo', '$nombre_enc', '$apellidos_enc', '$id_usuario')");

        // devolver el resultado de la inserción en la tabla HORASCUERPO
        return $resultado_cuerpo;
    } else {
        // Si ya existe un registro con el mismo dni_emp y fecha, no realizar la inserción

        return false;
    }
}


//------BORRAR------//

//Función para borrar usuarios
function borrar_usuario($con, $codigos)
{
    mysqli_query($con, "DELETE FROM usuario WHERE nombre_usu='" . $codigos[0] . "'");
    echo "<div class='text-center'><br>Usuario eliminado correctamente.</div>";
}

//Función para borrar empleados
function borrar_empleado($con, $dni)
{
    mysqli_query($con, "DELETE FROM empleado WHERE dni='$dni'");
    echo "<div class='text-center'><br>Empleado eliminado correctamente.</div>";
}

//Función para borrar encargados
function borrar_encargado($con, $nombre_enc, $apellidos_enc)
{
    mysqli_query($con, "DELETE FROM encargado WHERE nombre_enc='$nombre_enc' AND apellidos_enc='$apellidos_enc'");
    echo "<div class='text-center'><br>Encargado eliminado correctamente.</div>";
}

//Función para borrar jefes de obra
function borrar_jefeobra($con, $id_selec)
{
    mysqli_query($con, "DELETE FROM jefeobra WHERE id_jo='$id_selec'");
    echo "<div class='text-center'><br>Jefe de obra eliminado correctamente.</div>";
}

//Función para borrar obras
function borrar_obra($con, $codselec)
{
    mysqli_query($con, "DELETE FROM obra WHERE codigo_obra='$codselec'");
    echo "<div class='text-center'><br>Obra eliminada correctamente.</div>";
}

//Función para borrar provincias
function borrar_provincia($con, $codigos)
{
    mysqli_query($con, "DELETE FROM provincias WHERE provincia='" . $codigos[0] . "'");
    echo "<div class='text-center'><br>Provincia eliminada correctamente.</div>";

}

//Función para borrar registro HORASCABECERA

function eliminar_registroHCAB($con, $idRegistro)
{
    // eliminar el registro de la tabla HORASCABECERA
    $resultado = mysqli_query($con, "DELETE FROM horascabecera WHERE id_horascabe = '$idRegistro'");

    return $resultado;
}

//Función para borrar registro HORASCUERPO

function eliminar_registroHCUE($con, $idBoton)
{
    // eliminar el registro de la tabla HORASCUERPO
    $resultado = mysqli_query($con, "DELETE FROM horascuerpo WHERE id_horascuerpo = '$idBoton'");

    return $resultado;
}

//Función para borrar registro HORASCUERPO por fecha

function eliminar_registroHCUEfecha($con, $fecha)
{
    // eliminar el registro de la tabla HORASCUERPO
    $resultado = mysqli_query($con, "DELETE FROM horascuerpo WHERE fecha = '$fecha'");

    return $resultado;
}

//Función para borrar registro HORASCUERPO por fecha y usuario

function eliminar_registroHCUEfechausu($con, $fecha, $id_usuario)
{
    // eliminar el registro de la tabla HORASCUERPO
    $resultado = mysqli_query($con, "DELETE FROM horascuerpo WHERE fecha = '$fecha' AND id_usuario = '$id_usuario'");

    return $resultado;
}

//Función para borrar registro HORASCABECERA por fecha y usuario

function eliminar_registroHCABfechausu($con, $fecha, $id_usuario)
{
    // eliminar el registro de la tabla HORASCABECERA
    $resultado = mysqli_query($con, "DELETE FROM horascabecera WHERE fecha = '$fecha' AND id_usuario = '$id_usuario'");

    return $resultado;
}
//------MODIFICAR------//

function mod_usuario($con, $codigos, $nombre_usu, $pw, $tipo_usuario, $mail)
{
    if ($codigos[0] === $nombre_usu) {
        // El nombre de usuario es el mismo, se realiza la actualización directamente
        mysqli_query($con, "UPDATE usuario SET nombre_usu='$nombre_usu', pw='$pw', tipo_usuario='$tipo_usuario', mail='$mail' WHERE nombre_usu='" . $codigos[0] . "'");
        echo "<br>Usuario modificado correctamente:<br>Nombre: $nombre_usu<br>Contraseña: $pw<br> Tipo: $tipo_usuario <br> Correo electrónico: $mail";
    } else {
        // Comprobar si el nuevo nombre de usuario ya existe
        $query = "SELECT COUNT(*) as total FROM usuario WHERE nombre_usu = '$nombre_usu'";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);
        $total = $row['total'];

        if ($total > 0) {
            echo "<div class='text-center'><br/>Ya existe otro usuario con el nombre '$nombre_usu'. <br><div class='text-center'><br/>";
        } else {
            // Actualizar los datos del usuario
            mysqli_query($con, "UPDATE usuario SET nombre_usu='$nombre_usu', pw='$pw', tipo_usuario='$tipo_usuario', mail='$mail' WHERE nombre_usu='" . $codigos[0] . "'");
            echo "<br>Usuario modificado correctamente:<br>Nombre: $nombre_usu<br>Contraseña: $pw<br> Tipo: $tipo_usuario <br> Correo electrónico: $mail";
        }
    }
}

//Función para modificar empleados
function mod_empleado($con, $dniselec, $nombre_emp, $apellidos_emp, $empresa, $dni, $tel)
{
    if ($dniselec === $dni) {
        // El  dni del emplead es el mismo, se realiza la actualización directamente
        mysqli_query($con, "UPDATE empleado SET nombre_emp='$nombre_emp', apellidos_emp='$apellidos_emp', empresa='$empresa', dni='$dni', tel='$tel' WHERE dni='$dniselec'");
        echo "<div class='text-center'><br/>Empleado modificado correctamente:<br>Nombre: $nombre_emp<br>Apellidos: $apellidos_emp<br>Empresa: $empresa<br>DNI: $dni<br>Teléfono: $tel<div class='text-center'><br/>";
    } else {
        // Comprobar si el nuevo dni del empleado ya existe
        $query = "SELECT COUNT(*) as total FROM empleado WHERE dni = '$dni'";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);
        $total = $row['total'];

        if ($total > 0) {
            echo "<div class='text-center'><br/>Ya existe otro empleado con el DNI '$dni'. <br></div>";
        } else {
            // Actualizar los datos del usuario
            mysqli_query($con, "UPDATE empleado SET nombre_emp='$nombre_emp', apellidos_emp='$apellidos_emp', empresa='$empresa', dni='$dni', tel='$tel' WHERE dni='$dniselec'");
            echo "<div class='text-center'><br/>Empleado modificado correctamente:<br>Nombre: $nombre_emp<br>Apellidos: $apellidos_emp<br>Empresa: $empresa<br>DNI: $dni<br>Teléfono: $tel<div class='text-center'><br/>";
        }
    }
}

//Función para modificar encargados
function mod_encargado($con, $id_encargado, $nombre_enc, $apellidos_enc)
{
    $nombre_enc = mysqli_real_escape_string($con, $nombre_enc);
    $apellidos_enc = mysqli_real_escape_string($con, $apellidos_enc);

    // Comprobar si existe otro encargado con el mismo nombre y apellidos, exceptuando el registro del id_encargado
    $query = "SELECT COUNT(*) AS total FROM encargado WHERE id_encargado != $id_encargado AND nombre_enc='$nombre_enc' AND apellidos_enc='$apellidos_enc'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $total = $row['total'];

    if ($total > 0) {
        echo "<div class='text-center'><br/>Ya existe otro encargado con el nombre '$nombre_enc $apellidos_enc'. <br></div>";
    } else {
        // Realizar la actualización
        mysqli_query($con, "UPDATE encargado SET nombre_enc='$nombre_enc', apellidos_enc='$apellidos_enc' WHERE id_encargado=$id_encargado");
        echo "<div class='text-center'><br/>Encargado modificado correctamente:<br>Nombre: $nombre_enc<br>Apellidos: $apellidos_enc<br></div>";
    }
}



//Función para modificar jefes de obra
function mod_jefeobra($con, $id_selec, $nombre_jo, $apellidos_jo)
{
    $nombre_jo = mysqli_real_escape_string($con, $nombre_jo);
    $apellidos_jo = mysqli_real_escape_string($con, $apellidos_jo);

    // Comprobar si existe otro jefe de obra con el mismo nombre y apellidos, exceptuando el registro del id_selec
    $query = "SELECT COUNT(*) AS total FROM jefeobra WHERE id_jo != $id_selec AND nombre_jo='$nombre_jo' AND apellidos_jo='$apellidos_jo'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $total = $row['total'];

    if ($total > 0) {
        echo "<div class='text-center'><br/>Ya existe otro jefe de obra con el nombre '$nombre_jo $apellidos_jo'. <br></div>";
    } else {
        // Realizar la actualización
        mysqli_query($con, "UPDATE jefeobra SET nombre_jo='$nombre_jo', apellidos_jo='$apellidos_jo' WHERE id_jo='$id_selec'");
        echo "<div class='text-center'><br/>Jefe de obra modificado correctamente:<br>Nombre: $nombre_jo<br>Apellidos: $apellidos_jo<br></div>";
    }
}



// Función para modificar obras
function mod_obra($con, $id_obra, $nombre_obra, $provincia_obra, $codigo_obra, $empresa_obra)
{
    // Comprobar si ya existe otra obra con el mismo código
    $query = "SELECT COUNT(*) AS total FROM obra WHERE id_obra != $id_obra AND codigo_obra='$codigo_obra'";
    $resultado = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($resultado);
    $total = $row['total'];

    if ($total > 0) {
        echo "<div class='text-center'><br/>Ya existe otra obra con el código $codigo_obra. <br></div>";
    } else {
        // Realizar la actualización
        mysqli_query($con, "UPDATE obra SET nombre_obra='$nombre_obra', provincia_obra='$provincia_obra', codigo_obra='$codigo_obra', empresa_obra='$empresa_obra' WHERE id_obra='$id_obra'");
        echo "<div class='text-center'><br/>Obra modificada correctamente:<br>Nombre: $nombre_obra<br>Provincia: $provincia_obra <br>Código: $codigo_obra <br>Empresa: $empresa_obra</div>";
    }
}





// Función para modificar provincias
function mod_provincia($con, $id_provincia, $provincia, $preciohorasextra, $preciohorasextrafestivo)
{
    // Comprobar si ya existe una provincia con el mismo nombre, excepto el del id_provincia
    $query = "SELECT COUNT(*) AS total FROM provincias WHERE id_provincia != $id_provincia AND provincia='$provincia'";
    $resultado = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($resultado);
    $total = $row['total'];

    if ($total > 0) {
        // Ya existe otra provincia con el mismo nombre
        echo "<div class='text-center'><br/>Ya existe otra provincia con el nombre $provincia. <br></div>";
    } else {
        // Modificar la provincia
        $query = "UPDATE provincias SET provincia='$provincia', preciohorasextra='$preciohorasextra', preciohorasextrafestivo='$preciohorasextrafestivo' WHERE id_provincia='$id_provincia'";
        mysqli_query($con, $query);
        echo "<div class='text-center'><br/>Provincia modificada correctamente:<br>Nombre: $provincia<br>Precio hora extra: $preciohorasextra<br>Precio hora extra festivo: $preciohorasextrafestivo</div>";
    }
}


//------RELLENAR------//

//Función para rellenar la tabla USUARIO 
function rellenar_tabla_usuario($con)
{

    $resultado = obtener_usuarios($con);
    if (obtener_num_filas($resultado) == 0) {
        mysqli_query($con, "INSERT INTO usuario (nombre_usu, pw, tipo_usuario) VALUES ('Admin', '1234', 1)");
        mysqli_query($con, "INSERT INTO usuario (nombre_usu, pw, tipo_usuario) VALUES ('User', '4567', 2)");
    }
}

//Función para rellenar la tabla EMPLEADO 
function rellenar_tabla_empleado($con)
{

    $resultado = obtener_empleados($con);
    if (obtener_num_filas($resultado) == 0) {
        mysqli_query($con, "INSERT INTO empleado (nombre_emp, apellidos_emp, empresa, dni, tel) VALUES ('Emp1', 'AP1', 'Ogensa', '77777777R', '777777777')");
        mysqli_query($con, "INSERT INTO empleado (nombre_emp, apellidos_emp, empresa, dni, tel) VALUES ('Emp2', 'AP2', 'Ogensa', '55555555K', '888888888')");
    }
}

//Función para rellenar la tabla ENCARGADO 
function rellenar_tabla_encargado($con)
{

    $resultado = obtener_encargados($con);
    if (obtener_num_filas($resultado) == 0) {
        mysqli_query($con, "INSERT INTO encargado (nombre_enc, apellidos_enc) VALUES ('Enc1', 'Apellido')");
        mysqli_query($con, "INSERT INTO encargado (nombre_enc, apellidos_enc) VALUES ('Enc2', 'Apellido')");
    }
}

//Función para rellenar la tabla JEFEOBRA 
function rellenar_tabla_jefeobra($con)
{

    $resultado = obtener_jefesobra($con);
    if (obtener_num_filas($resultado) == 0) {
        mysqli_query($con, "INSERT INTO jefeobra (nombre_jo, apellidos_jo) VALUES ('JO1', 'Apellido')");
        mysqli_query($con, "INSERT INTO jefeobra (nombre_jo, apellidos_jo) VALUES ('JO2', 'Apellido')");
    }
}

//Función para rellenar la tabla OBRA 
function rellenar_tabla_obra($con)
{
    $resultado = obtener_obras($con);
    if (obtener_num_filas($resultado) == 0) {
        mysqli_query($con, "INSERT INTO obra (nombre_obra, provincia_obra, codigo_obra, empresa_obra) VALUES ('Obra1', 'Asturias', '1234', 'empresa')");
        mysqli_query($con, "INSERT INTO obra(nombre_obra, provincia_obra, codigo_obra, empresa_obra) VALUES ('Obra2', 'León', '6789', 'empresa')");
    }
}

//------OTRAS FUNCIONES------//

//Función para obtener todos los usuarios
function obtener_usuarios($con)
{
    $resultado = mysqli_query($con, "SELECT * FROM usuario");

    return $resultado;
}

//Función para obtener todos los empleados
function obtener_empleados($con)
{
    $resultado = mysqli_query($con, "SELECT * FROM empleado");

    return $resultado;
}

//Función para obtener todos los encargados
function obtener_encargados($con)
{
    $resultado = mysqli_query($con, "SELECT * FROM encargado");

    return $resultado;
}

//Función para obtener todos los jefes de obra
function obtener_jefesobra($con)
{
    $resultado = mysqli_query($con, "SELECT * FROM jefeobra");

    return $resultado;
}

//Función para obtener todas las obras
function obtener_obras($con)
{
    $resultado = mysqli_query($con, "SELECT * FROM obra");

    return $resultado;
}

//Función para obtener todas las provincias
function obtener_provincias($con)
{
    $resultado = mysqli_query($con, "SELECT * FROM provincias");

    return $resultado;
}

//Función para obtener el último registro de la tabla HORASCABECERA
function obtener_horascabecera($con)
{
    $resultado = mysqli_query($con, "SELECT * FROM horascabecera ORDER BY id_horascabe DESC LIMIT 1");

    return $resultado;
}

//Función para obtener el último registro de la tabla HORASCUERPO
function obtener_horascuerpo($con, $fecha)
{
    $resultado = mysqli_query($con, "SELECT * FROM horascuerpo WHERE fecha = '$fecha'");

    return $resultado;
}

//Función para obtener los meses y años de la tabla HORASCUERPO
function obtener_mesanios($con)
{
    $resultado = mysqli_query($con, "SELECT DISTINCT MONTH(fecha) AS mes, YEAR(fecha) AS año FROM horascuerpo");

    return $resultado;
}

//Función para obtener el registro que coincida con el mes seleccionado de la tabla HORASCUERPO
function obtener_registros_mes($con, $mes, $año)
{
    $resultado = mysqli_query($con, "SELECT * FROM horascuerpo WHERE YEAR(fecha) = $año AND MONTH(fecha) = $mes");

    return $resultado;
}


//Función para obtener el último registro de la tabla HORASCABECERA, de un usuario concreto
function obtener_ulregistrocabe($con, $id_usuario)
{
    $resultado = mysqli_query($con, "SELECT * FROM horascabecera WHERE id_usuario = '$id_usuario' ORDER BY id_horascabe DESC LIMIT 1");

    return $resultado;
}

//Función para obtener el último registro de la tabla HORASCABECERA, de una fecha concreta
function obtener_porfechacabe($con, $id_usuario, $fecha)
{
    $resultado = mysqli_query($con, "SELECT * FROM horascabecera WHERE id_usuario = '$id_usuario' AND fecha = '$fecha'");

    return $resultado;
}

//Función para obtener el último registro de la tabla HORASCABECERA, de un usuario concreto
function obtener_ulregistrocuerpo($con, $id_usuario, $fecha, $codigo_obra)
{
    $resultado = mysqli_query($con, "SELECT * FROM horascuerpo WHERE id_usuario = '$id_usuario' AND fecha = '$fecha' AND codigo_obra = '$codigo_obra' ORDER BY id_horascuerpo");

    return $resultado;
}

//Función para obtener el id delúltimo registro de la tabla HORASCABECERA, de un usuario concreto
function obtener_idulregistrocuerpo($con, $id_usuario, $fecha, $codigo_obra)
{
    $resultado = mysqli_query($con, "SELECT * FROM horascabecera WHERE id_usuario = '$id_usuario' AND fecha = '$fecha' AND codigo_obra = '$codigo_obra' ORDER BY id_horascabe");

    return $resultado;
}

//Función para obtener fecha último registro
function obtener_fechaultimoregistro($con, $id_usuario)
{
    $resultado = mysqli_query($con, "SELECT fecha FROM horascabecera WHERE id_usuario = '$id_usuario' ORDER BY id_horascabe DESC LIMIT 1");

    $fechaultimoregistro = mysqli_fetch_array($resultado)['fecha'];

    return $fechaultimoregistro;
}

//Función para obtener fecha último registro cuerpo
function obtener_fechaultimoregistrocuerpo($con, $id_usuario)
{
    $resultado = mysqli_query($con, "SELECT fecha FROM horascuerpo WHERE id_usuario = '$id_usuario' ORDER BY id_horascuerpo DESC LIMIT 1");

    $row = mysqli_fetch_array($resultado);
    $fechaultimoregistro = null;
    if ($row !== null && isset($row['fecha'])) {
        $fechaultimoregistro = $row['fecha'];
    }

    return $fechaultimoregistro;
}


//Función para recorrer el array que indiquemos y sacar un resultado
function obtener_resultados($resultado)
{
    return mysqli_fetch_array($resultado);
}

//Función que cuenta las filas existentes
function obtener_num_filas($resultado)
{
    return mysqli_num_rows($resultado);
}

//Función para  Obtener el ID del registro insertado en la tabla HORASCABECERA
function obtener_id_registro_insertado($con)
{
    // Obtener el ID del registro insertado en la tabla HORASCABECERA
    $idRegistro = mysqli_insert_id($con);

    // Devolver el ID del registro insertado
    return $idRegistro;
}

//Función para obtener tipo usuario
function obtener_tipousu($con, $usuario, $contraseña)
{
    $resultado = mysqli_query($con, "SELECT tipo_usuario FROM usuario WHERE nombre_usu='" . $usuario . "' AND pw='" . $contraseña . "'");

    return $resultado;
}

//Función para obtener id usuario
function obtener_idusu($con, $user)
{
    $resultado = mysqli_query($con, "SELECT id_usuario FROM usuario WHERE nombre_usu = '$user'");
    $id_usuario = mysqli_fetch_array($resultado)['id_usuario'];

    return intval($id_usuario);
}

//Función para obtener provincia
function obtener_provincia($con, $codigo_obra)
{
    $resultado = mysqli_query($con, "SELECT provincia_obra FROM obra WHERE codigo_obra = '$codigo_obra'");

    return $resultado;
}

//Función para seleccionar jefes de obra
function selecJo($con, $codjo)
{
    $resultado = mysqli_query($con, "SELECT * FROM jefeobra WHERE nombre_jo = '" . $codjo[0] . "'");

    return $resultado; //Devuelvo un array con los datos de todos los usuarios
}

//Función para seleccionar encargados
function selecEnc($con, $codenc)
{
    $resultado = mysqli_query($con, "SELECT * FROM encargado WHERE nombre_enc = '" . $codenc[0] . "'");

    return $resultado; //Devuelvo un array con los datos de todos los usuarios
}

//Función para seleccionar obras
function selecObra($con, $codob)
{
    $resultado = mysqli_query($con, "SELECT * FROM obra WHERE nombre_obra = '" . $codob[0] . "'");

    return $resultado; //Devuelvo un array con los datos de todos los usuarios
}

//Función para seleccionar empleados
function selecEmp($con, $codemp)
{
    $resultado = mysqli_query($con, "SELECT * FROM empleado WHERE nombre_emp = '" . $codemp[0] . "'");

    return $resultado; //Devuelvo un array con los datos de todos los usuarios
}

//Función para desconectar usuario
function cerrar_sesion()
{
    // Destruimos todas las variables de sesión
    session_destroy();
    echo '<meta http-equiv="refresh" content="0;URL=index.php">';
    exit();
}

