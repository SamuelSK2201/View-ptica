<?php
session_start();
include("s_acessos.php");
include("funcoes.php");

$idprospecao = anti_injection($_GET['idprospecao']);
$data_exame = anti_injection($_GET['data_exame']);

$str = "UPDATE prospeccao SET data_exame = '$data_exame' WHERE codigo = '$idprospecao'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

echo $data_exame;