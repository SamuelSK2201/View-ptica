<?
$page = 'pacientes';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_pacientes != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$nome = addslashes($_POST['nome']);
$data_nascimento = ConverteData($_POST['data_nascimento']);
$rg = $_POST['rg'];
$cpf = $_POST['cpf'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$telefone2 = $_POST['telefone2'];
$cep = $_POST['cep'];
$endereco = addslashes($_POST['endereco']);
$numero = $_POST['numero'];
$complemento = addslashes($_POST['complemento']);
$bairro = addslashes($_POST['bairro']);
$cidade = addslashes($_POST['cidade']);
$estado = $_POST['estado'];
$data_cadastro = ConverteData($_POST['data_cadastro']);

if($_POST['cmd'] == "upload")
{   
    $dir = getcwd();    
        
    $dir_upload = $dir . "/upload/";    
    @mkdir($dir_upload, 0777);
    
    $strpos = strpos($_FILES['arquivo']['name'], ".");
    $ext = substr($_FILES['arquivo']['name'], $strpos);

    $vet_ext = array(".csv"); 
    
    if(!in_array($ext, $vet_ext))
        redireciona("pacientes.php?ind_msg=5");

    $nome_arquivo = uniqid().$ext;    
    $arquivo_upload = $dir_upload.$nome_arquivo;

    move_uploaded_file($_FILES['arquivo']['tmp_name'], $arquivo_upload);

    $arq = fopen($arquivo_upload, "r");
    $i = 0;

    while($vet = fgetcsv($arq, 300000, ";")) 
    {
        if($i > 0)
        { 
            $nome = addslashes($vet[0]);
            $data_nascimento = ConverteData($vet[1]);
            $rg = $vet[2];
            $cpf = $vet[3];
            $email = $vet[4];
            $telefone = $vet[5];
            $telefone2 = $vet[6];
            $cep = $vet[7];
            $endereco = addslashes($vet[8]);
            $numero = $vet[9];
            $complemento = addslashes($vet[10]);
            $bairro = addslashes($vet[11]);
            $cidade = addslashes($vet[12]);
            $estado = $vet[13];

            if(!empty($nome))
            {
                //VERIFICA SE EXISTE PACIENTE
                $str = "SELECT * FROM pacientes WHERE idempresa = '$adm_empresa' AND cpf = '$cpf'";
                $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                $num = mysqli_num_rows($rs);

                if(!$num)
                {
                    $strI = "INSERT INTO pacientes (idempresa, nome, data_nascimento, rg, cpf, email, telefone, telefone2, cep, endereco, numero, complemento, bairro, cidade, estado, data_cadastro)
                        VALUES ('$adm_empresa', '$nome', '$data_nascimento', '$rg', '$cpf', '$email', '$telefone', '$telefone2', '$cep', '$endereco', '$numero', '$complemento', '$bairro', '$cidade', '$estado', CURDATE())";
                    $rsI  = mysqli_query($conexao, $strI) or die(mysqli_error($conexao));
                }
            }
        }

        $i++;
    }

    redireciona("pacientes.php?ind_msg=4");
}

if($_POST['cmd'] == "add")
{  
    $str = "INSERT INTO pacientes (idempresa, nome, data_nascimento, rg, cpf, email, telefone, telefone2, cep, endereco, numero, complemento, bairro, cidade, estado, data_cadastro)
        VALUES ('$adm_empresa', '$nome', '$data_nascimento', '$rg', '$cpf', '$email', '$telefone', '$telefone2', '$cep', '$endereco', '$numero', '$complemento', '$bairro', '$cidade', '$estado', '$data_cadastro')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
            
    redireciona("pacientes.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{    
    $str = "UPDATE pacientes 
        SET nome = '$nome', data_nascimento = '$data_nascimento', rg = '$rg', cpf = '$cpf', email = '$email', telefone = '$telefone', telefone2 = '$telefone2', cep = '$cep', 
        endereco = '$endereco', numero = '$numero', complemento = '$complemento', bairro = '$bairro', cidade = '$cidade', estado = '$estado', data_cadastro = '$data_cadastro'
        WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        
    redireciona("pacientes.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM pacientes WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("pacientes.php?ind_msg=3");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Paciente cadastrado com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Paciente editado com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Paciente excluído com sucesso!';
elseif($_GET['ind_msg'] == "4")
    $msg = 'Clientes(s) importado(s) com sucesso no sistema!';
elseif($_GET['ind_msg'] == "5")
    $msg = 'A extensão do arquivo de importação deve ser (.csv)!';

$str = "SELECT * FROM pacientes WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$title = "'View Óptica<br>Pacientes'";
$columns = '0, 1, 2, 3, 4, 5, 6, 7';
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
        <h2>Pacientes</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?
            if(!empty($_GET['ind_msg']) && $_GET['ind_msg'] <= 4)
            {
            ?>
            <p class="font-bold  alert alert-success m-b-sm">
                <?=$msg?>
            </p>
            <br>
            <?
            }

            if($_GET['ind_msg'] >= 5)
            {
            ?>
            <p class="font-bold  alert alert-danger m-b-sm">
                <?=$msg?>
            </p>
            <br>
            <?
            }
            ?>
            <div class="ibox float-e-margins">
                <div class="ibox-title">    
                    <h5>IMPORTAR ARQUIVO - <small><i>Clique na seta do lado direito para abrir ou ocultar o formulário</i></small></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" style="display: none;">
                    <form method="post" class="form-horizontal" name="form_u" id="form_u" enctype="multipart/form-data" action="pacientes.php">
                        <input type="hidden" name="cmd" value="upload">

                        <div class="alert alert-warning">
                            - Abaixo exemplo de arquivo csv utilizado para importação de pacientes em lote.<br><br>
                            <a href="arquivos/pacientes.csv" download class="btn btn-white btn-sm"><i class="fa fa-folder"></i> Ver </a>
                        </div>
                        
                        <div class="row">
                            <div class="col-xs-12"> 
                                <label for="arquivo">Upload de arquivo (csv):</label>
                                <input type="file" id="arquivo" name="arquivo" required>
                                <p class="help-block">Extensão permitida: csv.</p>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12"> 
                                <button type="submit" class="btn btn-primary" onclick="javascript: if(!confirm('Aguarde, esta operação pode demorar alguns minutos.')) { return false }">Cadastrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">    
                    <h5>CADASTRO MANUAL - <small><i>Clique na seta do lado direito para abrir ou ocultar o formulário</i></small></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" <?=($ind == 2) ? 'style="display: block;"' : 'style="display: none;"'?>>
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd">  
                        <input type="hidden" name="codigo" id="codigo" value="<?=$vet['codigo']?>">

                        <div class="row">
                            <div class="col-md-6">
                                <p class="font-bold" >
                                    Nome*
                                </p>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?=stripslashes($vet['nome'])?>" required>
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Nascimento*
                                </p>
                                <input class="form-control" type="text" name="data_nascimento" id="data_nascimento" value="<?=($vet['data_nascimento']) ? ConverteData($vet['data_nascimento']) : ''?>" required maxlength="10" data-mask="99/99/9999" onKeyUp="javascript: return auto_data('data_nascimento');" onKeyPress="javascript: return somenteNumeros(event);">
                            </div> 
                            <div class="col-md-2">
                                <p class="font-bold">
                                    CPF
                                </p>
                                <input class="form-control" type="text" name="cpf" id="cpf" value="<?=$vet['cpf']?>" maxlength="14" data-mask="999.999.999-99" onKeyUp="javascript: return auto_cpf('cpf');" onKeyPress="javascript: return somenteNumeros(event);">
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    RG
                                </p>
                                <input class="form-control" type="text" name="rg" id="rg" value="<?=$vet['rg']?>" >
                            </div>
                        </div>
                        <br>
                        <div class="row">  
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Telefone 1
                                </p>
                                <input class="form-control" type="text" name="telefone" id="telefone" value="<?=$vet['telefone']?>" maxlength="15" onKeyUp="javascript: mascara(this, mtel);" onKeyPress="javascript: return somenteNumeros(event);" >
                            </div> 
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Telefone 2
                                </p>
                                <input class="form-control" type="text" name="telefone2" id="telefone2" value="<?=$vet['telefone2']?>" maxlength="15" onKeyUp="javascript: mascara(this, mtel);" onKeyPress="javascript: return somenteNumeros(event);" >
                            </div> 
                            <div class="col-md-6">
                                <p class="font-bold">
                                    Email
                                </p>
                                <input class="form-control" type="email" name="email" id="email" value="<?=$vet['email']?>" >
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
                                <input class="form-control" type="text" name="numero" id="numero" value="<?=$vet['numero']?>" >
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
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Bairro
                                </p>
                                <input class="form-control" type="text" name="bairro" id="bairro" value="<?=stripslashes($vet['bairro'])?>" >
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Data de cadastro*
                                </p>
                                <input class="form-control" type="text" name="data_cadastro" id="data_cadastro" value="<?=($vet['data_cadastro']) ? ConverteData($vet['data_cadastro']) : date("d/m/Y")?>" required maxlength="10" data-mask="99/99/9999" onKeyUp="javascript: return auto_data('data_cadastro');" onKeyPress="javascript: return somenteNumeros(event);">
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
    $str = "SELECT DISTINCT A.codigo, A.nome, A.data_nascimento, A.estado, A.cidade, A.telefone, A.telefone2, A.data_cadastro,
        (SELECT B.data FROM agendamentos B WHERE B.idempresa = '$adm_empresa' AND B.idpaciente = A.codigo AND B.status = '6' $strWhereP ORDER BY B.data DESC LIMIT 1) AS ultima_consulta
        FROM pacientes A
        WHERE A.idempresa = '$adm_empresa' 
        ORDER BY A.nome";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de pacientes cadastrados no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Nascimento</th>
                                    <th>Estado</th>
                                    <th>Cidade</th>
                                    <th>Telefone 1</th>
                                    <th>Telefone 2</th>
                                    <th>Última consulta</th>
                                    <th>Data de cadastro</th>
                                    <th style="width: 15%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=($vetC['data_nascimento']) ? ConverteData($vet['data_nascimento']) : '-'?></td>    
                                    <td><?=$vet['estado']?></td>
                                    <td><?=$vet['cidade']?></td>  
                                    <td><a href="https://api.whatsapp.com/send?phone=55<?=preg_replace('/[^\d]/', '', $vet['telefone'])?>" target="_blank"><?=$vet['telefone']?></a></td>
                                    <td><a href="https://api.whatsapp.com/send?phone=55<?=preg_replace('/[^\d]/', '', $vet['telefone2'])?>" target="_blank"><?=$vet['telefone2']?></a></td>
                                    <td><?=($vet['ultima_consulta']) ? ConverteData($vet['ultima_consulta']) : '-'?></td>    
                                    <td><?=ConverteData($vet['data_cadastro'])?></td>  
                                    <td class="center">
                                        <a class="btn btn-default btn-circle" type="button" title="histórico" href="pacientes_historico.php?idpaciente=<?=$vet['codigo']?>" target="_blank"><i class="fa fa-user"></i></a>
                                        <a class="btn btn-info btn-circle" type="button" title="agendar" href="agendamentos.php?idpaciente=<?=$vet['codigo']?>"><i class="fa fa-calendar"></i></a>
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="pacientes.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="pacientes.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nome</th>
                                    <th>Nascimento</th>
                                    <th>Estado</th>
                                    <th>Cidade</th>
                                    <th>Telefone 1</th>
                                    <th>Telefone 2</th>
                                    <th>Última consulta</th>
                                    <th>Data de cadastro</th>
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