<?php
session_start();

class CSession
{
    public static function Inicializar()
    {
        $_SESSION["IdUsuario"] = NULL;
    } // public static function Inicializar()

    public static function ValidarInicioSesion($IdUsuario)
    {
        $_SESSION["IdUsuario"] = $IdUsuario;
        $_SESSION["HoraSegundosUltimoUsoSesion"] = time();
    } // public static function ValidarInicioSesion($IdUsuario)

    public static function UsuarioIngresoOK()
    {
        include "constantesApp.php";
        
        if (isset($_SESSION["IdUsuario"]) && $_SESSION["IdUsuario"] != NULL && self::TiempoTranscurridoSegundosDesdeUltimoUsoSesion() <= $MAX_TIEMPO_TRANSCURRIDO_SEGUNDOS_DESDE_ULTIMO_USO_SESION)
            return true;
        else
            return false;
    } // public static function UsuarioIngresoOK()

    private static function TiempoTranscurridoSegundosDesdeUltimoUsoSesion()
    {
        $TiempoTranscurridoSegundos = time() - $_SESSION["HoraSegundosUltimoUsoSesion"];
        $_SESSION["HoraSegundosUltimoUsoSesion"] = time();
        return $TiempoTranscurridoSegundos;
    } // private static function TiempoTranscurridoSegundosDesdeUltimoUsoSesion()

    public static function DemeIdUsuario()
    {
        return $_SESSION["IdUsuario"];
    } // public static function FijarIdUsuario($IdUsuario)

    public static function UsuarioSesionEsAdministrador()
    {
        include_once "CUsuarios.php";

        if (!self::UsuarioIngresoOK())
            return false;

        else
        {
            $IdUsuario = self::DemeIdUsuario();
            $Usuarios = new CUsuarios();
            $Usuarios->ConsultarXUsuario($IdUsuario, $Existe, $Usuario, $Cedula, $Nombre, $EsAdministrador);

            if (!$Existe)
                return false;
            elseif ($EsAdministrador == 0)
                return false;
            else
                return true;
        } // else
    } // public static function UsuarioSesionEsAdministrador()

    public static function DemeNombreUsuario()
    {
        include_once "CUsuarios.php";

        if (!self::UsuarioIngresoOK())
            return "";

        else
        {
            $IdUsuario = self::DemeIdUsuario();
            $Usuarios = new CUsuarios();
            $Usuarios->ConsultarXUsuario($IdUsuario, $Existe, $Usuario, $Cedula, $Nombre, $EsAdministrador);

            if (!$Existe)
                return "";
            else
                return $Nombre;
        } // else
    } // public static function DemeNombreUsuario()
} // class CSession
?>
