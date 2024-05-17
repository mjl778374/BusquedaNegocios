<?php
class CMensajes
{
    public static function MensajeError($Mensaje)
    {
        return '<div class="row justify-content-center alert alert-danger" role="alert">' . htmlspecialchars($Mensaje) . '</div>';
    } // public static function MensajeError($Mensaje)

    public static function MensajeOK($Mensaje)
    {
        return '<div class="row justify-content-center alert alert-success" role="alert">' . htmlspecialchars($Mensaje) . '</div>';
    } // public static function MensajeOK($Mensaje)
} // class CMensajes
?>

