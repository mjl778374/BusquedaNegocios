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

    $FormularioActivo = "Negocios";
    $URLFormularioActivo = "negocios.php";
    // Los anteriores son parámetros que recibe "menuApp.php"
    include "menuApp.php";

    include_once "CNegocios.php";
    session_start();
    $TextoXBuscar = "";

    if (isset($_GET["TextoXBuscar"]))
        $TextoXBuscar = $_GET["TextoXBuscar"];
    elseif (isset($_SESSION["Negocios_TextoXBuscar"]))
        $TextoXBuscar = $_SESSION["Negocios_TextoXBuscar"];

    $Negocios = new CNegocios();
    $ListadoNegocios = $Negocios->ConsultarXTodosNegocios($TextoXBuscar);
    $_SESSION["Negocios_TextoXBuscar"] = $TextoXBuscar;

    include_once "constantesApp.php";
    $NumPaginas = 0;

    if (count($ListadoNegocios) > 0 && $MAX_NUM_RESULTADOS_X_PAGINA > 0)
        $NumPaginas = ceil(count($ListadoNegocios) / $MAX_NUM_RESULTADOS_X_PAGINA);

    include_once "CParametrosGet.php";
    $NumPaginaActual = CParametrosGet::DemeNumPagina("NumPagina", $NumPaginas);
    // Los anteriores son parámetros que manipulan "componenteTabla.php" y "componentePaginacion.php"

    if (isset($_GET["NumPagina"]) && strcmp($_GET["NumPagina"], $NumPaginaActual) != 0)
        header("Location: " . "negocios.php?NumPagina=" . $NumPaginaActual);

    include_once "constantesApp.php";
    if ($NumPaginaActual > 0)
    {
        $EncabezadoTabla = array("Negocio", "Cantón");
        $Filas = [];
        $IndiceInicial = ($NumPaginaActual - 1) * $MAX_NUM_RESULTADOS_X_PAGINA;
        $IndiceFinal = $IndiceInicial + $MAX_NUM_RESULTADOS_X_PAGINA - 1;

        if ($IndiceFinal >= count($ListadoNegocios))
            $IndiceFinal = count($ListadoNegocios) - 1;

        for ($i = $IndiceInicial; $i <= $IndiceFinal; $i++)
        {
            $Negocio = $ListadoNegocios[$i];
            $Fila = array("mainNegocio.php?Modo=" . $MODO_CAMBIO . "&IdNegocio=" . $Negocio[0], $Negocio[1], $Negocio[2]);
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
<a href="negocio.php?Modo=<?php echo $MODO_ALTA;?>" class="btn btn-primary" role="button" aria-pressed="true">Agregar Negocio</a>
</div>

<?php
$URL = "negocios.php";
// Los anteriores son parámetros que recibe "componentePaginacion.php"

if ($NumPaginas > 0)
    include "componentePaginacion.php";
?>
<?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
</html>
