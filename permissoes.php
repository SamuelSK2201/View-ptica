<?
$menu = 'configuracoes';
$page = 'usuarios_tipos';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_usuarios_tipos != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$nome = addslashes($_POST['nome']);
$agenda = $_POST['agenda'];
$consultas = $_POST['consultas'];
$gerenciar_prescricao = $_POST['gerenciar_prescricao'];
$pacientes = $_POST['pacientes'];
$oticas = $_POST['oticas'];
$procedimentos = $_POST['procedimentos'];
$modelos = $_POST['modelos'];
$tipos_lentes = $_POST['tipos_lentes'];
$laboratorios = $_POST['laboratorios'];
$prospeccao = $_POST['prospeccao'];
$pesquisa_obrigatoriedade = $_POST['pesquisa_obrigatoriedade'];
$relatorios = $_POST['relatorios'];
$usuarios_tipos = $_POST['usuarios_tipos'];
$usuarios = $_POST['usuarios'];
$ajustes = $_POST['ajustes'];

$estoque = $_POST['estoque'];
$downloads = $_POST['downloads'];

$relatorios_atendidos = $_POST['relatorios_atendidos'];
$relatorios_vencidas = $_POST['relatorios_vencidas'];
$relatorios_financeiro = $_POST['relatorios_financeiro'];
$relatorios_consultas = $_POST['relatorios_consultas'];
$relatorios_pesquisadores = $_POST['relatorios_pesquisadores'];
$relatorios_aniversariantes = $_POST['relatorios_aniversariantes'];
$relatorios_pontuacoes = $_POST['relatorios_pontuacoes'];
$relatorios_faturamento = $_POST['relatorios_faturamento'];

$relatorios_aguardando = $_POST['relatorios_aguardando'];
$relatorios_reagendar = $_POST['relatorios_reagendar'];
$relatorios_reservados = $_POST['relatorios_reservados'];
$relatorios_finalizados = $_POST['relatorios_finalizados'];
$relatorios_atendido = $_POST['relatorios_atendido'];
$relatorios_pedidos = $_POST['relatorios_pedidos'];

if($_POST['cmd'] == "add")
{   
    $str = "INSERT INTO usuarios_tipos (idempresa, nome, agenda, consultas, pacientes, oticas, procedimentos, modelos, tipos_lentes, laboratorios, prospeccao, gerenciar_prescricao, pesquisa_obrigatoriedade, relatorios, relatorios_atendidos,
        relatorios_vencidas, relatorios_financeiro, relatorios_consultas, relatorios_reservados, relatorios_aguardando, relatorios_reagendar, relatorios_finalizados, relatorios_atendido, relatorios_pedidos, relatorios_pesquisadores, 
        relatorios_aniversariantes, relatorios_pontuacoes, relatorios_faturamento, usuarios_tipos, usuarios, ajustes, estoque, downloads)
        VALUES ('$adm_empresa', '$nome', '$agenda', '$consultas', '$pacientes', '$oticas', '$procedimentos', '$modelos', '$tipos_lentes', '$laboratorios', '$prospeccao', '$gerenciar_prescricao', '$pesquisa_obrigatoriedade', '$relatorios',
        '$relatorios_atendidos', '$relatorios_vencidas', '$relatorios_financeiro', '$relatorios_consultas', '$relatorios_reservados', '$relatorios_aguardando', '$relatorios_reagendar', '$relatorios_finalizados', '$relatorios_atendido', '$relatorios_pedidos', 
        '$relatorios_pesquisadores', '$relatorios_aniversariantes', '$relatorios_pontuacoes', '$relatorios_faturamento', '$usuarios_tipos', '$usuarios', '$ajustes', '$estoque', '$downloads')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("permissoes.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{    
    $str = "UPDATE usuarios_tipos 
        SET nome = '$nome', agenda = '$agenda', consultas = '$consultas', pacientes = '$pacientes', oticas = '$oticas', procedimentos = '$procedimentos', modelos = '$modelos', tipos_lentes = '$tipos_lentes', 
        laboratorios = '$laboratorios', prospeccao = '$prospeccao', gerenciar_prescricao = '$gerenciar_prescricao', pesquisa_obrigatoriedade = '$pesquisa_obrigatoriedade', relatorios = '$relatorios',
        relatorios_atendidos = '$relatorios_atendidos',  relatorios_vencidas = '$relatorios_vencidas', relatorios_financeiro = '$relatorios_financeiro', relatorios_consultas = '$relatorios_consultas',
        relatorios_reservados = '$relatorios_reservados', relatorios_aguardando = '$relatorios_aguardando', relatorios_reagendar = '$relatorios_reagendar', relatorios_finalizados = '$relatorios_finalizados',
        relatorios_atendido = '$relatorios_atendido', relatorios_pedidos = '$relatorios_pedidos', relatorios_pesquisadores = '$relatorios_pesquisadores', relatorios_aniversariantes = '$relatorios_aniversariantes',
        relatorios_pontuacoes = '$relatorios_pontuacoes', relatorios_faturamento = '$relatorios_faturamento', usuarios_tipos = '$usuarios_tipos', usuarios = '$usuarios', ajustes = '$ajustes', estoque = '$estoque', downloads = '$downloads'
        WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("permissoes.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $strC = "SELECT A.codigo 
        FROM usuarios A
        INNER JOIN usuarios_tipos B ON A.idtipo = B.codigo
        WHERE A.idtipo = '$codigo'";
    $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));
    $numC = mysqli_num_rows($rsC);

    if(!$numC)
    {
        $str = "DELETE FROM usuarios_tipos WHERE codigo = '$codigo'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        
        redireciona("permissoes.php?ind_msg=3");
    }
    
    redireciona("permissoes.php?ind_msg=4");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Permissão cadastrada com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Permissão editada com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Permissão excluída com sucesso!';
