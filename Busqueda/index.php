<!DOCTYPE html>
<html>
<?php
try
{
    include_once "CMensajes.php"; // Esto es para desglosar mensajes de error si ocurren (como los relativos a la base de datos)
    $MensajeXDesglosar = "";
 
    include_once "constantesApp.php";
    include_once "CParametrosGet.php";

    $IdRegionGeografica = CParametrosGet::ValidarIdEntero("IdRegion", $NumError);
    if ($NumError != 0)
        $IdRegionGeografica = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;

    $IdProvincia = CParametrosGet::ValidarIdEntero("IdProvincia", $NumError);
    if ($NumError != 0)
        $IdProvincia = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;

    $IdCanton = CParametrosGet::ValidarIdEntero("IdCanton", $NumError);
    if ($NumError != 0)
        $IdCanton = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;

    $ListadoNegocios = [];
    $Alias = "";
    $IdAlias = CParametrosGet::ValidarIdEntero("IdAlias", $NumError);

    if ($NumError == 0)
    {
        include_once "CAliasYCategorias.php";
        $AliasYCategorias = new CAliasYCategorias();
        $AliasYCategorias->ConsultarXAlias3($IdAlias, $Existe, $Alias);

        if (!$Existe)
            $Alias = "";

        include_once "CNegocios.php";
        $Negocios = new CNegocios();
        $ListadoNegocios = $Negocios->DemeTodosNegocios($IdAlias, $IdRegionGeografica, $IdProvincia, $IdCanton);
    } // if ($NumError == 0)

    include_once "CRegionesGeograficas.php";
    $RegionesGeograficas = new CRegionesGeograficas();
    $ListadoRegionesGeograficas = $RegionesGeograficas->DemeTodasRegionesGeograficas();

    $IdListaRegiones = "IdRegion";
    $NameListaRegiones = "IdRegion";

    include_once "CProvincias.php";
    $Provincias = new CProvincias();
    $ListadoProvincias = $Provincias->DemeTodasProvincias();

    $IdListaProvincias = "IdProvincia";
    $NameListaProvincias = "IdProvincia";

    include_once "CCantones.php";
    $Cantones = new CCantones();
    $ListadoCantones = $Cantones->DemeTodosCantones();

    $IdListaCantones = "IdCanton";
    $NameListaCantones = "IdCanton";
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = CMensajes::MensajeError($e->getMessage());
} // catch (Exception $e)
?>
<?php
include "encabezados.php";
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <a class="navbar-brand" href="">Menú</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoRegionesGeograficas;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Región Geográfica");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdRegionGeografica;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion=$IdListaRegiones; $NameListaSeleccion=$NameListaRegiones; include "componenteListaSeleccion.php" ?>

      </li>
      <li class="nav-item">
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoProvincias;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Provincia");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdProvincia;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion=$IdListaProvincias; $NameListaSeleccion=$NameListaProvincias; include "componenteListaSeleccion.php" ?>
      </li>
      <li class="nav-item">
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoCantones;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Cantón");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdCanton;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion=$IdListaCantones; $NameListaSeleccion=$NameListaCantones; include "componenteListaSeleccion.php" ?>
      </li>
    </ul>
<form class="form-inline my-2 my-lg-0" method="post">
      <input class="form-control col-12" type="search" placeholder="Categoría por Buscar" aria-label="Buscar" name="AliasXBuscar" onkeyup="CargarListaAlias(this.value);">
</form>
  </div>
</nav>
<div id="ListaAlias" class="list-group">
</div>
<?php
if ($Alias != "")
    echo "<h3>" . htmlspecialchars($Alias) . "</h3>";
?>
<div class="container mt-12">
<div class="row">
  <div class="col-4">
    <div class="list-group" id="list-tab" role="tablist">
<?php
    include "FuncionesUtiles.php";

    $EventoOnLoad = "";

    for($i = 0; $i < count($ListadoNegocios); $i++)
    {
        $Negocio = $ListadoNegocios[$i];
        $Nombre = $Negocio[1];
        $TituloNegocio = htmlspecialchars($Nombre);
        $Nombre = "Nombre del Negocio: " . htmlspecialchars(FormatearTexto($Nombre));
        $Direccion = "Dirección: " . htmlspecialchars(FormatearTexto($Negocio[2]));
        $Telefonos = "Teléfonos: " . htmlspecialchars(FormatearTexto($Negocio[3]));
        $Canton = "Cantón: " . htmlspecialchars(FormatearTexto($Negocio[4]));
        $Provincia = "Provincia: " . htmlspecialchars(FormatearTexto($Negocio[5]));
        $Region = "Región Geográfica: " . htmlspecialchars(FormatearTexto($Negocio[6]));

        $EventoOnClick = "CargarDatosNegocio('list-home', '" . $Nombre . "', '" . $Direccion . "', '" . $Telefonos . "', '" . $Canton . "', '" . $Provincia . "', '" .$Region . "');";

        $ItemActivo = "";
        if ($i == 0)
        {
            $ItemActivo = "active";
            $EventoOnLoad = $EventoOnClick;
        } // if ($i == 0)
?>
      <a class="list-group-item list-group-item-action <?php echo $ItemActivo; ?>" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home" onclick="<?php echo $EventoOnClick;?>"><?php echo $TituloNegocio;?></a>
<?php
    } // for($i = 0; $i < count($ListadoNegocios); $i++)
