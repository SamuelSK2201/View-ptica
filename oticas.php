<?
$page = 'oticas';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_oticas != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$nome = addslashes($_POST['nome']);
$cnpj = $_POST['cnpj'];
$responsavel = addslashes($_POST['responsavel']);
$contato = $_POST['contato'];
$cep = $_POST['cep'];
$endereco = addslashes($_POST['endereco']);
$numero = $_POST['numero'];
$complemento = addslashes($_POST['complemento']);
$bairro = addslashes($_POST['bairro']);
$cidade = addslashes($_POST['cidade']);
$estado = $_POST['estado'];

if($_POST['cmd'] == "add")
{   
    $strU = "SELECT * FROM oticas WHERE cnpj = '$cnpj'";
    $rsU  = mysqli_query($conexao, $strU) or die(mysqli_error($conexao));
    $numU = mysqli_num_rows($rsU);

    if($numU > 0)
        redireciona("oticas.php?ind_msg=4");

    $str = "INSERT INTO oticas (idempresa, nome, cnpj, responsavel, contato, cep, endereco, numero, complemento, bairro, cidade, estado)
        VALUES ('$adm_empresa', '$nome', '$cnpj', '$responsavel', '$contato', '$cep', '$endereco', '$numero', '$complemento', '$bairro', '$cidade', '$estado')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
            
    redireciona("oticas.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{
    $strU = "SELECT * FROM oticas WHERE cnpj = '$cnpj' AND codigo != '$codigo'";
    $rsU  = mysqli_query($conexao, $strU) or die(mysqli_error($conexao));
    $numU = mysqli_num_rows($rsU);

    if($numU > 0)
        redireciona("oticas.php?ind_msg=4");
    
    $str = "UPDATE oticas 
        SET nome = '$nome', cnpj = '$cnpj', responsavel = '$responsavel', contato = '$contato', cep = '$cep', endereco = '$endereco', numero = '$numero', 
        complemento = '$complemento', bairro = '$bairro', cidade = '$cidade', estado = '$estado'
        WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        
    redireciona("oticas.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM oticas WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("oticas.php?ind_msg=3");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Ótica parceira cadastrada com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Ótica parceira editada com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Ótica parceira excluída com sucesso!';
elseif($_GET['ind_msg'] == "4")
    $msg = 'CNPJ já cadastrado no sistema!';

$str = "SELECT * FROM oticas WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$title = "'View Óptica<br>Óticas parceiras'";
$columns = '0, 1, 2, 3, 4';
$order = ',order: [[ 0, "asc" ]]';
?>

<script language="javascript">
function valida(ind)
{   
    if(ind == 1)
        document.form.cmd.value = "add";
    else
        document.form.cmd.value = "edit";
}
</script>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Óticas parceiras</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?
            if(!empty($_GET['ind_msg']))
            {
            ?>
            <p class="font-bold  alert alert-success m-b-sm">
                <?=$msg?>
            </p>
            <br>
            <?
            }
            ?>
            <div class="ibox float-e-margins">
                <div class="ibox-title">    
                    <h5><i>Clique na seta do lado direito para abrir ou ocultar o formulário</i></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" <?=($ind == 2) ? 'style="display: block;"' : 'style="display: none;"'?>>
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" value="edit">  
                        <input type="hidden" name="codigo" id="codigo" value="<?=$vet['codigo']?>">

                        <div class="row">
                            <div class="col-md-4">
                                <p class="font-bold" >
                                    Nome*
                                </p>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?=stripslashes($vet['nome'])?>" required>
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    CNPJ*
                                </p>
                                <input class="form-control" type="text" name="cnpj" id="cnpj" value="<?=$vet['cnpj']?>" required maxlength="18" data-mask="99.999.999/9999-99" onKeyUp="javascript: return auto_cnpj('cnpj');" onKeyPress="javascript: return somenteNumeros(event);">
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold" >
                                    Responsável
                                </p>
                                <input type="text" name="responsavel" id="responsavel" class="form-control" value="<?=stripslashes($vet['responsavel'])?>" >
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Contato
                                </p>
                                <input class="form-control" type="text" name="contato" id="contato" value="<?=$vet['contato']?>" maxlength="15" onKeyUp="javascript: mascara(this, mtel);" onKeyPress="javascript: return somenteNumeros(event);" >
                            </div> 
                        </div>
                        <br>
                        <div class="row">                            
                            <div class="col-md-2">
                                <p class="font-bold">
                                    CEP
                                </p>
                                <input class="form-control" type="text" name="cep" id="cep" value="<?=$vet['cep']?>" maxlength="9" data-mask="99999-999" onKeyUp="javascript: return auto_cep('cep');" onKeyPress="javascript: return somenteNumeros(event);">
                            </div>  
                            <div class="col-md-6">
                                <p class="font-bold">
                                    Endereço
                                </p>
                                <input class="form-control" type="text" name="endereco" id="endereco" value="<?=stripslashes($vet['endereco'])?>" >
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Número
                                </p>
                                <input class="form-control" type="text" name="numero" id="numero" value="<?=stripslashes($vet['numero'])?>" >
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Complemento
                                </p>
                                <input class="form-control" type="text" name="complemento" id="complemento" value="<?=stripslashes($vet['complemento'])?>" >
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-5">
                                <p class="font-bold">
                                    Bairro
                                </p>
                                <input class="form-control" type="text" name="bairro" id="bairro" value="<?=stripslashes($vet['bairro'])?>" >
                            </div>
                            <div class="col-md-5">
                                <p class="font-bold">
                                    Cidade
                                </p>
                                <input class="form-control" type="text" name="cidade" id="cidade" value="<?=stripslashes($vet['cidade'])?>" >
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Estado
                                </p>
                                <input class="form-control" type="text" name="estado" id="estado" value="<?=$vet['estado']?>" >
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12"> 
                                <?
                                if($ind == 1)
                                {
                                ?>
                                <button type="submit" class="btn btn-primary" onClick="javascript: valida(<?=$ind?>);">Cadastrar</button>
                                <?
                                }
                                else
                                {
                                ?>
                                <button type="submit" class="btn btn-primary" onClick="javascript: valida(<?=$ind?>);">Alterar</button>
                                <?
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?
    $str = "SELECT * FROM oticas WHERE idempresa = '$adm_empresa' ORDER BY nome";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de óticas parceiras cadastradas no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Responsável</th>
                                    <th>Contato</th>
                                    <th>Estado</th>
                                    <th>Cidade</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=stripslashes($vet['responsavel'])?></td>      
                                    <td><?=$vet['contato']?></td>
                                    <td><?=$vet['estado']?></td>
                                    <td><?=$vet['cidade']?></td>      
                                    <td class="center">
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="oticas.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="oticas.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nome</th>
                                    <th>Responsável</th>
                                    <th>Contato</th>
                                    <th>Estado</th>
                                    <th>Cidade</th>
                                    <th>Ações</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?
    }
    else
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <p class="font-bold  alert alert-danger m-b-sm">
                Nenhum paciente encontrado no sistema.
            </p>
        </div>
    </div>
    <?
    }
    ?>
</div>
<?
include("includes/footer.php");
?>