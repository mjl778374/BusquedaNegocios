<?php
include_once "CSQL.php";

class CNegocios extends CSQL
{

    public const MAXIMO_TAMANO_CAMPO_NOMBRE = 100;
    public const MAXIMO_TAMANO_CAMPO_DIRECCION = 150;
    public const MAXIMO_TAMANO_CAMPO_TELEFONOS = 100;

    function __construct()
    {
        parent::__construct();
    } // function __construct()

    public function DemeTodasCategorias($IdNegocio)
    {
	$Retorno = [];

        $Consulta = "SELECT a.IdCategoria, b.Alias FROM AliasPrincipalesXCategoriaNegocios a, Alias b WHERE a.IdAlias = b.IdAlias AND b.EstaLibre = 0 AND a.IdCategoria IN (SELECT c.IdCategoria FROM CategoriasXNegocio c WHERE c.IdNegocio = ?) ORDER BY b.Alias ASC";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdNegocio));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $IdCategoria = $ResultadoConsulta[0];
                $Categoria = $ResultadoConsulta[1];

                $Retorno[] = array($IdCategoria, $Categoria);
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function DemeTodasCategorias($IdAlias)

    public function DemeTodosNegocios($IdAlias, $IdRegion, $IdProvincia, $IdCanton)
    {
        include_once "constantesApp.php";
	$Retorno = [];

        $Consulta = "SELECT distinct a.IdNegocio, a.Nombre, a.Direccion, a.Telefonos, b.Canton, c.Provincia, d.Region FROM Negocios a, Cantones b, Provincias c, RegionesGeograficas d, CategoriasXNegocio e WHERE a.IdCanton = b.IdCanton AND b.IdProvincia = c.IdProvincia AND b.IdRegionGeografica = d.IdRegion AND a.IdNegocio = e.IdNegocio AND e.IdCategoria IN (SELECT f.IdCategoria FROM AliasCategoriasNegocios f WHERE f.IdAlias = ?)";

        $TiposParametros = "i";
        $ArregloParametros = [];
        $ArregloParametros[] = $IdAlias;

        if($IdRegion != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION || $IdProvincia != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION || $IdCanton != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
        {
            $Consulta = $Consulta . " AND (1 = 0";

            if ($IdRegion != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
            {
                $ArregloParametros[] = $IdRegion;
                $TiposParametros = $TiposParametros . "i";
                $Consulta = $Consulta . " OR d.IdRegion = ?";
            } // if ($IdRegion != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)

            if ($IdProvincia != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
            {
                $ArregloParametros[] = $IdProvincia;
                $TiposParametros = $TiposParametros . "i";
                $Consulta = $Consulta . " OR c.IdProvincia = ?";
            } // if ($IdProvincia != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)

            if ($IdCanton != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
            {
                $ArregloParametros[] = $IdCanton;
                $TiposParametros = $TiposParametros . "i";
                $Consulta = $Consulta . " OR b.IdCanton = ?";
            } // if ($IdCanton != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)

            $Consulta = $Consulta . ")";
        } // if($IdRegion != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION || $IdProvincia != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION || $IdCanton != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)

        $Consulta = $Consulta . " ORDER BY a.Nombre ASC, b.Canton ASC";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $IdNegocio = $ResultadoConsulta[0];
                $Nombre = $ResultadoConsulta[1];
                $Direccion = $ResultadoConsulta[2];
                $Telefonos = $ResultadoConsulta[3];
                $Canton = $ResultadoConsulta[4];
                $Provincia = $ResultadoConsulta[5];
                $Region = $ResultadoConsulta[6];

                $Retorno[] = array($IdNegocio, $Nombre, $Direccion, $Telefonos, $Canton, $Provincia, $Region);
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function DemeTodosNegocios($IdAlias, $IdRegion, $IdProvincia, $IdCanton)

    public function ConsultarXNegocio($IdNegocio, &$Existe, &$Nombre, &$Direccion, &$Telefonos, &$IdCanton)
    {
        $Consulta = "SELECT Nombre, Direccion, Telefonos, IdCanton FROM Negocios WHERE IdNegocio = ?";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdNegocio));
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $Nombre = $ResultadoConsulta[0];
                $Direccion = $ResultadoConsulta[1];
                $Telefonos = $ResultadoConsulta[2];
                $IdCanton = $ResultadoConsulta[3];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXNegocio($IdNegocio, &$Existe, &$Nombre, &$Direccion, &$Telefonos, &$IdCanton)

    public function ConsultarXNegocio2($IdNegocio, $IdCategoria, &$Existe)
    {
        $Consulta = "SELECT a.Nombre FROM Negocios a WHERE a.IdNegocio = ? AND a.IdNegocio IN (SELECT b.IdNegocio FROM CategoriasXNegocio b WHERE b.IdCategoria = ? AND a.IdNegocio = b.IdNegocio)";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'ii', array($IdNegocio, $IdCategoria));
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXNegocio2($IdNegocio, $IdCategoria, &$Existe)

    private function DemePalabrasMasParecidas($PalabrasBusqueda)
    {
        include_once "CPalabrasSemejantes.php";

        $PalabrasSemejantes = new CPalabrasSemejantes();
        $Retorno = $PalabrasSemejantes->DemePalabrasMasParecidas($PalabrasBusqueda, "PalabrasXNegocio", array("PalabrasXCanton"));

        return $Retorno;
    } // private function DemePalabrasMasParecidas($PalabrasBusqueda)

    public function ConsultarXTodosNegocios($PalabrasBusqueda)
    {
        include_once "CCantones.php"; /* Se consultan campos de la clase CCantones */

        $Retorno = [];
        $PalabrasMasParecidas = $this->DemePalabrasMasParecidas($PalabrasBusqueda);

        $Consulta = "";
        $Consulta = $Consulta . "(";
        $Consulta = $Consulta . "     SELECT c.IdPalabra";
        $Consulta = $Consulta . "     FROM Palabras c";
        $Consulta = $Consulta . "     WHERE (1 = 0";

        $TiposParametros = "";
        $ArregloParametros = [];

        for($NumConsultasPalabras = 0; $NumConsultasPalabras < 2; $NumConsultasPalabras++)
        {
            for($i = 0; $i < count($PalabrasMasParecidas); $i++)
            {
                $ArregloParametros[] = $PalabrasMasParecidas[$i];
                $TiposParametros = $TiposParametros . "i";

                if ($NumConsultasPalabras == 0)
                    $Consulta = $Consulta . " OR c.IdPalabraSemejante = ?";
            } // for($i = 0; $i < count($PalabrasMasParecidas); $i++)
        } // for($NumConsultasPalabras = 0; $NumConsultasPalabras < 2; $NumConsultasPalabras++)

        $Consulta = $Consulta . ")";
        $Consulta = $Consulta . ")";

        $ConsultaPalabras = $Consulta;
        $Consulta = "";

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdNegocio, a.Nombre, c.Canton, 'N' as Tipo";
        $Consulta = $Consulta . " FROM Negocios a, PalabrasXNegocio b, Cantones c";
        $Consulta = $Consulta . " WHERE a.IdNegocio = b.IdNegocio";
        $Consulta = $Consulta . " AND a.IdCanton = c.IdCanton";
        $Consulta = $Consulta . " AND b.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdNegocio, a.Nombre, c.Canton, Tipo";

        $Consulta = $Consulta . " UNION ";

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdNegocio, a.Nombre, c.Canton, 'C' as Tipo";
        $Consulta = $Consulta . " FROM Negocios a, PalabrasXCanton b, Cantones c";
        $Consulta = $Consulta . " WHERE b.IdCanton = c.IdCanton";
        $Consulta = $Consulta . " AND a.IdCanton = c.IdCanton";
        $Consulta = $Consulta . " AND b.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdNegocio, a.Nombre, c.Canton, Tipo";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

        if ($ConsultaEjecutadaExitosamente)
        {
            include_once "CGroupByCantidad.php";
            $GroupBy = new CGroupByCantidad();
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            $TamanoCampoIdNegocio = 0;

            while ($ResultadoConsulta != NULL)
            {
                $NumAciertos = $ResultadoConsulta[0];
                $IdNegocio = $ResultadoConsulta[1];
                $NombreNegocio = $ResultadoConsulta[2];
                $Canton = $ResultadoConsulta[3];

                if (strlen($IdNegocio) > $TamanoCampoIdNegocio)
                    $TamanoCampoIdNegocio = strlen($IdNegocio);

                $GroupBy->AgregarTupla(array($IdNegocio, $NombreNegocio, $Canton), array(0), array(0,1,2), $NumAciertos);
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $TamanoCampoCantidad = floor(log10($GroupBy->DemeMaximaCantidad())) + 1;
            $Retorno = $GroupBy->OrdenarTuplas(array(array(3,'i',$TamanoCampoCantidad,'desc',$GroupBy->DemeMaximaCantidad()), array(1, 's', self::MAXIMO_TAMANO_CAMPO_NOMBRE), array(2, 's', CCantones::MAXIMO_TAMANO_CAMPO_CANTON), array(0, 'i', $TamanoCampoIdNegocio, 'asc')));
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function ConsultarXTodosNegocios($PalabrasBusqueda)

    public function AltaNegocio($Nombre, $Direccion, $Telefonos, $IdCanton, &$NumError, &$IdNegocio)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL AltaNegocio(?, ?, ?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'sssisssss', array($Nombre, $Direccion, $Telefonos, $IdCanton, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdNegocio = $ResultadoConsulta[1];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaNegocio($Nombre, $Direccion, $Telefonos, $IdCanton, &$NumError, &$IdNegocio)

    public function CambioNegocio($IdNegocio, $Nombre, $Direccion, $Telefonos, $IdCanton, &$NumError)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL CambioNegocio(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'isssisssss', array($IdNegocio, $Nombre, $Direccion, $Telefonos, $IdCanton, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $NumError = $ResultadoConsulta[0];

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioNegocio($IdNegocio, $Nombre, $Direccion, $Telefonos, $IdCanton, &$NumError)

    public function AltaCategoria($IdNegocio, $IdCategoria, &$NumError)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL AltaNegocioEnCategoria(?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'iisssss', array($IdNegocio, $IdCategoria, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaCategoria($IdNegocio, $IdCategoria, &$NumError)

    public function CambioCategoria($IdNegocio, $IdCategoria, $IdNuevaCategoria, &$NumError)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL CambioNegocioDeCategoria(?, ?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'iiisssss', array($IdNegocio, $IdCategoria, $IdNuevaCategoria, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioCategoria($IdNegocio, $IdCategoria, $IdNuevaCategoria, &$NumError)

    public function IndexarTodo()
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL IndexarTodosNegocios(?, ?, ?, ?, ?, 0);";
        $this->EjecutarConsulta($Consulta, 'sssss', array(CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));
    } // public function IndexarTodo()

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class CNegocios extends CSQL
?>
