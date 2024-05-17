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

    $FormularioActivo = "RegionesGeograficas";
    $URLFormularioActivo = "regionesGeograficas.php";
    // Los anteriores son parámetros que recibe "menuApp.php"
    include "menuApp.php";

    include_once "CRegionesGeograficas.php";
    session_start();
    $TextoXBuscar = "";

    if (isset($_GET["TextoXBuscar"]))
        $TextoXBuscar = $_GET["TextoXBuscar"];
    elseif (isset($_SESSION["RegionesGeograficas_TextoXBuscar"]))
        $TextoXBuscar = $_SESSION["RegionesGeograficas_TextoXBuscar"];

    $Regiones = new CRegionesGeograficas();
    $ListadoRegiones = $Regiones->ConsultarXTodasRegionesGeograficas($TextoXBuscar);
    $_SESSION["RegionesGeograficas_TextoXBuscar"] = $TextoXBuscar;

    include_once "constantesApp.php";
    $NumPaginas = 0;

    if (count($ListadoRegiones) > 0 && $MAX_NUM_RESULTADOS_X_PAGINA > 0)
        $NumPaginas = ceil(count($ListadoRegiones) / $MAX_NUM_RESULTADOS_X_PAGINA);

    include_once "CParametrosGet.php";
    $NumPaginaActual = CParametrosGet::DemeNumPagina("NumPagina", $NumPaginas);
    // Los anteriores son parámetros que manipulan "componenteTabla.php" y "componentePaginacion.php"

    if (isset($_GET["NumPagina"]) && strcmp($_GET["NumPagina"], $NumPaginaActual) != 0)
        header("Location: " . "regionesGeograficas.php?NumPagina=" . $NumPaginaActual);

    include_once "constantesApp.php";
    if ($NumPaginaActual > 0)
    {
        $EncabezadoTabla = array("Región");
        $Filas = [];
        $IndiceInicial = ($NumPaginaActual - 1) * $MAX_NUM_RESULTADOS_X_PAGINA;
        $IndiceFinal = $IndiceInicial + $MAX_NUM_RESULTADOS_X_PAGINA - 1;

        if ($IndiceFinal >= count($ListadoRegiones))
            $IndiceFinal = count($ListadoRegiones) - 1;

        for ($i = $IndiceInicial; $i <= $IndiceFinal; $i++)
        {
            $Region = $ListadoRegiones[$i];
            $Fila = array("regionGeografica.php?Modo=" . $MODO_CAMBIO . "&IdRegion=" . $Region[0], $Region[1]);
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
<a href="regionGeografica.php?Modo=<?php echo $MODO_ALTA;?>" class="btn btn-primary" role="button" aria-pressed="true">Agregar Región</a>
</div>

<?php
$URL = "regionesGeograficas.php";
// Los anteriores son parámetros que recibe "componentePaginacion.php"

if ($NumPaginas > 0)
    include "componentePaginacion.php";
?>
<?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
</html>
