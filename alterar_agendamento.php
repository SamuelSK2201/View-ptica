<?
include("s_acessos.php");
include("funcoes.php");

$idagendamento = anti_injection($_POST['id_agendamento']); 
$data_hora_inicial =  anti_injection($_POST['data_hora_inicial']); 
$data_hora_final =  anti_injection($_POST['data_hora_final']); 

$str = "SELECT * FROM agendamentos WHERE codigo = '$idagendamento' LIMIT 1";
$rs = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$num = mysqli_num_rows($rs);
$vet = mysqli_fetch_array($rs);

$data_hora_inicial = explode(" ", $data_hora_inicial);
$hora_inicial = $data_hora_inicial[1];
$data = $data_hora_inicial[0];
$data_hora_final = explode(" ", $data_hora_final);
$hora_final = $data_hora_final[1];

if($num > 0)
{
    $strU = "UPDATE agendamentos SET data = '$data', hora_inicial = '$hora_inicial', hora_final = '$hora_final' WHERE codigo = '$idagendamento'";
    $rsU  = mysqli_query($conexao, $strU) or die(mysqli_error($conexao));
}
?>