elseif($_GET['ind_msg'] == "4")
    $msg = 'Permissão não pode ser excluída!<br>Existem usuários associados a ela.';

$str = "SELECT * FROM usuarios_tipos WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);
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
        <h2>Permissões</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?
            if(!empty($_GET['ind_msg']) && $_GET['ind_msg'] != 4)
            {
            ?>
            <p class="font-bold  alert alert-success m-b-sm">
                <?=$msg?>
            </p>
            <br>
            <?
            }

            if($_GET['ind_msg'] == 4)
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
                    <h5><i>Clique na seta do lado direito para abrir ou ocultar o formulário</i></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" <?=($ind == 2) ? 'style="display: block;"' : 'style="display: none;"'?>>
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" >  
                        <input type="hidden" name="codigo" id="codigo" value="<?=$vet['codigo']?>" >

                        <div class="row">
                            <div class="col-md-6">
                                <p class="font-bold">
                                    Nome*<br>
                                    <input type="checkbox" value="1" onclick="javascript: selecionaTodos()" name="tall" id="tall" > <small>Selecionar todos as funcionalidades</small>
                                </p>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?=stripslashes($vet['nome'])?>" required>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="agenda" id="agenda" <?=($vet['agenda'] == 1) ? 'checked' : ''?>> Agenda                                
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="consultas" id="consultas" <?=($vet['consultas'] == 1) ? 'checked' : ''?>> Consultas   
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="gerenciar_prescricao" id="gerenciar_prescricao" <?=($vet['gerenciar_prescricao'] == 1) ? 'checked' : ''?>> Gerenciar prescrição                                
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="pacientes" id="pacientes" <?=($vet['pacientes'] == 1) ? 'checked' : ''?>> Pacientes   
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="oticas" id="oticas" <?=($vet['oticas'] == 1) ? 'checked' : ''?>> Óticas parceiras   
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="procedimentos" id="procedimentos" <?=($vet['procedimentos'] == 1) ? 'checked' : ''?>> Procedimentos  
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="tipos_lentes" id="tipos_lentes" <?=($vet['tipos_lentes'] == 1) ? 'checked' : ''?>> Tipos de lentes  
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="laboratorios" id="laboratorios" <?=($vet['laboratorios'] == 1) ? 'checked' : ''?>> Laboratórios
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="prospeccao" id="prospeccao" <?=($vet['prospeccao'] == 1) ? 'checked' : ''?>> Pesquisa de prospecção                                
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="pesquisa_obrigatoriedade" id="pesquisa_obrigatoriedade" <?=($vet['pesquisa_obrigatoriedade'] == 1) ? 'checked' : ''?>> Obrigado a responder perguntas                               
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" onclick="javascript: selecionaTodosRelatorios()" name="relatorios" id="relatorios" <?=($vet['relatorios'] == 1) ? 'checked' : ''?>> Relatórios                                
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="usuarios_tipos" id="usuarios_tipos" <?=($vet['usuarios_tipos'] == 1) ? 'checked' : ''?>> Permissões                       
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="usuarios" id="usuarios" <?=($vet['usuarios'] == 1) ? 'checked' : ''?>> Usuários
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="ajustes" id="ajustes" <?=($vet['ajustes'] == 1) ? 'checked' : ''?>> Ajustes                       
                            </div>
                        </div>
                        <br>
                        <p class="font-bold  alert alert-info m-b-sm">
                            ESTOQUE
                        </p>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="radio" value="1" name="estoque" id="estoque" <?=($vet['estoque'] == 1) ? 'checked' : ''?>> Gerenciar (adicionar / editar / excluir)<br>
                                <input type="radio" value="2" name="estoque" id="estoque" <?=($vet['estoque'] == 2) ? 'checked' : ''?>> Apenas visualizar
                                <input type="radio" value="0" name="estoque" id="estoque" <?=(!$vet['estoque']) ? 'checked' : ''?>> Sem acesso ao estoque
                            </div>
                        </div>
                        <br>
                        <p class="font-bold  alert alert-info m-b-sm">
                            RELATÓRIOS
                        </p>
                        <br>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_atendidos" id="relatorios_atendidos" <?=($vet['relatorios_atendidos'] == 1) ? 'checked' : ''?>> Atendidos hoje
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_vencidas" id="relatorios_vencidas" <?=($vet['relatorios_vencidas'] == 1) ? 'checked' : ''?>> Consultas vencidas                               
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_financeiro" id="relatorios_financeiro" <?=($vet['relatorios_financeiro'] == 1) ? 'checked' : ''?>> Relatório financeiro                               
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_consultas" id="relatorios_consultas" <?=($vet['relatorios_consultas'] == 1) ? 'checked' : ''?>> Relatório consultas                                
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_pesquisadores" id="relatorios_pesquisadores" <?=($vet['relatorios_pesquisadores'] == 1) ? 'checked' : ''?>> Pesquisadores x Pesquisas                                
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_aniversariantes" id="relatorios_aniversariantes" <?=($vet['relatorios_aniversariantes'] == 1) ? 'checked' : ''?>> Aniversariantes                                
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_pontuacoes" id="relatorios_pontuacoes" <?=($vet['relatorios_pontuacoes'] == 1) ? 'checked' : ''?>> Pontuações
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_faturamento" id="relatorios_faturamento" <?=($vet['relatorios_faturamento'] == 1) ? 'checked' : ''?>> Faturamento
                            </div>
                        </div>
                        <br>
                        <p class="font-bold  alert alert-info m-b-sm">
                            FUNIL
                        </p>
                        <br>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_aguardando" id="relatorios_aguardando" <?=($vet['relatorios_aguardando'] == 1) ? 'checked' : ''?>> Aguardando 
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_reagendar" id="relatorios_reagendar" <?=($vet['relatorios_reagendar'] == 1) ? 'checked' : ''?>> Reagendar 
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_reservados" id="relatorios_reservados" <?=($vet['relatorios_reservados'] == 1) ? 'checked' : ''?>> Reservados 
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_finalizados" id="relatorios_finalizados" <?=($vet['relatorios_finalizados'] == 1) ? 'checked' : ''?>> Finalizados
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_atendido" id="relatorios_atendido" <?=($vet['relatorios_atendido'] == 1) ? 'checked' : ''?>> Atendido
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="relatorios_pedidos" id="relatorios_pedidos" <?=($vet['relatorios_pedidos'] == 1) ? 'checked' : ''?>> Pedidos laboratório
                            </div>
                        </div>
                        <br>
                        <p class="font-bold  alert alert-info m-b-sm">
                            BAIXAR LISTA / RELATÓRIO
                        </p>
                        <br>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="checkbox" value="1" name="downloads" id="downloads" <?=($vet['downloads'] == 1) ? 'checked' : ''?>> Baixar
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
    $str = "SELECT * FROM usuarios_tipos WHERE idempresa = '$adm_empresa' ORDER BY nome";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de permissões cadastradas no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
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
                                    <td class="center">
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="permissoes.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="permissoes.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nome</th>
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
                Nenhuma permição encontrada no sistema.
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