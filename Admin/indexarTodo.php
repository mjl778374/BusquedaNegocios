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

    $FormularioActivo = "IndexarTodo"; // Este es un parámetro que recibe "menuApp.php"
    include "menuApp.php";

    if (isset($_POST["IndexarTodo"]))
    {
        $MensajeXDesglosar = "";

        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se van a indexar los usuarios...");
        include_once "CUsuarios.php";
        $Usuarios = new CUsuarios();
        $Usuarios->IndexarTodo();
        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se indexaron los usuarios exitosamente.");

        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se van a indexar las regiones geográficas...");
        include_once "CRegionesGeograficas.php";
        $Regiones = new CRegionesGeograficas();
        $Regiones->IndexarTodo();
        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se indexaron las regiones geográficas exitosamente.");

        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se van a indexar las provincias...");
        include_once "CProvincias.php";
        $Provincias = new CProvincias();
        $Provincias->IndexarTodo();
        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se indexaron las provincias exitosamente.");

        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se van a indexar los cantones...");
        include_once "CCantones.php";
        $Cantones = new CCantones();
        $Cantones->IndexarTodo();
        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se indexaron los cantones exitosamente.");

        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se van a indexar los alias y categorías...");
        include_once "CAliasYCategorias.php";
        $AliasYCategorias = new CAliasYCategorias();
        $AliasYCategorias->IndexarTodo();
        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se indexaron los alias y categorías exitosamente.");

        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se van a indexar los negocios...");
        include_once "CNegocios.php";
        $Negocios = new CNegocios();
        $Negocios->IndexarTodo();
        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se indexaron los negocios exitosamente.");

        $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeOK("Se indexó todo exitosamente.");
    } // if (isset($_POST["IndexarTodo"]))
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = $MensajeXDesglosar . CMensajes::MensajeError($e->getMessage());
} // catch (Exception $e)
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary btn-lg btn-block" name="IndexarTodo">Indexar Todo</button>
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
