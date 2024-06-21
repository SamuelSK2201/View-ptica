<?
if($_SESSION['adm_verifica'] != "adm_option")
{
	session_destroy();
	
	msg("Para acessar esta area, e necessario estar logado no sistema.");
	redireciona("index.php");
}
?>