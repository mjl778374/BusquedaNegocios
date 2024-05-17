<?php
include "encabezados.php";
?>
<?php
include_once "CSession.php";
CSession::Inicializar();

include_once "CUsuarios.php";

if (isset($_POST["UsuarioLogin"]))
{
    include_once "CMensajes.php";
    $MensajeXDesglosar = "";
    try
    {
        $Usuarios = new CUsuarios();
        $Usuario = $_POST["UsuarioLogin"];
        $Contrasena = $_POST["ContrasenaLogin"];
        $Usuarios->ValidarLogin($Usuario, $Contrasena, $Existe, $IdUsuario);

        if (!$Existe)
            throw new Exception("Usuario o contraseña incorrecta.");

        CSession::ValidarInicioSesion($IdUsuario);
        header("Location: " . "main.php"); // Se redirecciona a la página principal de la aplicación
    } // try
    catch (Exception $e)
    {
        $MensajeXDesglosar = CMensajes::MensajeError($e->getMessage());
    } // catch (Exception $e)
} // if (isset($_POST["Usuario"]))
?>
<?php
$MaximoTamanoCampoUsuario = CUsuarios::MAXIMO_TAMANO_CAMPO_USUARIO;
$MaximoTamanoCampoContrasena = CUsuarios::MAXIMO_TAMANO_CAMPO_CONTRASENA;
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Usuario">Usuario</label>
                <input type="text" class="form-control" id="UsuarioLogin" name="UsuarioLogin" placeholder="Ingrese su usuario" maxlength="<?php echo $MaximoTamanoCampoUsuario;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Contrasena">Contraseña</label>
                <input type="password" class="form-control" id="ContrasenaLogin" name="ContrasenaLogin" placeholder="Ingrese su contraseña" maxlength="<?php echo $MaximoTamanoCampoContrasena;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </div>
    </div>
<?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
</form>
</body>
