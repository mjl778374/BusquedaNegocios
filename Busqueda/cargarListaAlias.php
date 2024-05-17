<?php
$TextoXBuscar = $_GET['TextoXBuscar'];

include_once "CAliasYCategorias.php";
$AliasYCategorias = new CAliasYCategorias();
$ListadoAlias = $AliasYCategorias->ConsultarXTodosAlias($TextoXBuscar);
?>

<script>
window.parent.VaciarListaAlias();

<?php

include "FuncionesUtiles.php";

for($i = 0; $i < count($ListadoAlias); $i++)
{
$UnAlias = $ListadoAlias[$i];
$IdAlias = $UnAlias[0];
$Alias = $UnAlias[1];
$Alias = htmlspecialchars(FormatearTexto($Alias));
?>
window.parent.AgregarAliasAListaAlias(<?php echo $IdAlias;?>, "<?php echo $Alias; ?>");
<?php
} // for($i = 0; $i < count($ListadoAlias); $i++)
?>
</script>