?>
    </div>
  </div>
  <div class="col-8">
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list"></div>
    </div>
  </div>
</div>
</div>
<?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
<script>
<?php
if ($EventoOnLoad != "") // Requiere que el evento termine con ";"
    echo $EventoOnLoad;
?>

function CargarDatosNegocio(IdLista, Nombre, Direccion, Telefonos, Canton, Provincia, Region)
{
    var Lista = document.getElementById(IdLista);
    Lista.innerHTML = EnvolverDentroParrafo(Nombre) + EnvolverDentroParrafo(Direccion) + EnvolverDentroParrafo(Telefonos) + EnvolverDentroParrafo(Canton) + EnvolverDentroParrafo(Provincia) + EnvolverDentroParrafo(Region);
} // function CargarDatosNegocio(IdLista, Nombre, Direccion, Telefonos, Canton, Provincia, Region)

function EnvolverDentroParrafo(Texto)
{
    return "<br>" + Texto + "</br>";
} // function EnvolverDentroParrafo(Texto)

function OpcionSeleccionada(IdListaSeleccion)
{
    var ListaSeleccion = document.getElementById(IdListaSeleccion);
    return ListaSeleccion.options[ListaSeleccion.selectedIndex].value;
} // function OpcionSeleccionada(IdListaSeleccion)

function SeleccionarAlias(iIdAlias)
{
    var m_sHref = "<?php echo $URL_PAGINA_INGRESO; ?>?IdAlias=" + iIdAlias;
    var m_sIdRegion = OpcionSeleccionada("<?php echo $IdListaRegiones;?>");
    var m_sIdProvincia = OpcionSeleccionada("<?php echo $IdListaProvincias;?>");
    var m_sIdCanton = OpcionSeleccionada("<?php echo $IdListaCantones;?>");

    if (m_sIdRegion != "<?php echo $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;?>")
        m_sHref = m_sHref + "&IdRegion=" + m_sIdRegion;

    if (m_sIdProvincia != "<?php echo $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;?>")
        m_sHref = m_sHref + "&IdProvincia=" + m_sIdProvincia;

    if (m_sIdCanton != "<?php echo $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;?>")
        m_sHref = m_sHref + "&IdCanton=" + m_sIdCanton;

    window.location.href = m_sHref;
} // function SeleccionarAlias(iIdAlias)

function VaciarListaAlias()
{
    document.getElementById("ListaAlias").innerHTML = "";
} // function VaciarListaAlias()

function AgregarAliasAListaAlias(iIdAlias, sAlias)
{
    var m_sHTML = document.getElementById("ListaAlias").innerHTML;
    m_sHTML = m_sHTML + '<button type="button" class="list-group-item list-group-item-action" onclick="SeleccionarAlias(' + iIdAlias + ');">' + sAlias + '</button>';
    document.getElementById("ListaAlias").innerHTML = m_sHTML;
} // function AgregarAliasAListaAlias(iIdAlias, sAlias)

function CargarListaAlias(sTextoXBuscar)
{
    sTextoXBuscar = ReemplazarTodo(sTextoXBuscar, "\\", "+"); // La función "ReemplazarTodo" se encuentra en el archivo "FuncionesUtiles.js" que se carga desde "encabezados.php"
    sTextoXBuscar = ReemplazarTodo(sTextoXBuscar, "?", "+");
    sTextoXBuscar = ReemplazarTodo(sTextoXBuscar, "&", "+");
    sTextoXBuscar = ReemplazarTodo(sTextoXBuscar, " ", "+");

    var m_sHref = 'cargarListaAlias.php?TextoXBuscar=' + sTextoXBuscar;
    window.fraProcesar.location.href = m_sHref;
} // function CargarListaAlias(sTextoXBuscar)
</script>
<?php
$AnchoFrame = 0;
$AltoFrame = 0;

if ($MOSTRAR_CONSULTAS_SQL)
{
    $AnchoFrame = "100%";
    $AltoFrame = 400;
} // if ($MOSTRAR_CONSULTAS_SQL)
?>
<iframe name="fraProcesar" width="<?php echo $AnchoFrame;?>" height="<?php echo $AltoFrame;?>"></iframe>
</html>
