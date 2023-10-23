<?php
session_start(); //Inicio la sesión
require("database.php"); //Traigo la base de datos
$con = conectar(); //Conecto

if ($_SESSION['tipo_usuario'] != '1') { //Comprobación de usuario
    echo '<meta http-equiv="refresh" content="0;URL=index.php">';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cerrar_sesion'])) {
    cerrar_sesion();//Cierra sesión si se presiona el botón DESCONECTAR
}
?>

<html>

<head>
    <title>Panel de administrador</title>
    <link rel="icon" type="image/jpg" href="IMG/logo.png" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body,
        html {
            margin: 25px;
            padding: 30px;
        }

    </style>



</head>

<body style="text-align: center; height: 100%; margin: 0; display: flex; flex-direction: column; justify-content: center;">


    <h4>PANEL ADMINISTRADOR</h4><br />


    <!-- HORAS EXTRA Y PARTE HORAS -->
    <div class="row justify-content-center">
        <div class="col-md-4" style="margin-bottom: 1rem;">
            <div class="card">
                <div class="card-body">
                    <form action="precioextras.php" method="post">
                        <h4 class="card-title">HORAS EXTRA MENSUALES</h4>
                        <button type="submit" class="btn btn-danger btn-block">HORAS EXTRA</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4" style="margin-bottom: 1rem;">
            <div class="card">
                <div class="card-body">
                    <form action="registromensualadmin.php" method="post">
                        <h4 class="card-title">PARTE HORAS</h4>
                        <button type="submit" class="btn btn-danger btn-block">PARTE</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- USUARIOS Y EMPLEADOS -->
    <div class="row justify-content-center">
        <div class="col-md-4" style="margin-bottom: 1rem;">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">USUARIOS</h4>

                    <div class="row">
                        <div class="col">
                            <form action="altausu.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">CREAR</button>
                            </form>
                        </div>
                        <div class="col">
                            <form action="bajausu.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">BORRAR</button>
                            </form>
                        </div>
                        <div class="col">
                            <form action="modusu.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">MODIFICAR</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4" style="margin-bottom: 1rem;">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">EMPLEADOS</h4>

                    <div class="row">
                        <div class="col">
                            <form action="altaemp.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">CREAR</button>
                            </form>
                        </div>
                        <div class="col">
                            <form action="bajaemp.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">BORRAR</button>
                            </form>
                        </div>
                        <div class="col">
                            <form action="modemp.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">MODIFICAR</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ENCARGADOS Y JEFES DE OBRA -->
    <div class="row justify-content-center">
        <div class="col-md-4" style="margin-bottom: 1rem;">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">ENCARGADOS</h4>

                    <div class="row">
                        <div class="col">
                            <form action="altaenc.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">CREAR</button>
                            </form>
                        </div>
                        <div class="col">
                            <form action="bajaenc.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">BORRAR</button>
                            </form>
                        </div>
                        <div class="col">
                            <form action="modenc.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">MODIFICAR</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4" style="margin-bottom: 1rem;">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">JEFES DE OBRA</h4>

                    <div class="row">
                        <div class="col">
                            <form action="altajo.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">CREAR</button>
                            </form>
                        </div>
                        <div class="col">
                            <form action="bajajo.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">BORRAR</button>
                            </form>
                        </div>
                        <div class="col">
                            <form action="modjo.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">MODIFICAR</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- OBRAS Y PROVINCIAS -->
    <div class="row justify-content-center">
        <div class="col-md-4" style="margin-bottom: 1rem;">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">OBRAS</h4>

                    <div class="row">
                        <div class="col">
                            <form action="altaobra.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">CREAR</button>
                            </form>
                        </div>
                        <div class="col">
                            <form action="bajaobra.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">BORRAR</button>
                            </form>
                        </div>
                        <div class="col">
                            <form action="modobra.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">MODIFICAR</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4" style="margin-bottom: 1rem;">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">PROVINCIAS</h4>

                    <div class="row">
                        <div class="col">
                            <form action="altaprovincia.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">CREAR</button>
                            </form>
                        </div>
                        <div class="col">
                            <form action="bajaprovincia.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">BORRAR</button>
                            </form>
                        </div>
                        <div class="col">
                            <form action="modprovincia.php" method="post">
                                <button type="submit" class="btn btn-danger btn-block">MODIFICAR</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="post">
        <input type="hidden" name="cerrar_sesion" value="1">
        <p><input type="submit" class="btn btn-secondary" name="cerrar_sesion" value="DESCONECTAR" /></p>
    </form>


</body>

</html>