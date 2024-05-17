<?php
include_once "CSQL.php";

class CProvincias extends CSQL
{
    public const MAXIMO_TAMANO_CAMPO_PROVINCIA = 50;

    function __construct()
    {
        parent::__construct();
    } // function __construct()

    public function ConsultarXProvincia($IdProvincia, &$Existe, &$Provincia)
    {
        $Consulta = "SELECT Provincia FROM Provincias WHERE IdProvincia = ?";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdProvincia));
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $Provincia = $ResultadoConsulta[0];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXProvincia($IdProvincia, &$Existe, &$Provincia)

    public function DemeTodasProvincias()
    {
        $Consulta = "SELECT IdProvincia, Provincia FROM Provincias ORDER BY Provincia ASC";
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
    } // public function DemeTodasProvincias()

    private function DemePalabrasMasParecidas($PalabrasBusqueda)
    {
        include_once "CPalabrasSemejantes.php";

        $PalabrasSemejantes = new CPalabrasSemejantes();
        $Retorno = $PalabrasSemejantes->DemePalabrasMasParecidas($PalabrasBusqueda, "PalabrasXProvincia");

        return $Retorno;
    } // private function DemePalabrasMasParecidas($PalabrasBusqueda)

    public function ConsultarXTodasProvincias($PalabrasBusqueda)
    {
        $Retorno = [];
        $PalabrasMasParecidas = $this->DemePalabrasMasParecidas($PalabrasBusqueda);

        $Consulta = "SELECT COUNT(1) as NumAciertos, a.IdProvincia, a.Provincia";
        $Consulta = $Consulta . " FROM Provincias a, PalabrasXProvincia b";
        $Consulta = $Consulta . " WHERE a.IdProvincia = b.IdProvincia";
        $Consulta = $Consulta . " AND b.IdPalabra IN (";
        $Consulta = $Consulta . "     SELECT c.IdPalabra";
        $Consulta = $Consulta . "     FROM Palabras c";
        $Consulta = $Consulta . "     WHERE (1 = 0";

        $TiposParametros = "";
        $ArregloParametros = [];

        for($i = 0; $i < count($PalabrasMasParecidas); $i++)
        {
            $ArregloParametros[] = $PalabrasMasParecidas[$i];
            $TiposParametros = $TiposParametros . "i";
            $Consulta = $Consulta . " OR c.IdPalabraSemejante = ?";
        } // for($i = 0; $i < count($PalabrasMasParecidas); $i++)

        $Consulta = $Consulta . ")";
        $Consulta = $Consulta . ")";
        $Consulta = $Consulta . " GROUP BY a.IdProvincia, a.Provincia";
        $Consulta = $Consulta . " ORDER BY NumAciertos DESC, a.Provincia ASC";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $IdProvincia = $ResultadoConsulta[1];
                $Provincia = $ResultadoConsulta[2];

                $Retorno[] = array($IdProvincia, $Provincia);
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function ConsultarXTodasProvincias($PalabrasBusqueda)

    public function AltaProvincia($Provincia, &$NumError, &$IdProvincia)
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL AltaProvincia(?, ?, ?, ?, ?, ?, 1);";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'ssssss', array($Provincia, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdProvincia = $ResultadoConsulta[1];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaProvincia($Provincia, &$NumError, &$IdProvincia)

    public function CambioProvincia($IdProvincia, $Provincia, &$NumError)
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL CambioProvincia(?, ?, ?, ?, ?, ?, ?, 1);";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'issssss', array($IdProvincia, $Provincia, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $NumError = $ResultadoConsulta[0];

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioProvincia($IdProvincia, $Provincia, &$NumError)

    public function IndexarTodo()
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL IndexarTodasProvincias(?, ?, ?, ?, ?, 0);";
        $this->EjecutarConsulta($Consulta, 'sssss', array(CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));
    } // public function IndexarTodo()

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class CProvincias extends CSQL
?>
