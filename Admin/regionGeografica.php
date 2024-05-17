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
    $FormularioActivo = "RegionGeografica"; // Este es un parámetro que recibe "menuApp.php"
    include "menuApp.php";

    $Region = "";
    
    if (isset($_POST["Region"]))
        $Region = $_POST["Region"];

    include_once "CMensajes.php";
    include_once "CParametrosGet.php";
    include_once "CRegionesGeograficas.php";

    $Modo = CParametrosGet::ValidarModo("Modo", $NumError);

    if ($NumError == 1)
        throw new Exception("Debe incorporar el parámetro 'Modo'.");
    elseif ($NumError == 2)
        throw new Exception("'Modo' inválido.");
    elseif ($NumError != 0)
        throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'Modo'.");

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $IdRegion = CParametrosGet::ValidarIdEntero("IdRegion", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdRegion'.");
        elseif ($NumError == 2)
            throw new Exception("'IdRegion' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdRegion'.");

        if (strcmp($_GET["IdRegion"], $IdRegion) != 0)
            header("Location: " . "regionGeografica.php?Modo=" . $Modo . "&IdRegion=" . $IdRegion);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)

    $ErrorNoExisteRegionConIdEspecificado = "No existe la región con el id " . $IdRegion . ".";

    if (isset($_POST["Region"]))
    {
        $Regiones = new CRegionesGeograficas();

        include_once "CPalabras.php";

        $ErrorRegionInvalida = "La región debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();

        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            $Regiones->AltaRegionGeografica($Region, $NumError, $IdRegion);

            if ($NumError == 1001)
                throw new Exception("Ya existe la región " . $Region . ". No se puede insertar nuevamente.");
            elseif ($NumError == 2001)
                throw new Exception($ErrorRegionInvalida);
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'AltaRegion'.");

            header("Location: regionGeografica.php?Modo=" . $MODO_CAMBIO . "&IdRegion=" . $IdRegion); // Se carga la región guardada.
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            $Regiones->CambioRegionGeografica($IdRegion, $Region, $NumError);

            if ($NumError == 1001)
                throw new Exception("Ya existe la región " . $Region . " con otro id.");
            elseif ($NumError == 2001)
                throw new Exception($ErrorNoExisteRegionConIdEspecificado);
            elseif ($NumError == 3001)
                throw new Exception($ErrorRegionInvalida);
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'CambioRegionGeografica'.");

        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)

        $MensajeXDesglosar = CMensajes::MensajeOK("Se guardó la región exitosamente.");
    } // if (isset($_POST["Region"]))

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $Regiones = new CRegionesGeograficas();
        $Regiones->ConsultarXRegionGeografica($IdRegion, $Existe, $Region);

        if (!$Existe)
            throw new Exception($ErrorNoExisteRegionConIdEspecificado);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = CMensajes::MensajeError($e->getMessage());
} // catch (Exception $e)

$MaximoTamanoCampoRegion = CRegionesGeograficas::MAXIMO_TAMANO_CAMPO_REGION_GEOGRAFICA;
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Region">Región</label>
                <input type="text" class="form-control" id="Region" name="Region" placeholder="Ingrese la región" value="<?php echo htmlspecialchars($Region); ?>" maxlength="<?php echo $MaximoTamanoCampoRegion;?>">
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='regionesGeograficas.php';">Regresar</button>
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
