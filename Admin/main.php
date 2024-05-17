<!DOCTYPE html>
<html>
<?php
include_once "constantesApp.php";
include_once "CSession.php";

$UsuarioSesionIngresoOKApp = CSession::UsuarioIngresoOK();

if (!$UsuarioSesionIngresoOKApp)
    header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación
?>
<?php
include "encabezados.php";
$FormularioActivo = "Main"; // Este es un parámetro que recibe "menuApp.php"
include "menuApp.php";
?>
<?php
include_once "CSession.php";
$NombreUsuario = CSession::DemeNombreUsuario();
$TextoXDesplegar = trim("Bienvenid@ " . trim($NombreUsuario));
?>
<div class="container mt-4">
<blockquote class="blockquote text-center">
  <h1 class="display-4"><?php echo htmlspecialchars($TextoXDesplegar);?></h1>
</blockquote>
</div>
</html>
