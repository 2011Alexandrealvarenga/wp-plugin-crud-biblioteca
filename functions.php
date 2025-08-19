<?php 
function pat_table_creator()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'biblioteca';
    $sql = "DROP TABLE IF EXISTS $table_name;
            CREATE TABLE $table_name(
            id mediumint(11) NOT NULL AUTO_INCREMENT,

            categoria varchar(200) NOT NULL,
            titulo TEXT NOT NULL,
            autor varchar(500) NOT NULL,
            ano varchar (200) NOT NULL,
            link text (200) NOT NULL,

            PRIMARY KEY id(id)
            )$charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function pat_da_display_esm_menu()
{
    add_menu_page('Biblioteca Unidades', 'Biblioteca Unidades', 'manage_options', 'pat-emp-list', 'da_PAT_list_callback','', 8);
    add_submenu_page('pat-emp-list', 'Biblioteca - Lista', 'Biblioteca - Lista', 'manage_options', 'pat-emp-list', 'da_PAT_list_callback');
    add_submenu_page(null, 'Biblioteca Atualiza', 'Biblioteca Atualiza', 'manage_options', 'update-pat', 'pat_da_emp_update_call');
    add_submenu_page(null, 'Delete Employee', 'Delete Employee', 'manage_options', 'delete-pat', 'pat_da_emp_delete_call');
}

//[employee_list]
// add_shortcode('employee_list', 'da_PAT_list_callback');

function da_PAT_list_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'biblioteca';
    $msg = '';
    if (isset($_REQUEST['submit'])) {
        $wpdb->insert("$table_name", [
            "categoria" => $_REQUEST['categoria'],
            'titulo' => $_REQUEST['titulo'],
            'autor' => $_REQUEST['autor'],
            'ano' => $_REQUEST['ano'],
            'link' => $_REQUEST['link']
        ]);

        if ($wpdb->insert_id > 0) {
            $msg = "Gravado com sucesso!";
        } else {
            $msg = "Falha ao gravar!";
        }
    }

    ?>
    <div class="content-pat">
        <h1 class="title">Biblioteca - Unidades</h1>
        <h2 class="subtitle">Cadastro de Unidade</h2>
        <form method="post">
            <div class="cont">
                <div class="esq">
                    <span>Categoria</span>
                </div>
                <input type="text" name="categoria" ><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>Titulo</span>
                </div>
                <input type="text" name="titulo" required><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>Autor(a)</span>
                </div>
                <input type="text" name="autor" ><br>
            </div>
            <div class="cont">

                <div class="esq">
                    <span>Ano</span>
                </div>
                <input type="text" name="ano" required><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>Link</span>
                </div>
                <input type="text" name="link" ><br>
            </div>
           
            <!-- <div class="cont">
                <div class="esq">
                    <span>Telefone</span>
                </div>
                <input type="text" name="telefone" ><br>
            </div> -->
            
            <div class="cont">
                <div class="esq">
                    <h4 id="msg" class="alert"><?php echo $msg; ?></h4>
                    <button class="btn-pat" type="submit" name="submit">CADASTRAR</button>

                </div>
            </div>           
        </form>
    </div>
    <?php 

    $table_name = $wpdb->prefix . 'biblioteca';
    $employee_list = $wpdb->get_results($wpdb->prepare("select * FROM $table_name ORDER BY titulo asc "), ARRAY_A);
    if (count($employee_list) > 0): ?>  

        <div class="busca">
            <h3 class="subtitle">Realize a busca da unidade</h3>
            <input type="text" class="form-control" id="live_search" autocomplete="off" placeholder="Ex.: Titulo, Categoria, Autor(a) ...">
        </div>   
        <div id="searchresult" style="margin: 24px 10px 0 0; display: block;"></div>
        <script  src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <script type="text/javascript">
            $(document).ready(function(){
                $("#live_search").keyup(function(){
                    var input = $(this).val();
                    // alert(input);
                    var url_search =  "<?php echo site_url(); ?>/wp-content/plugins/Wordpress-Plugin-CRUD-PAT/busca-resultado.php";
                    
                    if(input != ""){
                        $.ajax({                      
                            url:url_search,
                            method: "POST",
                            data:{input:input},

                            success:function(data){
                                $("#searchresult").html(data);
                                $("#searchresult").css('display','block');
                                $("#registros-todos-dados-tabela").css('display','none');
                            }
                        });
                    }else{
                        $("#searchresult").css("display","none");
                        $("#registros-todos-dados-tabela").css('display','block');
                    }
                });
            });
        </script>   
        <div id="registros-todos-dados-tabela" style="margin: 24px 10px 0 0;">
            <?php resultado_busca($employee_list);?>
        </div>
    <?php else:echo "<h2>Não há Informação</h2>";endif;
}


