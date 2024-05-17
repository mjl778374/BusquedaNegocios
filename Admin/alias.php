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
    $FormularioActivo = "Alias"; // Este es un parámetro que recibe "menuApp.php"
    include "menuApp.php";

    $Alias = "";
    
    if (isset($_POST["Alias"]))
        $Alias = $_POST["Alias"];

    include_once "CMensajes.php";
    include_once "CParametrosGet.php";
    include_once "CAliasYCategorias.php";

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

    if (strcmp($_GET["IdAlias"], $IdAlias) != 0)
        header("Location: alias.php?Modo=" . $MODO_CAMBIO . "&IdAlias=" . $IdAlias);

/*
    if (isset($_POST["Alias"]))
    {
        $ClaseAlias = new CAliasYCategorias();

        include_once "CPalabras.php";

        $ErrorNoExisteCategoriaConIdEspecificado = "No existe la categoría con el id " . $IdCategoria . ".";
        $ErrorNoExisteAliasConIdEspecificado = "No existe el alias con el id " . $IdAlias . ".";
        $ErrorAliasInvalido = "El alias debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();

        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            $ClaseAlias->AltaAlias($Alias, $IdCategoria, $NumError, $IdAlias);

            if ($NumError == 1001)
                throw new Exception($ErrorNoExisteCategoriaConIdEspecificado);
            else if ($NumError == 2001)
                throw new Exception("El alias " . $Alias . " es el nombre de esta categoría. No se puede insertar.");
            else if ($NumError == 3001)
                throw new Exception("El alias " . $Alias . " es el nombre de otra categoría. No se puede insertar.");
            elseif ($NumError == 4001)
                throw new Exception($ErrorAliasInvalido);
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'AltaAlias'.");

            header("Location: unAliasDeCategoria.php?Modo=" . $MODO_CAMBIO . "&IdCategoria=" . $IdCategoria . "&IdAlias=" . $IdAlias); // Se carga el alias guardado.
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            $IdNuevaCategoria = $IdCategoria;
            $ClaseAlias->CambioAlias($IdAlias, $Alias, $IdCategoria, $IdNuevaCategoria, $NumError, $IdNuevoAlias);

            if ($NumError == 1001)
                throw new Exception("El alias " . $Alias . " es el nombre de una categoría. No se puede cambiar.");
            else if ($NumError == 2001)
                throw new Exception($ErrorNoExisteCategoriaConIdEspecificado);
            else if ($NumError == 3001)
                throw new Exception("No existe la categoría con el id " . $IdNuevaCategoria . ".");
            else if ($NumError == 4001)
                throw new Exception($ErrorNoExisteAliasConIdEspecificadoEnIdCategoria);
            else if ($NumError == 5001)
                throw new Exception("El alias " . $Alias . " es el nombre de esta categoría. No se puede cambiar.");
            else if ($NumError == 6001)
                throw new Exception("El alias " . $Alias . " es el nombre de otra categoría. No se puede cambiar.");
            else if ($NumError == 7001)
                throw new Exception($ErrorAliasInvalido);
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'CambioAlias'.");

            if ($IdAlias != $IdNuevoAlias)
                header("Location: unAliasDeCategoria.php?Modo=" . $MODO_CAMBIO . "&IdCategoria=" . $IdCategoria . "&IdAlias=" . $IdNuevoAlias);
        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)

        $MensajeXDesglosar = CMensajes::MensajeOK("Se guardó el alias exitosamente.");
    } // if (isset($_POST["Alias"]))
*/
    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $ClaseAlias = new CAliasYCategorias();
        $ClaseAlias->ConsultarXAlias2($IdAlias, $Existe, $Alias);
        $ErrorNoExisteAliasConIdEspecificado = "No existe el alias con el id " . $IdAlias . ".";

        if (!$Existe)
            throw new Exception($ErrorNoExisteAliasConIdEspecificado);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = CMensajes::MensajeError($e->getMessage());
} // catch (Exception $e)

$MaximoTamanoCampoAlias = CAliasYCategorias::MAXIMO_TAMANO_CAMPO_ALIAS;
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Alias">Alias</label>
                <input type="text" class="form-control" id="Alias" name="Alias" placeholder="Ingrese el alias" value="<?php echo htmlspecialchars($Alias); ?>" maxlength="<?php echo $MaximoTamanoCampoAlias;?>">
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <!--<button type="submit" class="btn btn-primary">Guardar</button>!-->
                <button type="button" class="btn btn-primary" onclick="window.top.location.href='aliasYCategorias.php';">Regresar</button>
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
