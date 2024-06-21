<?php
session_start();
include("s_acessos.php");
include("funcoes.php");

$idpedido = anti_injection($_GET['idpedido']);
$nr = anti_injection($_GET['nr']);

$str = "UPDATE pedidos_compras SET nr = '$nr' WHERE codigo = '$idpedido'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

echo $nr;