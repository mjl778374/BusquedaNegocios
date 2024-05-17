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

    include_once "CMensajes.php";
    include_once "CParametrosGet.php";
    include_once "CAliasYCategorias.php";

    $IdCategoria = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;

    $Modo = CParametrosGet::ValidarModo("Modo", $NumError);

    if ($NumError == 1)
        throw new Exception("Debe incorporar el parámetro 'Modo'.");
    elseif ($NumError == 2)
        throw new Exception("'Modo' inválido.");
    elseif ($NumError != 0)
        throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'Modo'.");

    $IdAlias = CParametrosGet::ValidarIdEntero("IdAlias", $NumError);
    if ($NumError == 1)
        throw new Exception("Debe incorporar el parámetro 'IdAlias'.");
    elseif ($NumError == 2)
        throw new Exception("'IdAlias' debe ser un número entero mayor o igual que 0.");
    elseif ($NumError != 0)
        throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdAlias'.");

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $IdCategoria = CParametrosGet::ValidarIdEntero("IdCategoria", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdCategoria'.");
        elseif ($NumError == 2)
            throw new Exception("'IdCategoria' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdCategoria'.");

        if (strcmp($_GET["IdCategoria"], $IdCategoria) != 0 || strcmp($_GET["IdAlias"], $IdAlias) != 0)
            header("Location: unaCategoriaDeAlias.php?Modo=" . $MODO_CAMBIO . "&IdCategoria=" . $IdCategoria . "&IdAlias=" . $IdAlias);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)

    $ErrorNoExisteAliasConIdEspecificadoEnIdCategoria = "No existe el alias con el id " . $IdAlias . " en la categoría con el id " . $IdCategoria;

    if (isset($_POST["IdNuevaCategoria"]))
    {
        $IdNuevaCategoria = $_POST["IdNuevaCategoria"];
        $ClaseAlias = new CAliasYCategorias();

        include_once "CPalabras.php";

        $ErrorDebeSeleccionarCategoria = "Debe seleccionar una categoría";
        $ErrorNoExisteAliasConIdEspecificado = "No existe el alias con el id " . $IdAlias . ".";
        $ErrorAliasInvalido = "El alias debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();

        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            $IdCategoria = $IdNuevaCategoria;
            $ErrorNoExisteCategoriaConIdEspecificado = "No existe la categoría con el id " . $IdNuevaCategoria . ".";
            $ClaseAlias->AltaAlias2($IdAlias, $IdNuevaCategoria, $NumError, $Alias);

            if ($NumError == 1001)
            {
                if ($IdNuevaCategoria == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    throw new Exception($ErrorDebeSeleccionarCategoria);
                else
                    throw new Exception($ErrorNoExisteCategoriaConIdEspecificado);
            } // if ($NumError == 1001)

            else if ($NumError == 2001)
                throw new Exception("El alias " . $Alias . " es el nombre de esta categoría. No se puede insertar.");
            else if ($NumError == 3001)
                throw new Exception("El alias " . $Alias . " es el nombre de otra categoría. No se puede insertar.");
            elseif ($NumError == 4001)
                throw new Exception($ErrorAliasInvalido);

            elseif ($NumError == 11)
                throw new Exception($ErrorNoExisteAliasConIdEspecificado);

            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'AltaAlias2'.");

            header("Location: unaCategoriaDeAlias.php?Modo=" . $MODO_CAMBIO . "&IdCategoria=" . $IdNuevaCategoria . "&IdAlias=" . $IdAlias); // Se carga el alias guardado.
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            $ErrorNoExisteCategoriaConIdEspecificado = "No existe la categoría con el id " . $IdCategoria . ".";
            $ClaseAlias->CambioAlias2($IdAlias, $IdCategoria, $IdNuevaCategoria, $NumError, $Alias);

            if ($NumError != 0)
                $IdCategoria = $IdNuevaCategoria; // Esto es para que en el combo de selección de categorías aparezca, como seleccionada, la categoría que seleccionó el usuario

            if ($NumError == 1001)
                throw new Exception("El alias " . $Alias . " es el nombre de una categoría. No se puede cambiar.");
            else if ($NumError == 2001)
                throw new Exception($ErrorNoExisteCategoriaConIdEspecificado);

            else if ($NumError == 3001)
            {
                if ($IdNuevaCategoria == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    throw new Exception($ErrorDebeSeleccionarCategoria);
                else
                    throw new Exception("No existe la categoría con el id " . $IdNuevaCategoria . ".");
            } // else if ($NumError == 3001)

            else if ($NumError == 4001)
                throw new Exception($ErrorNoExisteAliasConIdEspecificadoEnIdCategoria);
            else if ($NumError == 5001)
                throw new Exception("El alias " . $Alias . " es el nombre de esta categoría. No se puede cambiar.");
            else if ($NumError == 6001)
                throw new Exception("El alias " . $Alias . " es el nombre de otra categoría. No se puede cambiar.");
            else if ($NumError == 7001)
                throw new Exception($ErrorAliasInvalido);

            elseif ($NumError == 11)
                throw new Exception($ErrorNoExisteAliasConIdEspecificado);

            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'CambioAlias2'.");

            if ($IdCategoria != $IdNuevaCategoria)
                header("Location: unaCategoriaDeAlias.php?Modo=" . $MODO_CAMBIO . "&IdCategoria=" . $IdNuevaCategoria . "&IdAlias=" . $IdAlias);
        } // if (isset($_POST["IdNuevaCategoria"]))

        $IdCategoria = $IdNuevaCategoria;
        $MensajeXDesglosar = CMensajes::MensajeOK("Se guardó la categoría exitosamente.");
    } // if (isset($_POST["Alias"]))

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $ClaseAlias = new CAliasYCategorias();
        $ClaseAlias->ConsultarXAlias($IdAlias, $IdCategoria, $Existe, $Alias);

        if (!$Existe)
            throw new Exception($ErrorNoExisteAliasConIdEspecificadoEnIdCategoria);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = CMensajes::MensajeError($e->getMessage());
} // catch (Exception $e)

$AliasYCategorias = new CAliasYCategorias();
$ListadoCategorias = $AliasYCategorias->DemeTodasCategorias2();
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="IdNuevaCategoria">Categoría</label>
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoCategorias;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Seleccione una Categoría...");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdCategoria;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion="IdNuevaCategoria"; $NameListaSeleccion="IdNuevaCategoria"; include "componenteListaSeleccion.php" ?>
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='todasCategoriasDeAlias.php?IdAlias=<?php echo $IdAlias?>';">Regresar</button>
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
