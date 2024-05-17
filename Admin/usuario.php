<!DOCTYPE html>
<html>
<?php
try
{
    include_once "constantesApp.php";
    include_once "CSession.php";
    $UsuarioSesionIngresoOKApp = CSession::UsuarioIngresoOK();
    $UsuarioSesionEsAdmin = CSession::UsuarioSesionEsAdministrador();
    if (!$UsuarioSesionIngresoOKApp || !$UsuarioSesionEsAdmin)
        header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación
    // La validación anterior debe ser la primera que se realice, pues al verificarse si el usuario ingresó correctamente, 
    // se calcula el tiempo transcurrido desde el último inicio de sesión, del cual se requiere saber si ya
    // pasó cierto tiempo o no.

    include_once "CMensajes.php";
    $MensajeXDesglosar = "";

    include "encabezados.php";
    $FormularioActivo = "Usuario"; // Este es un parámetro que recibe "menuApp.php"
    include "menuApp.php";

    $Usuario = "";
    $Cedula = "";
    $Nombre = "";
    $BitEsAdministrador = 0;
    $BitBorrarContrasena = 0;

    if (isset($_POST["Usuario"]))
    {
        $Usuario = $_POST["Usuario"];
        $Cedula = $_POST["Cedula"];
        $Nombre = $_POST["Nombre"];

        if ($_POST["EsAdministrador"] == "on")
            $BitEsAdministrador = 1;

        if ($_POST["BorrarContrasena"] == "on")
            $BitBorrarContrasena = 1;
    } // if (isset($_POST["Usuario"]))

    include_once "CMensajes.php";
    include_once "CParametrosGet.php";
    include_once "CUsuarios.php";

    $Modo = CParametrosGet::ValidarModo("Modo", $NumError);

    if ($NumError == 1)
        throw new Exception("Debe incorporar el parámetro 'Modo'.");
    elseif ($NumError == 2)
        throw new Exception("'Modo' inválido.");
    elseif ($NumError != 0)
        throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'Modo'.");

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $IdUsuario = CParametrosGet::ValidarIdEntero("IdUsuario", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdUsuario'.");
        elseif ($NumError == 2)
            throw new Exception("'IdUsuario' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdUsuario'.");

        if (strcmp($_GET["IdUsuario"], $IdUsuario) != 0)
            header("Location: " . "usuario.php?Modo=" . $Modo . "&IdUsuario=" . $IdUsuario);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)

    $ErrorNoExisteUsuarioConIdEspecificado = "No existe el usuario con el id " . $IdUsuario . ".";

    if (isset($_POST["Usuario"]))
    {
        $Usuarios = new CUsuarios();

        include_once "CPalabras.php";

        $ErrorUsuarioInvalido = "El usuario debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
        $ErrorCedulaInvalida = "La cédula debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
        $ErrorNombreInvalido = "El nombre debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();

        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            $Usuarios->AltaUsuario($Usuario, $Cedula, $Nombre, $BitEsAdministrador, $NumError, $IdUsuario);

            if ($NumError == 1001)
                throw new Exception("Ya existe el usuario " . $Usuario . ". No se puede insertar nuevamente.");
            elseif ($NumError == 2001)
                throw new Exception($ErrorUsuarioInvalido);
            elseif ($NumError == 2002)
                throw new Exception($ErrorCedulaInvalida);
            elseif ($NumError == 2003)
                throw new Exception($ErrorNombreInvalido);
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'AltaUsuario'.");

            header("Location: usuario.php?Modo=" . $MODO_CAMBIO . "&IdUsuario=" . $IdUsuario); // Se carga el usuario guardado.
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            $Usuarios->CambioUsuario($IdUsuario, $Usuario, $Cedula, $Nombre, $BitEsAdministrador, $BitBorrarContrasena, $NumError);
            $BitBorrarContrasena = 0;

            if ($NumError == 1001)
                throw new Exception("Ya existe el usuario " . $Usuario . " con otro id.");
            elseif ($NumError == 2001)
                throw new Exception($ErrorNoExisteUsuarioConIdEspecificado);
            elseif ($NumError == 3001)
                throw new Exception($ErrorUsuarioInvalido);
            elseif ($NumError == 3002)
                throw new Exception($ErrorCedulaInvalida);
            elseif ($NumError == 3003)
                throw new Exception($ErrorNombreInvalido);
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'CambioUsuario'.");

        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)

        $MensajeXDesglosar = CMensajes::MensajeOK("Se guardó el usuario exitosamente.");
    } // if (isset($_POST["Usuario"]))

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $Usuarios = new CUsuarios();
        $Usuarios->ConsultarXUsuario($IdUsuario, $Existe, $Usuario, $Cedula, $Nombre, $BitEsAdministrador);

        if (!$Existe)
            throw new Exception($ErrorNoExisteUsuarioConIdEspecificado);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = CMensajes::MensajeError($e->getMessage());
} // catch (Exception $e)

$HabilitarBorradoContrasena = "";
$BorradoContrasenaSeleccionado = "";

if (strcmp($Modo, $MODO_ALTA) == 0)
{
    $HabilitarBorradoContrasena = "disabled";
    $BitBorrarContrasena = 1;
} // if (strcmp($Modo, $MODO_ALTA) == 0)

$EsAdministradorSeleccionado = "";

if ($BitEsAdministrador)
    $EsAdministradorSeleccionado = "checked";

if ($BitBorrarContrasena)
    $BorradoContrasenaSeleccionado = "checked";

$MaximoTamanoCampoUsuario = CUsuarios::MAXIMO_TAMANO_CAMPO_USUARIO;
$MaximoTamanoCampoCedula = CUsuarios::MAXIMO_TAMANO_CAMPO_CEDULA;
$MaximoTamanoCampoNombre = CUsuarios::MAXIMO_TAMANO_CAMPO_NOMBRE;
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Usuario">Usuario</label>
                <input type="text" class="form-control" id="Usuario" name="Usuario" placeholder="Ingrese el usuario" value="<?php echo htmlspecialchars($Usuario); ?>" maxlength="<?php echo $MaximoTamanoCampoUsuario;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Cedula">Cédula</label>
                <input type="text" class="form-control" id="Cedula" name="Cedula" placeholder="Ingrese la cédula del usuario" value="<?php echo htmlspecialchars($Cedula); ?>" maxlength="<?php echo $MaximoTamanoCampoCedula;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Nombre">Nombre</label>
                <input type="text" class="form-control" id="Nombre" name="Nombre" placeholder="Ingrese el nombre del usuario" value="<?php echo htmlspecialchars($Nombre); ?>" maxlength="<?php echo $MaximoTamanoCampoNombre;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="custom-control custom-checkbox col-8 col-md-6 col-lg-4">
                <input type="checkbox" class="custom-control-input" id="EsAdministrador" name="EsAdministrador" <?php echo $EsAdministradorSeleccionado; ?>>
                <label class="custom-control-label" for="EsAdministrador">Es Administrador</label>
           </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="custom-control custom-checkbox col-8 col-md-6 col-lg-4">
                <input type="checkbox" class="custom-control-input" id="BorrarContrasena" name="BorrarContrasena" <?php echo $HabilitarBorradoContrasena; ?> <?php echo $BorradoContrasenaSeleccionado; ?>>
                <label class="custom-control-label" for="BorrarContrasena">Borrar Contraseña</label>
           </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='usuarios.php';">Regresar</button>
            </div>
        </div>
    </div>
<?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
</form>
</body>
</html>
