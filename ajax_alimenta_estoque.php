<?php
session_start();
include("s_acessos.php");
include("funcoes.php");

$ind = anti_injection($_GET['ind']);
$codigo = anti_injection($_GET['codigo']);
$qtde = anti_injection($_GET['qtde']);

$table = 'estoques_grade_negativa';
if($ind == 2)
	$table = 'estoques_grade_positiva';

$strU = "UPDATE $table SET qtde = '$qtde' WHERE codigo = '$codigo'";
$rsU  = mysqli_query($conexao, $strU) or die(mysqli_error($conexao));
?>