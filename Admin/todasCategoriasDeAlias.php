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
    include_once "CParametrosGet.php";
    $MensajeXDesglosar = "";

    include "encabezados.php";

    $IdAlias = CParametrosGet::ValidarIdEntero("IdAlias", $NumError);

    if ($NumError == 1)
        throw new Exception("Debe incorporar el parámetro 'IdAlias'.");
    elseif ($NumError == 2)
        throw new Exception("'IdAlias' debe ser un número entero mayor o igual que 0.");
    elseif ($NumError != 0)
        throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdAlias'.");

    include_once "CAliasYCategorias.php";

    $AliasYCategorias = new CAliasYCategorias();
    $ListadoCategorias = $AliasYCategorias->DemeTodasCategorias($IdAlias);

    include_once "constantesApp.php";
    $NumPaginas = 0;

    if (count($ListadoCategorias) > 0 && $MAX_NUM_RESULTADOS_X_PAGINA > 0)
        $NumPaginas = ceil(count($ListadoCategorias) / $MAX_NUM_RESULTADOS_X_PAGINA);

    include_once "CParametrosGet.php";
    $NumPaginaActual = CParametrosGet::DemeNumPagina("NumPagina", $NumPaginas);
    // Los anteriores son parámetros que manipulan "componenteTabla.php" y "componentePaginacion.php"

    if (isset($_GET["NumPagina"]) && strcmp($_GET["NumPagina"], $NumPaginaActual) != 0)
        header("Location: " . "todasCategoriasDeAlias.php?IdAlias=" . $IdAlias . "&NumPagina=" . $NumPaginaActual);

    include_once "constantesApp.php";
    if ($NumPaginaActual > 0)
    {
        $EncabezadoTabla = array("Categoría");
        $Filas = [];
        $IndiceInicial = ($NumPaginaActual - 1) * $MAX_NUM_RESULTADOS_X_PAGINA;
        $IndiceFinal = $IndiceInicial + $MAX_NUM_RESULTADOS_X_PAGINA - 1;

        if ($IndiceFinal >= count($ListadoCategorias))
            $IndiceFinal = count($ListadoCategorias) - 1;

        for ($i = $IndiceInicial; $i <= $IndiceFinal; $i++)
        {
            $Categoria = $ListadoCategorias[$i];
            $Fila = array("unaCategoriaDeAlias.php?Modo=" . $MODO_CAMBIO . "&IdCategoria=" . $Categoria[0] . "&IdAlias=" . $IdAlias, $Categoria[1]);
            $Filas[] = $Fila;
        // Los anteriores son parámetros que recibe "componenteTabla.php"
        } // for ($i = $IndiceInicial; $i <= $IndiceFinal; $i++)

        if (count($Filas) > 0)
            include "componenteTabla.php";
    } // if ($NumPaginaActual > 0)

    $MensajeXDesglosar = "";
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = CMensajes::MensajeError($e->getMessage());
} // catch (Exception $e)
?>
<div class="container mt-4 mb-4">
<?php
include_once "constantesApp.php";
?>
<a href="unaCategoriaDeAlias.php?Modo=<?php echo $MODO_ALTA;?>&IdAlias=<?php echo $IdAlias;?>" class="btn btn-primary" role="button" aria-pressed="true">Agregar Categoría a Alias</a>
</div>

<?php
$URL = "todasCategoriasDeAlias.php";
$ParametrosURL = "?IdAlias=" . $IdAlias;
// Los anteriores son parámetros que recibe "componentePaginacion.php"

if ($NumPaginas > 0)
    include "componentePaginacion.php";
?>
<?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
</html>
