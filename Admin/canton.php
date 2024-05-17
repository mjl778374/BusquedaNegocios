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
    $FormularioActivo = "Canton"; // Este es un parámetro que recibe "menuApp.php"
    include "menuApp.php";

    $Canton = "";
    $IdRegionGeografica = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;
    $IdProvincia = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;

    include_once "CProvincias.php";
    $Provincias = new CProvincias();
    $ListadoProvincias = $Provincias->DemeTodasProvincias();

    include_once "CRegionesGeograficas.php";
    $RegionesGeograficas = new CRegionesGeograficas();
    $ListadoRegionesGeograficas = $RegionesGeograficas->DemeTodasRegionesGeograficas();
    
    if (isset($_POST["Canton"]))
    {
        $Canton = $_POST["Canton"];
        $IdRegionGeografica = $_POST["IdRegionGeografica"];
        $IdProvincia = $_POST["IdProvincia"];
    } // if (isset($_POST["Canton"]))

    include_once "CMensajes.php";
    include_once "CParametrosGet.php";
    include_once "CCantones.php";

    $Modo = CParametrosGet::ValidarModo("Modo", $NumError);

    if ($NumError == 1)
        throw new Exception("Debe incorporar el parámetro 'Modo'.");
    elseif ($NumError == 2)
        throw new Exception("'Modo' inválido.");
    elseif ($NumError != 0)
        throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'Modo'.");

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $IdCanton = CParametrosGet::ValidarIdEntero("IdCanton", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdCanton'.");
        elseif ($NumError == 2)
            throw new Exception("'IdCanton' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdCanton'.");

        if (strcmp($_GET["IdCanton"], $IdCanton) != 0)
            header("Location: " . "canton.php?Modo=" . $Modo . "&IdCanton=" . $IdCanton);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)

    $ErrorNoExisteCantonConIdEspecificado = "No existe el cantón con el id " . $IdCanton . ".";

    if (isset($_POST["Canton"]))
    {
        $Cantones = new CCantones();

        include_once "CPalabras.php";

        $ErrorCantonInvalido = "El cantón debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
        $ErrorDebeSeleccionarRegionGeografica = "Debe seleccionar una región geográfica";
        $ErrorDebeSeleccionarProvincia = "Debe seleccionar una provincia";
        $ErrorNoExisteRegionGeografica = "La región geográfica seleccionada no existe";
        $ErrorNoExisteProvincia = "La provincia seleccionada no existe";

        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            $Cantones->AltaCanton($Canton, $IdRegionGeografica, $IdProvincia, $NumError, $IdCanton);

            if ($NumError == 1001)
                throw new Exception("Ya existe el cantón " . $Canton . ". No se puede insertar nuevamente.");
            elseif ($NumError == 2001)
                throw new Exception($ErrorCantonInvalido);
            elseif ($NumError == 2002)
            {
                if ($IdRegionGeografica == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    throw new Exception($ErrorDebeSeleccionarRegionGeografica);
                else
                    throw new Exception($ErrorNoExisteRegionGeografica);
            } // elseif ($NumError == 2002)
            elseif ($NumError == 2003)
            {
                if ($IdProvincia == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    throw new Exception($ErrorDebeSeleccionarProvincia);
                else
                    throw new Exception($ErrorNoExisteProvincia);
            } // elseif ($NumError == 2003)
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'AltaCanton'.");

            header("Location: canton.php?Modo=" . $MODO_CAMBIO . "&IdCanton=" . $IdCanton); // Se carga el cantón guardado.
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            $Cantones->CambioCanton($IdCanton, $Canton, $IdRegionGeografica, $IdProvincia, $NumError);

            if ($NumError == 1001)
                throw new Exception("Ya existe el cantón " . $Canton . " con otro id.");
            elseif ($NumError == 2001)
                throw new Exception($ErrorNoExisteCantonConIdEspecificado);
            elseif ($NumError == 3001)
                throw new Exception($ErrorCantonInvalido);
            elseif ($NumError == 3002)
            {
                if ($IdRegionGeografica == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    throw new Exception($ErrorDebeSeleccionarRegionGeografica);
                else
                    throw new Exception($ErrorNoExisteRegionGeografica);
            } // elseif ($NumError == 3002)
            elseif ($NumError == 3003)
            {
                if ($IdProvincia == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    throw new Exception($ErrorDebeSeleccionarProvincia);
                else
                    throw new Exception($ErrorNoExisteProvincia);
            } // elseif ($NumError == 3003)
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'CambioCanton'.");

        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)

        $MensajeXDesglosar = CMensajes::MensajeOK("Se guardó el cantón exitosamente.");
    } // if (isset($_POST["Canton"]))

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $Cantones = new CCantones();
        $Cantones->ConsultarXCanton($IdCanton, $Existe, $Canton, $IdRegionGeografica, $IdProvincia);

        if (!$Existe)
            throw new Exception($ErrorNoExisteCantonConIdEspecificado);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = CMensajes::MensajeError($e->getMessage());
} // catch (Exception $e)

$MaximoTamanoCampoCanton = CCantones::MAXIMO_TAMANO_CAMPO_CANTON;
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Canton">Cantón</label>
                <input type="text" class="form-control" id="Canton" name="Canton" placeholder="Ingrese el cantón" value="<?php echo htmlspecialchars($Canton); ?>" maxlength="<?php echo $MaximoTamanoCampoCanton;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="IdRegionGeografica">Región Geográfica</label>
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoRegionesGeograficas;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Seleccione una Región Geográfica...");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdRegionGeografica;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion="IdRegionGeografica"; $NameListaSeleccion="IdRegionGeografica"; include "componenteListaSeleccion.php" ?>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="IdProvincia">Provincia</label>
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoProvincias;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Seleccione una Provincia...");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdProvincia;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion="IdProvincia"; $NameListaSeleccion="IdProvincia"; include "componenteListaSeleccion.php" ?>
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='cantones.php';">Regresar</button>
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
