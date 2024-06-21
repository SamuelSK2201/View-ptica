<?
include("includes/header.php");

$txt1 = addslashes($_POST['txt1']);
$txt2 = addslashes($_POST['txt2']);
$txt3 = addslashes($_POST['txt3']);
$txt4 = addslashes($_POST['txt4']);

$str = "SELECT * FROM disponiveis_txt WHERE idempresa = '$adm_empresa'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$num = mysqli_num_rows($rs);
$vet = mysqli_fetch_array($rs);

if($_POST['cmd'] == "add")
{
    $str = "INSERT INTO disponiveis_txt (idempresa, txt1, txt2, txt3, txt4) VALUES ('$adm_empresa', '$txt1', '$txt2', '$txt3', '$txt4')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("disponiveis_txt.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{
    $str = "UPDATE disponiveis_txt SET txt1 = '$txt1', txt2 = '$txt2', txt3 = '$txt3', txt4 = '$txt4' WHERE idempresa = '$adm_empresa'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("disponiveis_txt.php?ind_msg=2");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Textos registrados com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Textos editados com sucesso!';
?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Gerenciar os textos das telas de agendamento</h2>
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
                <div class="ibox-content" style="display: block;">
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" value="<?=($num > 0) ? 'edit' : 'add'?>">            

                        <div class="row">                            
                            <div class="col-md-12">
                                <p class="font-bold">
                                    TELA 01 - Texto da tela de seleção de data
                                </p>
                                <textarea class="form-control" name="txt1" id="txt1" rows="5" required><?=stripslashes($vet['txt1'])?></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row">                            
                            <div class="col-md-12">
                                <p class="font-bold">
                                    TELA 02 - Texto da tela de seleção de horário
                                </p>
                                <textarea class="form-control" name="txt2" id="txt2" rows="5" required><?=stripslashes($vet['txt2'])?></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row">                            
                            <div class="col-md-12">
                                <p class="font-bold">
                                    TELA 03 - Texto da tela com formulário de agendamento
                                </p>
                                <textarea class="form-control" name="txt3" id="txt3" rows="5" required><?=stripslashes($vet['txt3'])?></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row">                            
                            <div class="col-md-12">
                                <p class="font-bold">
                                    TELA 04 - Texto da tela de confirmação
                                </p>
                                <textarea class="form-control" name="txt4" id="txt4" rows="5" required><?=stripslashes($vet['txt4'])?></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12"> 
                                <button type="submit" class="btn btn-primary" ><?=($num > 0) ? 'Alterar' : 'Salvar'?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?
include("includes/footer.php");
?>