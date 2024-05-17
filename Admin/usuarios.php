<!DOCTYPE html>
<html>
<?php
try
{
    include_once "constantesApp.php";
    include_once "CSession.php";
    $UsuarioSesionIngresoOKApp = CSession::UsuarioIngresoOK();
    $UsuarioSesionEsAdmin = CSession::UsuarioSesionEsAdministrador();
    if (!$UsuarioSesionIngresoOKApp || !$UsuarioSesionEsAdmin)
        header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación
    // La validación anterior debe ser la primera que se realice, pues al verificarse si el usuario ingresó correctamente, 
    // se calcula el tiempo transcurrido desde el último inicio de sesión, del cual se requiere saber si ya
    // pasó cierto tiempo o no.

    include_once "CMensajes.php";
    $MensajeXDesglosar = "";

    include "encabezados.php";

    $FormularioActivo = "Usuarios";
    $URLFormularioActivo = "usuarios.php";
    // Los anteriores son parámetros que recibe "menuApp.php"
    include "menuApp.php";

    include_once "CUsuarios.php";
    session_start();
    $TextoXBuscar = "";

    if (isset($_GET["TextoXBuscar"]))
        $TextoXBuscar = $_GET["TextoXBuscar"];
    elseif (isset($_SESSION["Usuarios_TextoXBuscar"]))
        $TextoXBuscar = $_SESSION["Usuarios_TextoXBuscar"];

    $Usuarios = new CUsuarios();
    $ListadoUsuarios = $Usuarios->ConsultarXTodosUsuarios($TextoXBuscar);
    $_SESSION["Usuarios_TextoXBuscar"] = $TextoXBuscar;

    include_once "constantesApp.php";
    $NumPaginas = 0;

    if (count($ListadoUsuarios) > 0 && $MAX_NUM_RESULTADOS_X_PAGINA > 0)
        $NumPaginas = ceil(count($ListadoUsuarios) / $MAX_NUM_RESULTADOS_X_PAGINA);

    include_once "CParametrosGet.php";
    $NumPaginaActual = CParametrosGet::DemeNumPagina("NumPagina", $NumPaginas);
    // Los anteriores son parámetros que manipulan "componenteTabla.php" y "componentePaginacion.php"

    if (isset($_GET["NumPagina"]) && strcmp($_GET["NumPagina"], $NumPaginaActual) != 0)
        header("Location: " . "usuarios.php?NumPagina=" . $NumPaginaActual);

    include_once "constantesApp.php";
    if ($NumPaginaActual > 0)
    {
        $EncabezadoTabla = array("Usuario", "Cédula", "Nombre", "Es Administrador");
        $Filas = [];
        $IndiceInicial = ($NumPaginaActual - 1) * $MAX_NUM_RESULTADOS_X_PAGINA;
        $IndiceFinal = $IndiceInicial + $MAX_NUM_RESULTADOS_X_PAGINA - 1;

        if ($IndiceFinal >= count($ListadoUsuarios))
            $IndiceFinal = count($ListadoUsuarios) - 1;

        for ($i = $IndiceInicial; $i <= $IndiceFinal; $i++)
        {
            $Usuario = $ListadoUsuarios[$i];
            $EsAdministrador = "No";

            if ($Usuario[4] == "1")
                $EsAdministrador = "Sí";

            $Fila = array("usuario.php?Modo=" . $MODO_CAMBIO . "&IdUsuario=" . $Usuario[0], $Usuario[1], $Usuario[2], $Usuario[3], $EsAdministrador);
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
<a href="usuario.php?Modo=<?php echo $MODO_ALTA;?>" class="btn btn-primary" role="button" aria-pressed="true">Agregar Usuario</a>
</div>

<?php
$URL = "usuarios.php";
// Los anteriores son parámetros que recibe "componentePaginacion.php"

if ($NumPaginas > 0)
    include "componentePaginacion.php";
?>
<?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
</html>