function resultado_busca($employee_list){?>
    <table border="1" cellpadding="5" width="100%">
        <tr>
            <th>ID</th>
            <th>Categoria</th>
            <th>Titulo</th>
            <th>Autor(a)</th>
            <th>Ano</th>
            <th>Link</th>
            <!-- <th>Telefone</th> -->

            <th>Editar</th>
            <th>Deletar</th>
        </tr>
        <?php $i = 1;
        foreach ($employee_list as $index => $employee): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $employee['categoria']; ?></td>
                <td><?php echo $employee['titulo']; ?></td>
                <td><?php echo $employee['autor']; ?></td>
                <td><?php echo $employee['ano']; ?></td>
                <td><?php echo $employee['link']; ?></td>
                <!-- <td><?php //echo $employee['telefone']; ?></td> -->

                <td><a href="admin.php?page=update-pat&id=<?php echo $employee['id']; ?>" class="btn-editar">EDITAR</a></td>
                <td><a href="admin.php?page=delete-pat&id=<?php echo $employee['id']; ?>" class="btn-deletar">DELETAR</a></td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php }

function pat_da_emp_update_call()
{
    global $wpdb;
    
    $url = site_url();
    $url2 = '/wp-admin/admin.php?page=pat-emp-list';
    $urlvoltar = $url.$url2;

    $table_name = $wpdb->prefix . 'biblioteca';
    $msg = '';
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : "";
    
    $employee_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name where id = %d", $id), ARRAY_A); ?>
   <div class="content-pat">
        <h1 class="title">Biblioteca - Unidades</h1>
        <h2 class="subtitle">Atualização de Cadastro de Unidade</h2>
        <form method="post">  
            <div class="cont">
                <div class="esq">
                    <span>Categoria</span>
                </div>
                <input type="text" name="categoria" value="<?php echo $employee_details['categoria']; ?>" ><br>
            </div>    
            <div class="cont">
                <div class="esq">
                    <span>Titulo</span>
                </div>
                <input type="text" name="titulo" value="<?php echo $employee_details['titulo']; ?>" required><br>
            </div>  
            <div class="cont">
                <div class="esq">
                    <span>Autor</span>
                </div>
                <input type="text" name="autor" value="<?php echo $employee_details['autor']; ?>" ><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>Ano</span>
                </div>
                <input type="text" name="ano" value="<?php echo $employee_details['ano']; ?>" ><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>Link</span>
                </div>
                <input type="text" name="link" value="<?php echo $employee_details['link']; ?>" ><br>
            </div>
           
            <!-- <div class="cont">
                <div class="esq">
                    <span>Telefone</span>
                </div>
                <input type="text" name="telefone" value="<?php //echo $employee_details['telefone']; ?>" ><br>
            </div> -->
            
            <div class="cont">
                <div class="esq">
                    <button class="btn-pat" type="submit" name="update">ATUALIZAR</button>
                </div>
            </div>
            <div class="cont">
                <div class="esq">
                    <?php                     
                        if (isset($_REQUEST['update'])) {
                            if (!empty($id)) {
                                $wpdb->update("$table_name", [
                                    "categoria" => $_REQUEST['categoria'], 
                                    "titulo" => $_REQUEST['titulo'],
                                    "autor" => $_REQUEST['autor'],
                                    'ano' => $_REQUEST['ano'], 
                                    'link' => $_REQUEST['link']
                            ], ["id" => $id]);
                                $msg = 'Atualização realizada!';
                                echo '<h4 class="alert">    '. $msg .'</h4>';
                                echo '<a href="'. $urlvoltar.'" class="link-back">Voltar para a lista</a>';
                            }
                        }
                    ?>
                    
                </div>
            </div> 
        </form>
<?php }

