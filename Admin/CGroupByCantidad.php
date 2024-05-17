<?php

class CGroupByCantidad
{
    private $MaximaCantidad = 0;
    private $Resultados = array();

    public function DemeMaximaCantidad()
    {
        return $this->MaximaCantidad;
    } // public function DemeMaximaCantidad()

    private function DemeTextoLlave($DatosLlave)
    {
        $TextoLlave = "";

        for($i = 0; $i < count($DatosLlave); $i++)
        {
            if ($i > 0)
                $TextoLlave = $TextoLlave . "_";

            $TextoLlave = $TextoLlave . $DatosLlave[$i];
        } // for($i = 0; $i < count($DatosLlave); $i++)

        return $TextoLlave;
    } // private function DemeTextoLlave($CamposLlave)

    public function AgregarTupla($ResultadoConsultaSQL, $CamposLlave, $CamposXTomar, $Cantidad)
    {
        $DatosLlave = [];

        for($i = 0; $i < count($CamposLlave); $i++)
        {
            $IndiceCampoLlave = $CamposLlave[$i];
            $DatosLlave[] = $ResultadoConsultaSQL[$IndiceCampoLlave];
        } // for($i = 0; $i < count($CamposLlave); $i++)

        $Llave = $this->DemeTextoLlave($DatosLlave);

        if (array_key_exists($Llave, $this->Resultados))
        {
            $IndiceCantidad = count($CamposXTomar);
            $Cantidad = $this->Resultados[$Llave][$IndiceCantidad] + $Cantidad;
            $this->Resultados[$Llave][$IndiceCantidad] = $Cantidad;
        } // if (array_key_exists($Llave, $this->Resultados))
        else
        {
            $ResultadoXAgregar = [];

            for($i = 0; $i < count($CamposXTomar); $i++)
            {
                $IndiceCampoXTomar = $CamposXTomar[$i];
                $ResultadoXAgregar[] = $ResultadoConsultaSQL[$IndiceCampoXTomar];
            } // for($i = 0; $i < count($CamposXTomar); $i++)

            $ResultadoXAgregar[] = $Cantidad;
            $this->Resultados[$Llave] = $ResultadoXAgregar;
        } // else

        if ($Cantidad > $this->MaximaCantidad)
            $this->MaximaCantidad = $Cantidad;

    } // public function AgregarTupla($ResultadoConsultaSQL, $CamposLlave, $CamposXTomar, $Cantidad)

    private function Rellenar($Texto, $CaracterRelleno, $Hasta, $AlInicio)
    {
        while(strlen($Texto) < $Hasta)
        {
            if ($AlInicio)
                $Texto = $CaracterRelleno . $Texto;
            else
                $Texto = $Texto . $CaracterRelleno;
        } // while(strlen($Texto) < $Hasta)

        return $Texto;
    } // private function Rellenar($Texto, $CaracterRelleno, $Hasta, $AlInicio)

    public function OrdenarTuplas($CamposLlave)
    {
        // Ordena ascendentemente los campos de texto. Los de número se pueden ordenar tanto ascendente como descendentemente.
        $ResultadosOrdenados = array();
        $LlavesEnResultados = array_keys($this->Resultados);

        for($i = 0; $i < count($this->Resultados); $i++)
        {
            $ArregloLlave = [];

            for($j = 0; $j < count($CamposLlave); $j++)
            {
                $IndiceLlave = $CamposLlave[$j][0];
                $TipoCampo = $CamposLlave[$j][1];
                $TamanoCampo = $CamposLlave[$j][2];
                $LlaveEnResultados = $LlavesEnResultados[$i];

                if ($TipoCampo == "s")
                    $TextoCampoLlave = $this->Rellenar($this->Resultados[$LlaveEnResultados][$IndiceLlave], ' ', $TamanoCampo, false);

                else if ($TipoCampo == "i")
                {
                    $Direccion = $CamposLlave[$j][3];

                    if ($Direccion == "asc")
                        $Valor = $this->Resultados[$LlaveEnResultados][$IndiceLlave];

                    else if ($Direccion == "desc")
                    {
                        $MaximoValor = $CamposLlave[$j][4];
                        $Valor = $MaximoValor - $this->Resultados[$LlaveEnResultados][$IndiceLlave];
                    } // else if ($Direccion == "desc")

                    $TextoCampoLlave = $this->Rellenar($Valor, '0', $TamanoCampo, true);
                } // else if ($TipoCampo == "i")

                $ArregloLlave[] = $TextoCampoLlave;
            } // for($j = 0; $j < count($CamposLlave); $j++)

            $Llave = $this->DemeTextoLlave($ArregloLlave);
            $ResultadosOrdenados = array_merge($ResultadosOrdenados, array($Llave=>$this->Resultados[$LlaveEnResultados]));
        } // for($i = 0; $i < count($this->Resultados); $i++)

        ksort($ResultadosOrdenados);

        $ResultadosXRetornar = [];
        foreach ($ResultadosOrdenados as $Clave => $Valor)
            $ResultadosXRetornar[] = $Valor;

        return $ResultadosXRetornar;
    } // public function OrdenarTuplas($CamposLlave)
} // class CGroupByCantidad
?>
