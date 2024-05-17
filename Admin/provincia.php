<!DOCTYPE html>
<html>
<?php
try
{
    include_once "constantesApp.php";
    include_once "CSession.php";
    $UsuarioSesionIngresoOKApp = CSession::UsuarioIngresoOK();
    if (!$UsuarioSesionIngresoOKApp)
        header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación
    // La validación anterior debe ser la primera que se realice, pues al verificarse si el usuario ingresó correctamente, 
    // se calcula el tiempo transcurrido desde el último inicio de sesión, del cual se requiere saber si ya
    // pasó cierto tiempo o no.

    include_once "CMensajes.php";
    $MensajeXDesglosar = "";

    include "encabezados.php";
    $FormularioActivo = "Provincia"; // Este es un parámetro que recibe "menuApp.php"
    include "menuApp.php";

    $Provincia = "";
    
    if (isset($_POST["Provincia"]))
        $Provincia = $_POST["Provincia"];

    include_once "CMensajes.php";
    include_once "CParametrosGet.php";
    include_once "CProvincias.php";

    $Modo = CParametrosGet::ValidarModo("Modo", $NumError);

    if ($NumError == 1)
        throw new Exception("Debe incorporar el parámetro 'Modo'.");
    elseif ($NumError == 2)
        throw new Exception("'Modo' inválido.");
    elseif ($NumError != 0)
        throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'Modo'.");

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $IdProvincia = CParametrosGet::ValidarIdEntero("IdProvincia", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdProvincia'.");
        elseif ($NumError == 2)
            throw new Exception("'IdProvincia' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdProvincia'.");

        if (strcmp($_GET["IdProvincia"], $IdProvincia) != 0)
            header("Location: " . "provincia.php?Modo=" . $Modo . "&IdProvincia=" . $IdProvincia);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)

    $ErrorNoExisteProvinciaConIdEspecificado = "No existe la provincia con el id " . $IdProvincia . ".";

    if (isset($_POST["Provincia"]))
    {
        $Provincias = new CProvincias();

        include_once "CPalabras.php";

        $ErrorProvinciaInvalida = "La provincia debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();

        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            $Provincias->AltaProvincia($Provincia, $NumError, $IdProvincia);

            if ($NumError == 1001)
                throw new Exception("Ya existe la provincia " . $Provincia . ". No se puede insertar nuevamente.");
            elseif ($NumError == 2001)
                throw new Exception($ErrorProvinciaInvalida);
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'AltaProvincia'.");

            header("Location: provincia.php?Modo=" . $MODO_CAMBIO . "&IdProvincia=" . $IdProvincia); // Se carga la provincia guardada.
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            $Provincias->CambioProvincia($IdProvincia, $Provincia, $NumError);

            if ($NumError == 1001)
                throw new Exception("Ya existe la provincia " . $Provincia . " con otro id.");
            elseif ($NumError == 2001)
                throw new Exception($ErrorNoExisteProvinciaConIdEspecificado);
            elseif ($NumError == 3001)
                throw new Exception($ErrorProvinciaInvalida);
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'CambioProvincia'.");

        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)

        $MensajeXDesglosar = CMensajes::MensajeOK("Se guardó la provincia exitosamente.");
    } // if (isset($_POST["Provincia"]))

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $Provincias = new CProvincias();
        $Provincias->ConsultarXProvincia($IdProvincia, $Existe, $Provincia);

        if (!$Existe)
            throw new Exception($ErrorNoExisteProvinciaConIdEspecificado);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = CMensajes::MensajeError($e->getMessage());
} // catch (Exception $e)

$MaximoTamanoCampoProvincia = CProvincias::MAXIMO_TAMANO_CAMPO_PROVINCIA;
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Provincia">Provincia</label>
                <input type="text" class="form-control" id="Provincia" name="Provincia" placeholder="Ingrese la provincia" value="<?php echo htmlspecialchars($Provincia); ?>" maxlength="<?php echo $MaximoTamanoCampoProvincia;?>">
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='provincias.php';">Regresar</button>
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
