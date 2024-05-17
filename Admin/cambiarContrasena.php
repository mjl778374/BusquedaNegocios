<!DOCTYPE html>
<html>
<?php
include_once "constantesApp.php";
include_once "CSession.php";
$UsuarioSesionIngresoOKApp = CSession::UsuarioIngresoOK();
if (!$UsuarioSesionIngresoOKApp)
   header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación
// La validación anterior debe ser la primera que se realice, pues al verificarse si el usuario ingresó correctamente, 
// se calcula el tiempo transcurrido desde el último inicio de sesión, del cual se requiere saber si ya
// pasó cierto tiempo o no.

?>
<?php
include "encabezados.php";
$FormularioActivo = "CambiarContrasena"; // Este es un parámetro que recibe "menuApp.php"
include "menuApp.php";
?>
<?php
include_once "CUsuarios.php";

if (isset($_POST["ContrasenaActual"]))
{
    include_once "CMensajes.php";
    $MensajeXDesglosar = "";
    try
    {
        $Usuarios = new CUsuarios();
        $IdUsuario = CSession::DemeIdUsuario();
        $ContrasenaAnterior = $_POST["ContrasenaActual"];
        $NuevaContrasena = $_POST["NuevaContrasena"];
        $ConfirmacionNuevaContrasena = $_POST["ConfirmarNuevaContrasena"];
        $Usuarios->CambiarContrasena($IdUsuario, $ContrasenaAnterior, $NuevaContrasena, $ConfirmacionNuevaContrasena, $NumError, $LongitudMinimaContrasena, $CaracteresEspeciales);

        if ($NumError == 1001)
            throw new Exception("La contraseña actual es incorrecta.");
        elseif ($NumError == 2001)
            throw new Exception("La nueva contraseña no coincide con su confirmación.");
        elseif ($NumError == 3001)
            throw new Exception("La nueva contraseña debe tener al menos " . $LongitudMinimaContrasena . " caracteres.");
        elseif ($NumError == 3002)
            throw new Exception("La nueva contraseña debe estar conformada por al menos un caracter alfabético en mayúscula, un caracter alfabético en minúscula, un dígito decimal y un caracter especial entre " . $CaracteresEspeciales);
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . ".");

        $MensajeXDesglosar = CMensajes::MensajeOK("Se cambió la contraseña exitosamente.");
    } // try
    catch (Exception $e)
    {
        $MensajeXDesglosar = CMensajes::MensajeError($e->getMessage());
    } // catch (Exception $e)
} // if (isset($_POST["ContrasenaActual"]))
?>
<?php
$MaximoTamanoCampoContrasena = CUsuarios::MAXIMO_TAMANO_CAMPO_CONTRASENA;
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="ContrasenaActual">Contraseña Actual</label>
                <input type="password" class="form-control" id="ContrasenaActual" name="ContrasenaActual" placeholder="Ingrese su contraseña actual" maxlength="<?php echo $MaximoTamanoCampoContrasena;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="NuevaContrasena">Nueva Contraseña</label>
                <input type="password" class="form-control" id="NuevaContrasena" name="NuevaContrasena" placeholder="Ingrese su nueva contraseña" maxlength="<?php echo $MaximoTamanoCampoContrasena;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="ConfirmarNuevaContrasena">Confirmación de Nueva Contraseña</label>
                <input type="password" class="form-control" id="ConfirmarNuevaContrasena" name="ConfirmarNuevaContrasena" placeholder="Confirme su nueva contraseña" maxlength="<?php echo $MaximoTamanoCampoContrasena;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Enviar</button>
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
