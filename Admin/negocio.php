<!DOCTYPE html>
<html>
<?php
try
{
    include_once "constantesApp.php";
    include_once "CSession.php";
    $UsuarioSesionIngresoOKApp = CSession::UsuarioIngresoOK();
    if (!$UsuarioSesionIngresoOKApp)
    {
	echo "<script>window.top.location.href='" . $URL_PAGINA_INGRESO . "'</script>";
        exit;
        // header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación
    } // if (!$UsuarioSesionIngresoOKApp)
    // La validación anterior debe ser la primera que se realice, pues al verificarse si el usuario ingresó correctamente, 
    // se calcula el tiempo transcurrido desde el último inicio de sesión, del cual se requiere saber si ya
    // pasó cierto tiempo o no.

    include_once "CMensajes.php";
    $MensajeXDesglosar = "";

    include "encabezados.php";
    $FormularioActivo = "Negocio"; // Este es un parámetro que recibe "menuApp.php"
    include "menuApp.php";

    $Nombre = "";
    $Direccion = "";
    $Telefonos = "";
    $IdCanton = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;

    include_once "CCantones.php";
    $Cantones = new CCantones();
    $ListadoCantones = $Cantones->DemeTodosCantones();
   
    if (isset($_POST["NombreNegocio"]))
    {
        $Nombre = $_POST["NombreNegocio"];
        $Direccion = $_POST["Direccion"];;
        $Telefonos = $_POST["Telefonos"];;
        $IdCanton = $_POST["IdCanton"];
    } // if (isset($_POST["NombreNegocio"]))

    include_once "CMensajes.php";
    include_once "CParametrosGet.php";
    include_once "CNegocios.php";

    $Modo = CParametrosGet::ValidarModo("Modo", $NumError);

    if ($NumError == 1)
        throw new Exception("Debe incorporar el parámetro 'Modo'.");
    elseif ($NumError == 2)
        throw new Exception("'Modo' inválido.");
    elseif ($NumError != 0)
        throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'Modo'.");

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $IdNegocio = CParametrosGet::ValidarIdEntero("IdNegocio", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdNegocio'.");
        elseif ($NumError == 2)
            throw new Exception("'IdNegocio' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdNegocio'.");

        if (strcmp($_GET["IdNegocio"], $IdNegocio) != 0)
            header("Location: " . "negocio.php?Modo=" . $Modo . "&IdNegocio=" . $IdNegocio);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)

    $ErrorNoExisteNegocioConIdEspecificado = "No existe el negocio con el id " . $IdNegocio . ".";

    if (isset($_POST["NombreNegocio"]))
    {
        $Negocios = new CNegocios();

        include_once "CPalabras.php";

        $ErrorNombreInvalido = "El nombre debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
        $ErrorDireccionInvalida = "La dirección debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
        $ErrorTelefonosInvalidos = "Los teléfonos deben tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
        $ErrorDebeSeleccionarCanton = "Debe seleccionar un cantón";
        $ErrorNoExisteCanton = "El cantón seleccionado no existe";

        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            $Negocios->AltaNegocio($Nombre, $Direccion, $Telefonos, $IdCanton, $NumError, $IdNegocio);

            if ($NumError == 1001)
                throw new Exception($ErrorNombreInvalido);
            elseif ($NumError == 1002)
                throw new Exception($ErrorDireccionInvalida);
            elseif ($NumError == 1003)
                throw new Exception($ErrorTelefonosInvalidos);
            elseif ($NumError == 2001)
            {
                if ($IdCanton == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    throw new Exception($ErrorDebeSeleccionarCanton);
                else
                    throw new Exception($ErrorNoExisteCanton);
            } // elseif ($NumError == 2001)
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'AltaNegocio'.");

            header("Location: mainNegocio.php?Modo=" . $MODO_CAMBIO . "&IdNegocio=" . $IdNegocio); // Se carga el cantón guardado.
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            $Negocios->CambioNegocio($IdNegocio, $Nombre, $Direccion, $Telefonos, $IdCanton, $NumError);

            if ($NumError == 1001)
                throw new Exception($ErrorNoExisteNegocioConIdEspecificado);
            else if ($NumError == 2001)
                throw new Exception($ErrorNombreInvalido);
            elseif ($NumError == 2002)
                throw new Exception($ErrorDireccionInvalida);
            elseif ($NumError == 2003)
                throw new Exception($ErrorTelefonosInvalidos);
            elseif ($NumError == 3001)
            {
                if ($IdCanton == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    throw new Exception($ErrorDebeSeleccionarCanton);
                else
                    throw new Exception($ErrorNoExisteCanton);
            } // elseif ($NumError == 3001)
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'CambioNegocio'.");
        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)

        $MensajeXDesglosar = CMensajes::MensajeOK("Se guardó el negocio exitosamente.");
    } // if (isset($_POST["NombreNegocio"]))

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $Negocios = new CNegocios();
        $Negocios->ConsultarXNegocio($IdNegocio, $Existe, $Nombre, $Direccion, $Telefonos, $IdCanton);

        if (!$Existe)
            throw new Exception($ErrorNoExisteNegocioConIdEspecificado);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = CMensajes::MensajeError($e->getMessage());
} // catch (Exception $e)

$MaximoTamanoCampoNombre = CNegocios::MAXIMO_TAMANO_CAMPO_NOMBRE;
$MaximoTamanoCampoDireccion = CNegocios::MAXIMO_TAMANO_CAMPO_DIRECCION;
$MaximoTamanoCampoTelefonos = CNegocios::MAXIMO_TAMANO_CAMPO_TELEFONOS;
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="NombreNegocio">Nombre</label>
                <input type="text" class="form-control" id="NombreNegocio" name="NombreNegocio" placeholder="Ingrese el nombre del negocio" value="<?php echo htmlspecialchars($Nombre); ?>" maxlength="<?php echo $MaximoTamanoCampoNombre;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Direccion">Dirección</label>
                <textarea class="form-control" id="Direccion" name="Direccion" rows="4" placeholder="Ingrese la dirección" maxlength="<?php echo $MaximoTamanoCampoDireccion;?>"><?php echo htmlspecialchars($Direccion); ?></textarea>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Telefonos">Teléfonos</label>
                <input type="text" class="form-control" id="Telefonos" name="Telefonos" placeholder="Ingrese los teléfonos" value="<?php echo htmlspecialchars($Telefonos); ?>" maxlength="<?php echo $MaximoTamanoCampoTelefonos;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="IdCanton">Cantón</label>
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoCantones;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Seleccione un Cantón...");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdCanton;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion="IdCanton"; $NameListaSeleccion="IdCanton"; include "componenteListaSeleccion.php" ?>
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.top.location.href='negocios.php';">Regresar</button>
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
