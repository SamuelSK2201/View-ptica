<?
session_start();

include("s_acessos.php");
include("funcoes.php");
  
$dir = getcwd();    
    
$dir_upload = $dir . "/arquivos/";    
@mkdir($dir_upload, 0777);

$nome_arquivo = "EXCE_LENTE_58K_MULTI.csv";
$arquivo_upload = $dir_upload.$nome_arquivo;

echo $arquivo_upload.'<br>';

$arq = fopen($arquivo_upload, "r");
$i = 0;

while($vet = fgetcsv($arq, 300000, ";")) 
{
    print_r($vet);
    echo '<br>';

    if($i > 0 && !empty($vet[4]))
    {
        $lentes = utf8_encode($vet[0]);
        $pontos = $vet[1];
        //$status = $vet[2];
        $custo = str_replace(",", ".", str_replace("R$ ", "", $vet[3]));
        $laboratorio = $vet[4];

        $status = 2;
        if($vet[2] == 'ATIVO')
            $status = 1;

        $strE = "SELECT * FROM empresas ORDER BY razao_social";
        $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));

        while($vetE = mysqli_fetch_array($rsE))
        {
            $idempresa = $vetE['codigo'];

            echo $str = "SELECT * FROM laboratorios WHERE nome = '$laboratorio' AND idempresa = '$idempresa'";
            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
            $num = mysqli_num_rows($rs);
            $vet = mysqli_fetch_array($rs);
            echo '<br>';

            if(!$num)
            {
                echo $strL = "INSERT INTO laboratorios (idempresa, nome, import) VALUES ('$idempresa', '$laboratorio', '1')";
                $rsL  = mysqli_query($conexao, $strL) or die(mysqli_error($conexao));
                $idlaboratorio = mysqli_insert_id($conexao);
                echo '<br>';
            }
            else
            {
                $idlaboratorio = $vet['codigo'];
            }

            echo $str = "SELECT * FROM tipos_lentes WHERE nome = '$lentes' AND idempresa = '$idempresa' AND idlaboratorio = '$idlaboratorio'";
            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
            $num = mysqli_num_rows($rs);
            echo '<br>';

            if(!$num)
            {
                echo $strL = "INSERT INTO tipos_lentes (idempresa, nome, pontos, custo, idlaboratorio, status, import) VALUES ('$idempresa', '$lentes', '$pontos', '$custo', '$idlaboratorio', '$status', '1')";
                $rsL  = mysqli_query($conexao, $strL) or die(mysqli_error($conexao));
                echo '<br>';
            }
        }
    }

    $i++;
}

//ALTER TABLE `laboratorios` ADD `import` INT(1) NOT NULL DEFAULT '0' AFTER `numero`;
//ALTER TABLE `tipos_lentes` ADD `import` INT(1) NOT NULL DEFAULT '0' AFTER `status`;

//DELETE FROM laboratorios WHERE import = '1'
//DELETE FROM tipos_lentes WHERE import = '1'
?>