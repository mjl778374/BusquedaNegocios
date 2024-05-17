<?php
include_once "CSQL.php";

class CAliasYCategorias extends CSQL
{
    public const MAXIMO_TAMANO_CAMPO_ALIAS = 100;

    function __construct()
    {
        parent::__construct();
    } // function __construct()

    public function DemeTodosAlias($IdCategoria)
    {
	$Retorno = [];

        $Consulta = "SELECT a.IdAlias, a.Alias FROM Alias a, AliasCategoriasNegocios b WHERE b.IdCategoria = ? AND a.IdAlias = b.IdAlias AND a.EstaLibre = 0 AND NOT a.IdAlias IN (SELECT c.IdAlias FROM AliasPrincipalesXCategoriaNegocios c WHERE c.IdCategoria = b.IdCategoria) ORDER BY a.Alias ASC";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdCategoria));
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $IdAlias = $ResultadoConsulta[0];
                $Alias = $ResultadoConsulta[1];

                $Retorno[] = array($IdAlias, $Alias);
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function DemeTodosAlias($IdCategoria)

    public function DemeTodasCategorias($IdAlias)
    {
	$Retorno = [];

        $Consulta = "SELECT a.IdCategoria, b.Alias FROM AliasPrincipalesXCategoriaNegocios a, Alias b WHERE a.IdAlias = b.IdAlias AND b.EstaLibre = 0 AND a.IdCategoria IN (SELECT c.IdCategoria FROM AliasCategoriasNegocios c WHERE c.IdAlias = ?) ORDER BY b.Alias ASC";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdAlias));
        $Existe = false;

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

    public function DemeTodasCategorias2()
    {
	$Retorno = [];

        $Consulta = "SELECT a.IdCategoria, b.Alias FROM AliasPrincipalesXCategoriaNegocios a, Alias b WHERE a.IdAlias = b.IdAlias AND b.EstaLibre = 0 AND a.IdCategoria IN (SELECT c.IdCategoria FROM AliasCategoriasNegocios c) ORDER BY b.Alias ASC";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, '', array());
        $Existe = false;

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
    } // public function DemeTodasCategorias2()

    public function ConsultarXCategoria($IdCategoria, &$Existe, &$Categoria)
    {
        $Consulta = "SELECT Alias FROM Alias WHERE EstaLibre = 0 AND IdAlias IN (SELECT IdAlias FROM AliasPrincipalesXCategoriaNegocios WHERE IdCategoria = ?)";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdCategoria));
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $Categoria = $ResultadoConsulta[0];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXCategoria($IdCategoria, &$Existe, &$Categoria)

    public function ConsultarXAlias($IdAlias, $IdCategoria, &$Existe, &$Alias)
    {
        $Consulta = "SELECT a.Alias FROM Alias a, AliasCategoriasNegocios b WHERE a.IdAlias = b.IdAlias AND a.IdAlias = ? AND b.IdCategoria = ? AND a.EstaLibre = 0 AND NOT a.IdAlias IN (SELECT c.IdAlias FROM AliasPrincipalesXCategoriaNegocios c WHERE c.IdCategoria = b.IdCategoria)";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'ii', array($IdAlias, $IdCategoria));
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $Alias = $ResultadoConsulta[0];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXAlias($IdAlias, $IdCategoria, &$Existe, &$Alias)

    public function ConsultarXAlias2($IdAlias, &$Existe, &$Alias)
    {
        $Consulta = "SELECT Alias FROM Alias WHERE IdAlias = ? AND EstaLibre = 0 AND NOT IdAlias IN (SELECT IdAlias FROM AliasPrincipalesXCategoriaNegocios)";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdAlias));
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $Alias = $ResultadoConsulta[0];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXAlias2($IdAlias, &$Existe, &$Alias)

    public function ConsultarXAlias3($IdAlias, &$Existe, &$Alias)
    {
        $Consulta = "SELECT Alias FROM Alias WHERE IdAlias = ? AND EstaLibre = 0";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdAlias));
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $Alias = $ResultadoConsulta[0];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXAlias3($IdAlias, &$Existe, &$Alias)

    private function DemePalabrasMasParecidas($PalabrasBusqueda)
    {
        include_once "CPalabrasSemejantes.php";

        $PalabrasSemejantes = new CPalabrasSemejantes();
        $Retorno = $PalabrasSemejantes->DemePalabrasMasParecidas($PalabrasBusqueda, "PalabrasXAlias");

        return $Retorno;
    } // private function DemePalabrasMasParecidas($PalabrasBusqueda)

    public function ConsultarXTodosAliasYCategorias($PalabrasBusqueda)
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

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdAlias, a.Alias, b.IdCategoria, e.IdAlias as IdAliasPrincipalCategoria, e.Alias as AliasPrincipalCategoria, 'A' as Tipo";
        $Consulta = $Consulta . " FROM Alias a, AliasCategoriasNegocios b, CategoriasNegocios c, AliasPrincipalesXCategoriaNegocios d, Alias e, PalabrasXAlias f";
        $Consulta = $Consulta . " WHERE a.IdAlias = b.IdAlias";
        $Consulta = $Consulta . " AND b.IdCategoria = c.IdCategoria";
        $Consulta = $Consulta . " AND c.IdCategoria = d.IdCategoria";
        $Consulta = $Consulta . " AND d.IdAlias = e.IdAlias";
        $Consulta = $Consulta . " AND a.IdAlias = f.IdAlias";
        $Consulta = $Consulta . " AND a.EstaLibre = 0";
        $Consulta = $Consulta . " AND f.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdAlias, a.Alias, b.IdCategoria, IdAliasPrincipalCategoria, AliasPrincipalCategoria, Tipo";
        
        $Consulta = $Consulta . " UNION ";

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdAlias, a.Alias, b.IdCategoria, e.IdAlias as IdAliasPrincipalCategoria, e.Alias as AliasPrincipalCategoria, 'C' as Tipo";
        $Consulta = $Consulta . " FROM Alias a, AliasCategoriasNegocios b, CategoriasNegocios c, AliasPrincipalesXCategoriaNegocios d, Alias e, PalabrasXAlias f";
        $Consulta = $Consulta . " WHERE a.IdAlias = b.IdAlias";
        $Consulta = $Consulta . " AND b.IdCategoria = c.IdCategoria";
        $Consulta = $Consulta . " AND c.IdCategoria = d.IdCategoria";
        $Consulta = $Consulta . " AND d.IdAlias = e.IdAlias";
        $Consulta = $Consulta . " AND d.IdAlias = f.IdAlias";
        $Consulta = $Consulta . " AND a.EstaLibre = 0";
        $Consulta = $Consulta . " AND f.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdAlias, a.Alias, b.IdCategoria, IdAliasPrincipalCategoria, AliasPrincipalCategoria, Tipo";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

        if ($ConsultaEjecutadaExitosamente)
        {
            include_once "CGroupByCantidad.php";
            $GroupBy = new CGroupByCantidad();
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $NumAciertos = $ResultadoConsulta[0];
                $IdAlias = $ResultadoConsulta[1];
                $Alias = $ResultadoConsulta[2];
                $IdCategoria = $ResultadoConsulta[3];
                $IdAliasPrincipalCategoria = $ResultadoConsulta[4];
                $AliasPrincipalCategoria = $ResultadoConsulta[5];

                $GroupBy->AgregarTupla(array($IdAlias, $Alias, $IdCategoria, $IdAliasPrincipalCategoria, $AliasPrincipalCategoria), array(0,2), array(0,1,2,3,4), $NumAciertos);
                
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $TamanoCampo = floor(log10($GroupBy->DemeMaximaCantidad())) + 1;
            
            $Retorno = $GroupBy->OrdenarTuplas(array(array(5,'i',$TamanoCampo,'desc',$GroupBy->DemeMaximaCantidad()), array(1, 's', self::MAXIMO_TAMANO_CAMPO_ALIAS), array(4, 's', self::MAXIMO_TAMANO_CAMPO_ALIAS)));
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function ConsultarXTodosAliasYCategorias($PalabrasBusqueda)

    public function ConsultarXTodosAlias($PalabrasBusqueda)
    {
        // Esta consulta la hace el usuario que busca solo por los alias; no por categorías
        $Retorno = [];

        $PalabrasMasParecidas = $this->DemePalabrasMasParecidas($PalabrasBusqueda);

        $Consulta = "";
        $Consulta = $Consulta . "(";
        $Consulta = $Consulta . "     SELECT c.IdPalabra";
        $Consulta = $Consulta . "     FROM Palabras c";
        $Consulta = $Consulta . "     WHERE (1 = 0";

        $TiposParametros = "";
        $ArregloParametros = [];

        for($NumConsultasPalabras = 0; $NumConsultasPalabras < 1; $NumConsultasPalabras++)
        {
            for($i = 0; $i < count($PalabrasMasParecidas); $i++)
            {
                $ArregloParametros[] = $PalabrasMasParecidas[$i];
                $TiposParametros = $TiposParametros . "i";

                if ($NumConsultasPalabras == 0)
                    $Consulta = $Consulta . " OR c.IdPalabraSemejante = ?";
            } // for($i = 0; $i < count($PalabrasMasParecidas); $i++)
        } // for($NumConsultasPalabras = 0; $NumConsultasPalabras < 1; $NumConsultasPalabras++)

        $Consulta = $Consulta . ")";
        $Consulta = $Consulta . ")";

        $ConsultaPalabras = $Consulta;
        $Consulta = "";

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdAlias, a.Alias";
        $Consulta = $Consulta . " FROM Alias a, PalabrasXAlias b";
        $Consulta = $Consulta . " WHERE a.IdAlias = b.IdAlias";
        $Consulta = $Consulta . " AND a.EstaLibre = 0";
        $Consulta = $Consulta . " AND b.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdAlias, a.Alias";
        
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

        if ($ConsultaEjecutadaExitosamente)
        {
            include_once "CGroupByCantidad.php";
            $GroupBy = new CGroupByCantidad();
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $NumAciertos = $ResultadoConsulta[0];
                $IdAlias = $ResultadoConsulta[1];
                $Alias = $ResultadoConsulta[2];

                $GroupBy->AgregarTupla(array($IdAlias, $Alias), array(0), array(0,1), $NumAciertos);
                
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $TamanoCampo = floor(log10($GroupBy->DemeMaximaCantidad())) + 1;
            
            $Retorno = $GroupBy->OrdenarTuplas(array(array(2,'i',$TamanoCampo,'desc',$GroupBy->DemeMaximaCantidad()), array(1, 's', self::MAXIMO_TAMANO_CAMPO_ALIAS)));
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function ConsultarXTodosAlias($PalabrasBusqueda)

    public function AltaCategoria($Categoria, &$NumError, &$IdCategoria)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL AltaCategoria(?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'ssssss', array($Categoria, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdCategoria = $ResultadoConsulta[1];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaCategoria($Categoria, &$NumError, &$IdCategoria)

    public function CambioCategoria($IdCategoria, $Categoria, &$NumError)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL CambioCategoria(?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'issssss', array($IdCategoria, $Categoria, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $NumError = $ResultadoConsulta[0];

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioCategoria($IdCategoria, $Categoria, &$NumError)

    public function AltaAlias($Alias, $IdCategoria, &$NumError, &$IdAlias)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL AltaAlias(?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'sisssss', array($Alias, $IdCategoria, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));


        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdAlias = $ResultadoConsulta[1];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaAlias($Alias, $IdCategoria, &$NumError, &$IdAlias)

    public function AltaAlias2($IdAlias, $IdCategoria, &$NumError, &$Alias)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL AltaAlias2(?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'iisssss', array($IdAlias, $IdCategoria, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));


        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $Alias = $ResultadoConsulta[1];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaAlias2($IdAlias, $IdCategoria, &$NumError, &$Alias)

    public function CambioAlias($IdAlias, $Alias, $IdCategoria, $IdNuevaCategoria, &$NumError, &$IdNuevoAlias)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL CambioAlias(?, ?, ?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'isiisssss', array($IdAlias, $Alias, $IdCategoria, $IdNuevaCategoria, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));


        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdNuevoAlias = $ResultadoConsulta[1];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioAlias($Alias, $IdCategoria, &$NumError, &$IdAlias)

    public function CambioAlias2($IdAlias, $IdCategoria, $IdNuevaCategoria, &$NumError, &$Alias)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL CambioAlias2(?, ?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'iiisssss', array($IdAlias, $IdCategoria, $IdNuevaCategoria, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $Alias = $ResultadoConsulta[1];
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioAlias2($IdAlias, $IdCategoria, $IdNuevaCategoria, &$NumError, &$Alias)

    public function IndexarTodo()
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL IndexarTodosAlias(?, ?, ?, ?, ?, 0);";
        $this->EjecutarConsulta($Consulta, 'sssss', array(CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));
    } // public function IndexarTodo()

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class CAliasYCategorias extends CSQL
?>