function pat_da_emp_delete_call()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'biblioteca';
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : "";
    if (isset($_REQUEST['delete'])) {
        if ($_REQUEST['conf'] == 'yes') {
            $row_exits = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
            if (count($row_exits) > 0) {
                $wpdb->delete("$table_name", array('id' => $id,));
            }
        } ?>
        <script>location.href = "<?php echo site_url(); ?>/wp-admin/admin.php?page=pat-emp-list";</script>
    <?php } ?>
    <form method="post">
        <div class="content-pat">
            <h1 class="title">Biblioteca - Unidades</h1>
            <h2 class="subtitle">Exclusão de cadastro de Unidade</h2>

            <h3 class="description">Deseja realmente apagar?</h3 >
            <input type="radio" name="conf" value="yes" checked>Sim
            <input type="radio" name="conf" value="no" >Não  <br><br>      
        
            <button class="btn-pat" type="submit" name="delete">OK</button>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        </div>        
    </form>
<?php 
}

function cwpai_exclude_data_from_xyztable() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'biblioteca';
    
    $sql = $wpdb->prepare(
        "DELETE FROM $table_name WHERE categoria IS NOT NULL AND titulo IS NOT NULL AND autor IS NOT NULL AND ano IS NOT NULL AND link IS NOT NULL"
    );
    
    $wpdb->query($sql);
}

function cwpai_insert_data_into_pat_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'biblioteca';

    $sql = $wpdb->prepare(
        "INSERT INTO $table_name (categoria, titulo, autor, ano, link)
        VALUES

('Sustentabilidade e ODS ','A Política Pública de Compras Sustentáveis no Governo do Ceará','Otávio Nunes de Vasconcelos Francisco Roberto Pinto','2016','https://bdtd.ibict.br/vufind/Record/UECE-0_8d7403a446867dafcd840d36f6686a75/Details'),
('Sustentabilidade e ODS ','A certificação ambiental como requisito de sustentabilidade e ecoeficiência nas compras públicas','Bernardi, Luiz Agnaldo','2019','https://bdtd.ibict.br/vufind/Record/UFPR_f6197730732912c483d7b0811d864e87'),
('Logística e Gestão de Suprimentos','A inovação como um vetor de mudança no processo de compra pública da agricultura familiar oriunda do PNAE','Oliveira Júnior, José Mendes de','2021','https://bdtd.ibict.br/vufind/Record/UNB_e83456194b73722012f4e335a3152c14'),
('Sustentabilidade e ODS ','Compras públicas sustentáveis: análise dos stakeholders de uma instituição federal de ensino do nordeste brasileiro','Castro, Marfisa Carla de Abreu Maciel','2021','https://bdtd.ibict.br/vufind/Record/UFOR_521bd0be9e411b265317c830bc193f84'),
('Sustentabilidade e ODS ','Em direção aos processos sustentáveis em compras públicas : uma investigação no contexto de uma instituição de ensino superior','SOUZA, Maria Isabel Teófilo de','2023','https://bdtd.ibict.br/vufind/Record/UFPE_be2b02e6a1a8b12f49d9b3240c698b8c')
        
        ");

     $wpdb->query($sql);    
}

