<?php
include_once "CSQL.php";

class CCantones extends CSQL
{
    public const MAXIMO_TAMANO_CAMPO_CANTON = 50;

    function __construct()
    {
        parent::__construct();
    } // function __construct()

    public function ConsultarXCanton($IdCanton, &$Existe, &$Canton, &$IdRegionGeografica, &$IdProvincia)
    {
        $Consulta = "SELECT Canton, IdRegionGeografica, IdProvincia FROM Cantones WHERE IdCanton = ?";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdCanton));
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $Canton = $ResultadoConsulta[0];
                $IdRegionGeografica = $ResultadoConsulta[1];
                $IdProvincia = $ResultadoConsulta[2];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXCanton($IdCanton, &$Existe, &$Canton, &$IdRegionGeografica, &$IdProvincia)

    public function DemeTodosCantones()
    {
        $Consulta = "SELECT IdCanton, Canton FROM Cantones ORDER BY Canton ASC";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, '', array());
        $Retorno = [];

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $Retorno[] = array($ResultadoConsulta[0], $ResultadoConsulta[1]);
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function DemeTodosCantones()

    private function DemePalabrasMasParecidas($PalabrasBusqueda)
    {
        include_once "CPalabrasSemejantes.php";

        $PalabrasSemejantes = new CPalabrasSemejantes();
        $Retorno = $PalabrasSemejantes->DemePalabrasMasParecidas($PalabrasBusqueda, "PalabrasXCanton", array("PalabrasXProvincia", "PalabrasXRegionGeografica"));

        return $Retorno;
    } // private function DemePalabrasMasParecidas($PalabrasBusqueda)

    public function ConsultarXTodosCantones($PalabrasBusqueda)
    {
        $Retorno = [];
        $PalabrasMasParecidas = $this->DemePalabrasMasParecidas($PalabrasBusqueda);

        $Consulta = "";
        $Consulta = $Consulta . "(";
        $Consulta = $Consulta . "     SELECT c.IdPalabra";
        $Consulta = $Consulta . "     FROM Palabras c";
        $Consulta = $Consulta . "     WHERE (1 = 0";

        $TiposParametros = "";
        $ArregloParametros = [];

        for($NumConsultasPalabras = 0; $NumConsultasPalabras < 3; $NumConsultasPalabras++)
        {
            for($i = 0; $i < count($PalabrasMasParecidas); $i++)
            {
                $ArregloParametros[] = $PalabrasMasParecidas[$i];
                $TiposParametros = $TiposParametros . "i";

                if ($NumConsultasPalabras == 0)
                    $Consulta = $Consulta . " OR c.IdPalabraSemejante = ?";
            } // for($i = 0; $i < count($PalabrasMasParecidas); $i++)
        } // for($NumConsultasPalabras = 0; $NumConsultasPalabras < 3; $NumConsultasPalabras++)

        $Consulta = $Consulta . ")";
        $Consulta = $Consulta . ")";

        $ConsultaPalabras = $Consulta;
        $Consulta = "";

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdCanton, a.Canton, c.Provincia, d.Region, 'C' as Tipo";
        $Consulta = $Consulta . " FROM Cantones a, PalabrasXCanton b, Provincias c, RegionesGeograficas d";
        $Consulta = $Consulta . " WHERE a.IdCanton = b.IdCanton";
        $Consulta = $Consulta . " AND a.IdProvincia = c.IdProvincia";
        $Consulta = $Consulta . " AND a.IdRegionGeografica = d.IdRegion";
        $Consulta = $Consulta . " AND b.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdCanton, a.Canton, c.Provincia, d.Region, Tipo";

        $Consulta = $Consulta . " UNION ";

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdCanton, a.Canton, c.Provincia, d.Region, 'P' as Tipo";
        $Consulta = $Consulta . " FROM Cantones a, PalabrasXProvincia b, Provincias c, RegionesGeograficas d";
        $Consulta = $Consulta . " WHERE a.IdProvincia = b.IdProvincia";
        $Consulta = $Consulta . " AND a.IdProvincia = c.IdProvincia";
        $Consulta = $Consulta . " AND a.IdRegionGeografica = d.IdRegion";
        $Consulta = $Consulta . " AND b.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdCanton, a.Canton, c.Provincia, d.Region, Tipo";

        $Consulta = $Consulta . " UNION ";

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdCanton, a.Canton, c.Provincia, d.Region, 'R' as Tipo";
        $Consulta = $Consulta . " FROM Cantones a, PalabrasXRegionGeografica b, Provincias c, RegionesGeograficas d";
        $Consulta = $Consulta . " WHERE a.IdRegionGeografica = b.IdRegion";
        $Consulta = $Consulta . " AND a.IdProvincia = c.IdProvincia";
        $Consulta = $Consulta . " AND a.IdRegionGeografica = d.IdRegion";
        $Consulta = $Consulta . " AND b.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdCanton, a.Canton, c.Provincia, d.Region, Tipo";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

        if ($ConsultaEjecutadaExitosamente)
        {
            include_once "CGroupByCantidad.php";
            $GroupBy = new CGroupByCantidad();
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $NumAciertos = $ResultadoConsulta[0];
                $IdCanton = $ResultadoConsulta[1];
                $Canton = $ResultadoConsulta[2];
                $Provincia = $ResultadoConsulta[3];
                $Region = $ResultadoConsulta[4];

                $GroupBy->AgregarTupla(array($IdCanton, $Canton, $Provincia, $Region), array(0), array(0,1,2,3), $NumAciertos);
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $TamanoCampo = floor(log10($GroupBy->DemeMaximaCantidad())) + 1;
            $Retorno = $GroupBy->OrdenarTuplas(array(array(4,'i',$TamanoCampo,'desc',$GroupBy->DemeMaximaCantidad()), array(1, 's', self::MAXIMO_TAMANO_CAMPO_CANTON)));
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function ConsultarXTodosCantones($PalabrasBusqueda)

    public function AltaCanton($Canton, $IdRegionGeografica, $IdProvincia, &$NumError, &$IdCanton)
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL AltaCanton(?, ?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'siisssss', array($Canton, $IdRegionGeografica, $IdProvincia, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdCanton = $ResultadoConsulta[1];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaCanton($Canton, $IdRegionGeografica, $IdProvincia, &$NumError, &$IdCanton)

    public function CambioCanton($IdCanton, $Canton, $IdRegionGeografica, $IdProvincia, &$NumError)
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL CambioCanton(?, ?, ?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'isiisssss', array($IdCanton, $Canton, $IdRegionGeografica, $IdProvincia, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $NumError = $ResultadoConsulta[0];

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioCanton($IdCanton, $Canton, $IdRegionGeografica, $IdProvincia, &$NumError)

    public function IndexarTodo()
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL IndexarTodosCantones(?, ?, ?, ?, ?, 0);";
        $this->EjecutarConsulta($Consulta, 'sssss', array(CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));
    } // public function IndexarTodo()

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class CCantones extends CSQL
?>
