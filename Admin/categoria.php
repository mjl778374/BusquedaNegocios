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
    $FormularioActivo = "Categoria"; // Este es un parámetro que recibe "menuApp.php"
    include "menuApp.php";

    $Categoria = "";
    
    if (isset($_POST["Categoria"]))
        $Categoria = $_POST["Categoria"];

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

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $IdCategoria = CParametrosGet::ValidarIdEntero("IdCategoria", $NumError);
        	
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdCategoria'.");
        elseif ($NumError == 2)
            throw new Exception("'IdCategoria' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdCategoria'.");

        if (strcmp($_GET["IdCategoria"], $IdCategoria) != 0)
            header("Location: " . "categoria.php?Modo=" . $Modo . "&IdCategoria=" . $IdCategoria);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)

    $ErrorNoExisteCategoriaConIdEspecificado = "No existe la categoría con el id " . $IdCategoria . ".";

    if (isset($_POST["Categoria"]))
    {
        $AliasYCategorias = new CAliasYCategorias();

        include_once "CPalabras.php";

        $ErrorCategoriaInvalida = "La categoría debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();

        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            $AliasYCategorias->AltaCategoria($Categoria, $NumError, $IdCategoria);

            if ($NumError == 1001)
                throw new Exception("Ya existe la categoría " . $Categoria . ". No se puede insertar nuevamente.");
            else if ($NumError == 2001)
                throw new Exception("Ya existe un alias con el nombre " . $Categoria . ". No se puede insertar la categoría.");
            elseif ($NumError == 3001)
                throw new Exception($ErrorCategoriaInvalida);
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'AltaCategoria'.");

            header("Location: mainCategoria.php?Modo=" . $MODO_CAMBIO . "&IdCategoria=" . $IdCategoria); // Se carga la categoría guardada.
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            $AliasYCategorias->CambioCategoria($IdCategoria, $Categoria, $NumError);

            if ($NumError == 1001)
                throw new Exception("Ya existe la categoría " . $Categoria . " con otro id.");
            elseif ($NumError == 2001)
                throw new Exception($ErrorNoExisteCategoriaConIdEspecificado);
            else if ($NumError == 3001)
                throw new Exception($Categoria . " es el nombre de un alias en esta categoría. No se puede cambiar.");
            else if ($NumError == 4001)
                throw new Exception($Categoria . " es el nombre de un alias en otra categoría. No se puede cambiar.");
            elseif ($NumError == 5001)
                throw new Exception($ErrorCategoriaInvalida);
            elseif ($NumError != 0)
                throw new Exception("No se manejó el error número " . $NumError . " en el proceso 'CambioCategoria'.");

        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)

        $MensajeXDesglosar = CMensajes::MensajeOK("Se guardó la categoría exitosamente.");
    } // if (isset($_POST["Categoria"]))

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $AliasYCategorias = new CAliasYCategorias();
        $AliasYCategorias->ConsultarXCategoria($IdCategoria, $Existe, $Categoria);

        if (!$Existe)
            throw new Exception($ErrorNoExisteCategoriaConIdEspecificado);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = CMensajes::MensajeError($e->getMessage());
} // catch (Exception $e)

$MaximoTamanoCampoCategoria = CAliasYCategorias::MAXIMO_TAMANO_CAMPO_ALIAS;
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Categoria">Categoría</label>
                <input type="text" class="form-control" id="Categoria" name="Categoria" placeholder="Ingrese la categoría" value="<?php echo htmlspecialchars($Categoria); ?>" maxlength="<?php echo $MaximoTamanoCampoCategoria;?>">
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
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
