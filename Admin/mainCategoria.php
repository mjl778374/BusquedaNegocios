<?php
    include_once "constantesApp.php";
    include_once "CSession.php";
    $UsuarioSesionIngresoOKApp = CSession::UsuarioIngresoOK();
    if (!$UsuarioSesionIngresoOKApp)
        header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación
    // La validación anterior debe ser la primera que se realice, pues al verificarse si el usuario ingresó correctamente, 
    // se calcula el tiempo transcurrido desde el último inicio de sesión, del cual se requiere saber si ya
    // pasó cierto tiempo o no.

    $ParametrosGet = "";
    $comodin = "?";

    if (isset($_GET["Modo"]))
    {
        $ParametrosGet = $ParametrosGet . $comodin . 'Modo=' . $_GET["Modo"];
        $comodin = "&";
    } // if (isset($_GET["Modo"]))

    if (isset($_GET["IdCategoria"]))
    {
        $ParametrosGet = $ParametrosGet . $comodin . 'IdCategoria=' . $_GET["IdCategoria"];
        $comodin = "&";
    } // if (isset($_GET["IdCategoria"]))
?>
<frameset rows="50%,*">
   <frame src="categoria.php<?php echo $ParametrosGet;?>">
   <frame src="todosAliasDeCategoria.php<?php echo $ParametrosGet;?>">
</frameset>
