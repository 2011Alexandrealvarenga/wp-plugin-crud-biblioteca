<?php 
function biblioteca_plugin_uninstall() {
    global $wpdb;
    
    // Excluir tabela
    $table_name = $wpdb->prefix . 'biblioteca';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    
    // Limpar quaisquer opções relacionadas
    delete_option('biblioteca_db_version');
}

function pat_table_creator()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'biblioteca';
    $sql = "DROP TABLE IF EXISTS $table_name;
            CREATE TABLE $table_name(
            id mediumint(11) NOT NULL AUTO_INCREMENT,

            id_item TEXT NOT NULL,
            categoria TEXT NOT NULL,
            titulo TEXT NOT NULL,
            autor TEXT NOT NULL,
            ano TEXT NOT NULL,
            link TEXT NOT NULL,
            palavra_chave text NOT NULL,

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
            "id_item" => $_REQUEST['id_item'],
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
                    <span>ID</span>
                </div>
                <input type="text" name="id_item" ><br>
            </div>
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
           
            <div class="cont">
                <div class="esq">
                    <span>Palavras-Chave</span>
                </div>
                <input type="text" name="palavra_chave" ><br>
            </div>
            
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
                        $("#searchresult").css("display",none");
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
            <th>id_item</th>
            <th>Categoria</th>
            <th>Titulo</th>
            <th>Autor(a)</th>
            <th>Ano</th>
            <th>Link</th>
            <th>Palavras-Chave</th>

            <th>Editar</th>
            <th>Deletar</th>
        </tr>
        <?php $i = 1;
        foreach ($employee_list as $index => $employee): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $employee['id_item']; ?></td>
                <td><?php echo $employee['categoria']; ?></td>
                <td><?php echo $employee['titulo']; ?></td>
                <td><?php echo $employee['autor']; ?></td>
                <td><?php echo $employee['ano']; ?></td>
                <td><?php echo $employee['link']; ?></td>
                <td><?php echo $employee['palavra_chave']; ?></td>

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
                    <span>ID</span>
                </div>
                <input type="text" name="id_item" value="<?php echo $employee_details['id_item']; ?>" ><br>
            </div>    
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
            <div class="cont">
                <div class="esq">
                    <span>Palavras Chave</span>
                </div>
                <input type="text" name="palavra_chave" value="<?php echo $employee_details['palavra_chave']; ?>" ><br>
            </div>
            
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
                                    "id_item" => $_REQUEST['id_item'], 
                                    "categoria" => $_REQUEST['categoria'], 
                                    "titulo" => $_REQUEST['titulo'],
                                    "autor" => $_REQUEST['autor'],
                                    'ano' => $_REQUEST['ano'], 
                                    'palavra_chave' => $_REQUEST['palavra_chave'], 
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
// insere os valores para o banco

function cwpai_insert_data_into_pat_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'biblioteca';

    $data = array(
array('1.1','Integridade nas contratações','A atuação do observatório social no monitoramento das compras públicas em tempos de pandemia da Covid-19: estudo de caso em um município mineiro  ','Gilson Luiz de Araújo','2023','https://bdtd.ibict.br/vufind/Record/CEFETMG_f8d6d2bb45407dcbeeb6d3e15da5b6f0','atuação, observatório, social, monitoramento, compras'),
array('1.1','Integridade nas contratações','Influência das estruturas e dos processos de governança e gestão de aquisições no desempenho das compras públicas  ','Souza, Kleberson Roberto de','2022','https://bdtd.ibict.br/vufind/Record/FGV_49cff6cce5141f6e15b1b1420265b12a','estruturas, processos, governança, gestão, aquisições'),
array('1.1','Integridade nas contratações',' Modelo de Maturidade para Avaliar a Gestão da Integridade nas Contratações Públicas – MGIC','PAIVA, Rodrigo Márcio Medeiros','2023','https://repositorio.ufpb.br/jspui/handle/123456789/33403','Contratações públicas, Gestão de riscos - Integridade, Modelo de maturidade, Inovação, Public bidding, Public contracts, Innovative public procurement, Healthtechs'),
array('1.1','Integridade nas contratações','Governança das aquisições: a implementação de um plano de gestão de riscos em uma seção de licitações do Exército Brasileiro','FERREIRA, José Roberto Chagas','2021','https://repositorio.idp.edu.br/handle/123456789/4203','Governança das aquisições, Licitações, Compras públicas, Gestão de riscos, Exército Brasileiro, Administração pública'),
array('1.1','Integridade nas contratações','Modelo de maturidade para avaliar a gestão de integridade nas contratações públicas ','Paiva, Rodrigo Márcio Medeiros; Vieira, James Batista','2024','https://periodicos.fgv.br/cgpc/article/view/91099','integridade pública; Licitações Públicas; gestão de riscos; contratações públicas; modelo de maturidade.'),
array('1.1','Integridade nas contratações','Práticas anticoncorrenciais no Brasil: verificação analítica, empírica e sistêmica da integridade de aquisições públicas por pregões eletrônicos','Sampaio, Adilson da Hora','2021','https://repositorio.ufba.br/handle/ri/37188','Práticas anticoncorrenciais Pregão eletrônico Corrupção Dinâmica de sistemas Fatores determinantes da corrupção'),
array('1.1','Integridade nas contratações','Compliance na administração pública: avaliação do processo inicial de implementação dos Programas de Integridade no Estado de São Paulo','Brito, Feliphe Ulisses','2024','https://sapientia.pucsp.br/handle/handle/42175','Integridade Administração pública Controladoria Corrupção'),
array('1.2','Participação social','Open Data Standards for Public Procurement and Contracting: A Collaborative Construction','SALM JUNIOR et al.,','2024','https://www.researchgate.net/publication/382275241_Open_Data_Standards_for_Public_Procurement_and_Contracting_A_Collaborative_Construction','construção colaborativa; padrão; dados abertos; compras públicas; municípios '),
array('1.3','Prestação de contas','Governança de contratos públicos: a materialização dos princípios da eficiência e do planejamento na Lei nº 14.133/2021','LÓPEZ VALLE et al.,','2023','https://www.scielo.br/j/seq/a/6mBFwVS889DqhL7zJdq5Pnc/?lang=en','Licitações; Governança das Contratações Públicas; Lei 14.133/2021; Eficiência; Planejamento'),
array('1.4','Mecanismos de controle interno e externo','Análise dos padrões de descontos nas modalidades de contratações públicas no Estado do Rio Grande do Norte',' Dantas, Raul Omar de Oliveira','2022','https://bdtd.ibict.br/vufind/Record/UFRN_b18cd940aed4da7c90919e197c7a27e5','Governança pública, Transparência, Eficiência administrativa, Modalidades de licitação, Análise estatística, Economia nas compras públicas, Tomada de decisão baseada em evidências'),
array('1.4','Mecanismos de controle interno e externo','Contratações públicas no cenário de governança pela internet: o pregão eletrônico na UFPE','FARIAS, Rogério Assunção de','2010','RI UFPE: Contratações públicas no cenário de governança pela internet : o pregão eletrônico na UFPE','Desenvolvimento econômico, Pregões eletrônicos, Gestão pública, Governança eletrônica, Licitações'),
array('1.4','Mecanismos de controle interno e externo','Centralização das compras públicas : vantagens e desafios de implementação','Silva, Stéfane Nascimento da','2024','Repositório Institucional da UnB: Centralização das compras públicas : vantagens e desafios de implementação','Centralização de compras, Compras públicas, Governança pública, Eficiência, Sustentabilidade, Transparência, Inovação, Lei 14.133/2021, Coordenação intergovernamental'),
array('1.4','Mecanismos de controle interno e externo','A transparência intraorganizacional como princípio da governança pública: aplicação no contexto das contratações em um tribunal regional do trabalho.','HOLLANDA, Dirceu Victor Monte de','2024','https://repositorio.ufrn.br/items/0dc9b322-e6c2-46c6-8604-8a21b0c504e2','Transparência intraorganizacional; Governança pública; Design Science Research (DSR); Contratações públicas'),
array('1.4','Mecanismos de controle interno e externo','A contratação pública como instrumento para a transparência e concorrência das compras públicas em Portugal','LOPES, Carla Sofia Alves  e  ROMAO, Ana Lúcia','2021','https://www.scielo.pt/scielo.php?script=sci_arttext&pid=S2184-77702021000400267&lang=pt','Concorrência; Contratação Pública; Metodologia Qualitativa; Transparência'),
array('1.4','Mecanismos de controle interno e externo','Coordenação de planejamento estratégico de compras da Fiocruz: uma proposta de governança para compras em rede','Santos, Lindenberg Lins dos','2021','https://arca.fiocruz.br/items/d0f4e628-96b5-4ca0-bdeb-0c4f2b464d35','Compras Públicas, Governança em Compras, Rede de Compras, Planejamento Estratégico'),
array('1.4','Mecanismos de controle interno e externo','Transparência e accountability em serviços públicos digitais','Saldanha, Douglas Morgan Fullin','2020','https://repositorio.unb.br/handle/10482/38702','Transparência, Serviços públicos eletrônicos, Accountability'),
array('10.1','Compras Municipais e Estaduais','A capacidade institucional na gestão das compras públicas em saúde: uma análise dos municípios da 7ª Região de Saúde do Rio Grande do Norte  ','Silva, Layse Rodrigues da','2025','https://repositorio.ufrn.br/server/api/core/bitstreams/3a806480-d408-4831-9fe1-3e6acf7f197f/content','saúde, capacidade, institucional, compras, públicas'),
array('10.1','Compras Municipais e Estaduais','Compras Governamentais nos Municípios do Estado de Mato Grosso do Sul: Proposta de um Roteiro Prático para as Licitações Eletrônicas, com Base na Lei Nº 14.133/2021  ','Martins, Cristiane Pereira dos Santos','2023','https://bdtd.ibict.br/vufind/Record/UFMS_2cfa1e0657629253600e6e827f1f0a7b','compras, municípios, mato, grosso, roteiro'),
array('10.1','Compras Municipais e Estaduais','Compras públicas em tempos de pandemia : uma análise dos entes subnacionais estaduais e dos consórcios públicos interestaduais  ','Rufino Filho, Ednaldo Tavares','2023','https://bdtd.ibict.br/vufind/Record/URGS_f093a80a834e8e56f9578199ac340722','consórcios, compras, públicas, pandemia, estaduais'),
array('10.1','Compras Municipais e Estaduais','Governança em compras públicas: um estudo na Secretaria Municipal do Planejamento, Orçamento e Gestão da Prefeitura de Fortaleza  ','Silva, Leonardo Pereira da','2024','https://bdtd.ibict.br/vufind/Record/UFC-7_4e22131434441c05c576db3476b79f70','governança, secretaria, municipal, planejamento, orçamento'),
array('10.1','Compras Municipais e Estaduais','Desafios e perspectivas para a centralização das contratações públicas no governo do Estado de São Paulo','Porta et al.,','2022','https://revista.enap.gov.br/index.php/RSP/article/view/6884/4555','centralização, públicas, estado, paulo, compras'),
array('10.1','Compras Municipais e Estaduais','A Gestão de Compras Públicas: um Estudo de Caso da Central de Compras do Distrito Federal.','Pinto de Araújo, Grice Barbosa;de Sousa Lemos, Leany Barreiro','2020','https://periodicos.ufpb.br/index.php/tpa/article/view/51188/30275','compras, gestão, públicas, estudo, caso'),
array('10.1','Compras Municipais e Estaduais','A concepção de compras públicas do Programa Nacional de Alimentação Escolar e a realidade de agricultores familiares e agentes públicos no estado de São Paulo, Brasil.','Giacomo Baccarin, José; Alexandre de Oliveira, Jonatan; Ezequiel Fonseca, Adriano','2022','https://doi.org/10.47946/rnera.v25i63.8601','compras, públicas, agricultores, familiares, concepção'),
array('10.1','Compras Municipais e Estaduais','Retos de la contratación pública local para garantizar la calidad y la innovación en los servicios de atención a las personas ','MARTÍ-COSTA, Marc; CONDE LÓPEZ, Cecilia Isabel','2024','https://www.redalyc.org/journal/2815/281580807003/','Serviços de atenção às pessoas, cláusulas sociais, órgãos de contratação, contratação pública, qualidade, concertação'),
array('10.1','Desafios locais','Insatisfacción con el sistema nacional de contratación pública: una visión del contratista en ejecución de obras (EQUADOR)','RODRIGUEZ, Elizabeth; RIVERA, Carlos; CASTILLO, Tito.','2021','https://doi.org/10.37135/unach.ns.001.01.10 ','adjudicação; compras públicas; contratado; fornecedor;empreiteiro; execução do projeto; insatisfação; Equador'),
array('10.1','Desafios locais','Essays in public food procurement: the case of PNAE in Brazil',' Siqueira, Ana Carolina Ferreira de','2023','https://www.teses.usp.br/teses/disponiveis/12/12139/tde-17042023-150611/','Governança; Município; Políticas públicas; Variável instrumental'),
array('10.2','Compras Municipais e Estaduais','Compras públicas municipais : uma proposta de modelo de gestão para municípios de pequeno porte  ','Pereira, Valdinei Juliano','2024','https://bdtd.ibict.br/vufind/Record/UEL_527be0b50da41c29a4ca322f3e66940a','compras, públicas, modelo, gestão, municípios'),
array('10.2','Compras Municipais e Estaduais','Compras governamentais dos municípios do estado de São Paulo: comportamento das modalidades de contratações de 2008 a 2018','Anjos, Lucas Madureira dos','2021','https://www.teses.usp.br/teses/disponiveis/96/96132/tde-31052021-141827/pt-br.php','compras, municípios, modalidades, governamentais, estado'),
array('10.2','Casos de sucesso','Cidades inovadoras: compras públicas como motor de inovação','Fernandes, Nelson da Cruz Monteiro','2024','https://repositorio.enap.gov.br/handle/1/8072','inovação, compras, públicas, cidades, inovadoras'),
array('10.3','Compras Municipais e Estaduais','Compras públicas alimentares em São Luís (Maranhão) e a construção de sistemas alimentares sustentáveis  ','Braga, Camila Lago','2023','https://bdtd.ibict.br/vufind/Record/URGS_b8783f26d0788253dbdeb4935e06879e','alimentares, compras, públicas, luís, maranhão'),
array('10.3','Regionalização de políticas','Compras públicas e o fomento ao desenvolvimento econômico local : uma análise para o município de Londrina entre 2016 e 2019  ','Santos, Natália Garcia','2024','https://bdtd.ibict.br/vufind/Record/UEL_3d7254c85a74484b8d82ba7d30132a98','compras, públicas, fomento, desenvolvimento, econômico'),
array('2.1','Compras públicas sustentáveis','Gestão do processo de compras públicas: aquisição de alimentos da agricultura familiar no contexto universitário  ','Souza, João Gabriel Sobierajski de','2024','https://bdtd.ibict.br/vufind/Record/UFSC_e3a0e86059f8197eeb47b997f700c977','aquisição, universitário, gestão, processo, compras'),
array('2.1','Compras públicas sustentáveis','Incorporación de valor social a la contratación pública en España: Situación y perspectivas ','GARCÍA, Marta Solorzano; MARCO, Julio Navío; COMECHE, Raúl Contreras.   ','2015','https://www.redalyc.org/articulo.oa?id=576461446002','Valor social, contratação pública, impacto social'),
array('2.1','Compras públicas sustentáveis','Compras públicas sustentáveis: análise dos stakeholders de uma instituição federal de ensino do nordeste brasileiro  ','Castro, Marfisa Carla de Abreu Maciel','2021','https://bdtd.ibict.br/vufind/Record/UFOR_521bd0be9e411b265317c830bc193f84','compras, públicas, sustentáveis, stakeholders, análise'),
array('2.1','Compras públicas sustentáveis','Em direção aos processos sustentáveis em compras públicas :uma investigação no contexto de uma instituição de ensino superior  ','SOUZA, Maria Isabel Teófilo de','2023','https://bdtd.ibict.br/vufind/Record/UFPE_be2b02e6a1a8b12f49d9b3240c698b8c','processos, sustentáveis, compras, públicas, direção'),
array('2.1','Compras públicas sustentáveis','Sustentabilidade nas compras e contratações públicas : estudo de caso em uma instituição pública federal  ','Wyse, Angela Terezinha de Souza','2016','https://bdtd.ibict.br/vufind/Author/Home?author=Silv%C3%A9rio%2C+Andreia+Pereira','compras, contratações, estudo, sustentabilidade, públicas'),
array('2.1','Compras públicas sustentáveis','Análise do princípio da eficiência administrativa nas compras públicas sustentáveis na agricultura familiar a partir da proposição de modelo teórico e metodológico de eficiência ecossocioeconomica  ','Araújo, Liane Maria Santiago Cavalcante','2020','https://bdtd.ibict.br/vufind/Record/UFOR_440dd2a3a88148b43bcccf27130b46ca','eficiência, princípio, administrativa, compras, públicas'),
array('2.1','Compras públicas sustentáveis','Compras públicas sustentáveis: uma análise sob a ótica da teoria da prática numa instituição pública de ensino superior  ','Silva, Aline Alves da','2024','https://bdtd.ibict.br/vufind/Record/UFC-7_a3e5dd30138a2f2903b833c5d4904b9b','compras, públicas, sustentáveis, teoria, prática'),
array('2.1','Compras públicas sustentáveis','Compras Públicas como vetor de Desenvolvimento Sustentável ','Cordeiro, Marcela Ribeiro Manhães Freitas','2023','https://bdtd.ibict.br/vufind/Record/UFVJM-2_777c2622664e1a9a3619eb1d33f3a650','públicas, desenvolvimento, sustentável, compras, vetor'),
array('2.1','Compras públicas sustentáveis','Compras públicas sustentáveis: uma revisão acerca da aquisição de alimentos  ','Ferreira, Elaine Cristina','2024','https://repositorio.ufu.br/handle/123456789/44049','compras, públicas, sustentáveis, revisão, acerca'),
array('2.1','Compras públicas sustentáveis','Compra pública como ferramenta do desenvolvimento sustentável : análise da sustentabilidade dos pregões eletrônicos gerenciados pelo município de Rio Grande em 2019  ','Mendonça, Adriano Barbosa','2021','https://bdtd.ibict.br/vufind/Record/FURG_3be0e9f2a5afd5df5889594a7f23d017','sustentabilidade, grande, compra, pública, ferramenta'),
array('2.1','Compras públicas sustentáveis','Compras públicas sustentáveis: análise dos stakeholders de uma instituição federal de ensino do nordeste brasileiro  ','Castro, Marfisa Carla de Abreu Maciel','2021','https://bdtd.ibict.br/vufind/Record/UFOR_521bd0be9e411b265317c830bc193f84','compras, públicas, sustentáveis, stakeholders, análise'),
array('2.1','Compras públicas sustentáveis','Compras públicas sustentáveis: uma análise do desempenho da Advocacia-Geral da União no contexto da Administração Pública Federal   ','Melo, Arnaldo Aparecido de','2018','https://bdtd.ibict.br/vufind/Record/UFSM_773550006f4adb34318eb04f9970d567','compras, públicas, sustentáveis, desempenho, advocaciageral'),
array('2.1','Compras públicas sustentáveis','Possibilidades e limitações para as compras públicas sustentáveis na Universidade Federal do Pará  ','SILVA, Adriana Bastos Silva','2014','https://bdtd.ibict.br/vufind/Record/UFPA_826d88f1c9e4830d9253c02e8dbc6d61','possibilidades, limitações, compras, públicas, sustentáveis'),
array('2.1','Compras públicas sustentáveis','Fundos europeus e compras públicas ecológicas','Nicolas et al','2025','https://www.sciencedirect.com/science/article/pii/S0921800924002970?via%3Dihub','compras, públicas, fundos, europeus, ecológicas'),
array('2.1','Compras públicas sustentáveis','Evaluation of sustainable public procurement in Spanish Universties. A focus on canteen services',' Valls-Val et al.,  ','2025','https://www.sciencedirect.com/science/article/pii/S0195925525000150?via%3Dihub','sustentáveis, universidades, serviços, cantina, avaliação'),
array('2.1','Compras públicas sustentáveis','Compras públicas sustentáveis: o Estado induz sustentabilidade com seu poder de compra?','Jorge Luiz P. Tardan','2020','https://www.redalyc.org/pdf/7198/719877736012.pdf','sustentabilidade, poder, compra, compras, públicas'),
array('2.1','Compras públicas sustentáveis','Green public procurement and corporate environmental performance: An empirical analysis based on data from green procurement contracts','Suyi Zheng a b , Jiandong Wen a b','2024','https://www.sciencedirect.com/science/article/abs/pii/S1059056024005707?via%3Dihub','contratação, pública, verde, ambiental, desempenho'),
array('2.1','Compras públicas sustentáveis','Green public procurement, external pressures and enterprise green transition: Evidence from China','Jinqi Gao , Fã Lu','2025','https://www.sciencedirect.com/science/article/abs/pii/S1049007825000600?via%3Dihub','transição, verde, china, compras, públicas'),
array('2.1','Compras públicas sustentáveis','Implementing public procurement of green innovations: Does structural alignment matter?','Jan Ole Similä a , Deodat Mwesio','2024','https://doi.org/10.1016/j.jclepro.2024.142562','compras, públicas, alinhamento, implementação, inovações'),
array('2.1','Compras públicas sustentáveis','The impact of public procurement on the adoption of circular economy practices','Sun et al.,','2024','https://www.sciencedirect.com/science/article/abs/pii/S147840922400013X?via%3Dihub','compras, públicas, adoção, práticas, economia'),
array('2.1','Compras públicas sustentáveis','Difusão da informação em processos de compras públicas sustentáveis: um estudo na perspectiva da análise de redes sociais','Seixas et al.,','2018','https://www.researchgate.net/publication/322719217_Difusao_da_informacao_em_processos_de_compras_publicas_sustentaveis_um_estudo_na_perspectiva_da_analise_de_redes_sociais','difusão, informação, processos, compras, estudo'),
array('2.1','Compras públicas sustentáveis','Análise dos resultados das contratações públicas sustentáveis','Biage, Verlany Souza Marinho de','2024','https://www.scielo.br/j/read/a/bTX9f7JFfRdcpRhQmWGJvbG/?lang=pt','públicas, análise, resultados, contratações, sustentáveis'),
array('2.1','Compras públicas sustentáveis','Institucionalização da contratação pública sustentável: uma análise da experiencia de MG','Mendonça, Ricardo Almeida Marques','2022','https://www.scielo.br/j/read/a/5vCczWPdBb867SydczJGLvN/?lang=pt','institucionalização, contratação, pública, sustentável, análise'),
array('2.1','Compras públicas sustentáveis','The heterogeneous impact of green public procurement on corporate green innovation','Kou et al.,','2024','https://doi.org/10.1016/j.resconrec.2024.107441','inovação, verde, compras, públicas, verdes'),
array('2.1','Compras públicas sustentáveis','Compras públicas como política para o desenvolvimento sustentável','OLIVEIRA, Bernardo Carlos S. C. M. de; SANTOS, Luis Miguel Luzio dos','2015','https://www.scielo.br/j/rap/a/rybgWdNfqmncMdXp6rZ4r9g/?lang=pt','desenvolvimento, sustentável, compras, públicas, política'),
array('2.1','Compras públicas sustentáveis','Análise da implementação da política de compras sustentáveis: um estudo de caso','Vitor Neves Cabral Biancca Scarpeline de Castro','2020','https://www.redalyc.org/journal/3211/321165166004/321165166004.pdf','implementação, compras, sustentáveis, análise, política'),
array('2.1','Compras públicas sustentáveis','Avaliação da Implementação: o contexto, a capacidade operacional e de aprendizagem da política de compras públicas sustentáveis','Maria de Moraes Silva, Anaítes;Alcobaça Gomes, Jaíra Maria','2022','https://revistas.uepg.br/index.php/emancipacao/article/view/14866','avaliação, implementação, compras, públicas, sustentáveis'),
array('2.1','Compras públicas sustentáveis','Barreiras às compras públicas sustentáveis: um survey exploratório no Brasil com organizações participantes do programa A3P','Delmônico, Diego Valério de Godoy','2017','https://repositorio.unesp.br/entities/publication/f4e27e81-a658-458c-b147-cfb3a830db3b','barreiras, compras, sustentáveis, públicas, survey'),
array('2.1','Compras públicas sustentáveis','Compras Públicas Sustentáveis No Brasil: Um Estudo Multi-Caso Em Organizações Governamentais','de Souza Silva Oliveira, Marcus Vinicius','2018','https://repositorioaberto.uab.pt/entities/publication/5e7fd438-b511-4dfd-9575-ccb2fddc3be8','compras, públicas, sustentáveis, organizações, brasil'),
array('2.1','Compras públicas sustentáveis ','Compras públicas sustentáveis: uma análise sob a ótica da teoria da prática numa instituição pública de ensino superior','Silva, Aline Alves da','2024','Metadados do item: Compras públicas sustentáveis: uma análise sob a ótica da teoria da prática numa instituição pública de ensino superior','sustentabilidade; compras públicas sustentáveis; universidade'),
array('2.1','Compras públicas sustentáveis','Transição sustentável das compras públicas de alimentos em IFES',' Giombelli, Giovana Paludo','2018','https://rd.uffs.edu.br/handle/prefix/2109','Restaurantes; Contratos; Agricultura familiar; Licitação; Tomada de preços'),
array('2.2','Inclusão de critérios de sustentabilidade','Sustainable Public Procurement: Integrating Environmental Standards into Global Supply Chains','MUTANGILI, Solomon Kyalo','2025','https://stratfordjournalpublishers.org/journals/index.php/journal-of-procurement-supply/article/view/2434','Compras públicas sustentáveis, padrões ambientais, cadeias de suprimentos globais, certificações ecológicas, tecnologias digitais.'),
array('2.2','Inclusão de critérios de sustentabilidade','A certificação ambiental como requisito de sustentabilidade e ecoeficiência nas compras públicas  ','Bernardi, Luiz Agnaldo','2019','https://bdtd.ibict.br/vufind/Record/UFPR_f6197730732912c483d7b0811d864e87','compras, públicas, certificação, ambiental, requisito'),
array('2.2','Inclusão de critérios de sustentabilidade','Licitações sustentáveis: os parâmetros do desenvolvimento nacional e o controle das compras públicas no estado do Ceará  ','Moraes Filho, Marco Antônio Praxedes de','2017','https://siduece.uece.br/siduece/trabalhoAcademicoPublico.jsf?id=86382','licitações, nacional, públicas, sustentáveis, parâmetros'),
array('2.2','Inclusão de critérios de sustentabilidade','Sustentabilidade nas licitações públicas e o princípio da economicidade: desafios para o desenvolvimento nacional sustentável','Alexandre, Wandewallesy de Brito','2020','https://repositorio.idp.edu.br/handle/123456789/2765','sustentabilidade, licitações, públicas, princípio, economicidade'),
array('2.2','Inclusão de critérios de sustentabilidade','Abordagem multicritério para apoiar compras públicas sustentáveis.  ','CABRAL, Luciana Priscila Barros.','2020','https://dspace.sti.ufcg.edu.br/bitstream/riufcg/12750/3/LUCIANA%20PRISCILA%20BARROS%20CABRAL%20-%20DISSERTA%c3%87%c3%83O%20PPGA%20CH%202020.pdf','sustentáveis, abordagem, multicritério, compras, públicas'),
array('2.2','Inclusão de critérios de sustentabilidade','Diretrizes para a gestão das compras públicas sustentáveis: a contribuição da UTFPR para o desenvolvimento nacional sustentável  ','Silva, Cristina Aparecida da','2018','https://bdtd.ibict.br/vufind/Record/UTFPR-12_883f3eead7378beadc57a6028a9da4b1','compras, diretrizes, públicas, sustentáveis, utfpr'),
array('2.2','Inclusão de critérios de sustentabilidade','Gestão de Compras Públicas: Uma avaliação dos critérios de sustentabilidade nas compras públicas do Instituto Federal do Triângulo Mineiro  ','Oliveira, Francielly Rodrigues de','2021','https://bdtd.ibict.br/vufind/Record/UFU_de2c4411e4d25f02b988b516c819563d','compras, públicas, critérios, gestão, avaliação'),
array('2.2','Inclusão de critérios de sustentabilidade','Desenvolvimento sustentável nas compras públicas: análise comparativa entre a Diretiva Europeia 2014/24/UE e a Lei nº 14.133/2021, visando superar as dificuldades de aplicação da lei brasileira  ','Bruno Fontenelle Gontijo','2024','https://bdtd.ibict.br/vufind/Record/UFMG_c6fba4b1ebb140051c6815a84680dd4c','compras, públicas, diretiva, europeia, aplicação'),
array('2.2','Inclusão de critérios de sustentabilidade','Compras públicas sustentáveis: Uma análise dos editais de licitação de cidades brasileiras participantes do Programa Cidades Sustentáveis.','Vilar Lemos et al., ','2020','https://dialnet.unirioja.es/servlet/articulo?codigo=7722648','sustentáveis, editais, licitação, cidades, compras'),
array('2.2','Inclusão de critérios de sustentabilidade','Compras públicas compartilhadas: a prática das licitações sustentáveis','Silva, Renato Cader da Barki, Teresa Villac Pinheiro','2012','https://repositorio.enap.gov.br/handle/1/1817','compras, públicas, sustentáveis, compartilhadas, prática'),
array('2.2','Inclusão de critérios de sustentabilidade','Instrumentos do Estado para estimular a ecoinovação: uma revisão sistemática','Galdino, Emanuel & Chistopoulos, Tania Pereira','2024','https://bibanpocs.emnuvens.com.br/revista/article/view/635/678','instrumentos, estado, estimular, ecoinovação, revisão'),
array('2.2','Inclusão de critérios de sustentabilidade','Fatores críticos no comportamento do gestor público responsável por compras sustentáveis: diferenças entre consumo individual e organizacional','Hugo Leonnardo Gomides do Couto Universidade Federal de Goiás (UFG)  SCImago image Cristiano Coelho Pontifícia Universidade Católica de Goiás (PUCGoiás)  SCImago image','2015','https://www.scielo.br/j/rap/a/94ScGWkPFxPjGVbLWHszVGz/?lang=pt','fatores, críticos, comportamento, gestor, público'),
array('2.2','Inclusão de critérios de sustentabilidade','Licitações públicas e sustentabilidade: uma análise da aplicação de critérios ambientais nas compras de órgãos públicos federais em Florianópolis (SC)','José Sérgio da Silva Cristóvam Hulisses Fernandes','2018','https://periodicos.pucpr.br/direitoeconomico/article/view/16857','sustentabilidade, critérios, licitações, públicas, análise'),
array('2.2','Inclusão de critérios de sustentabilidade','Incorporação de critérios de sustentabilidade nas compras públicas da Universidade Federal da Grande Dourados','Santos, Fernanda Ribeiro dos','2018','https://repositorio.ufgd.edu.br/jspui/handle/prefix/987','critérios, sustentabilidade, compras, universidade, federal'),
array('2.2','Inclusão de critérios de sustentabilidade','Objetivos e desafios da política de compras públicas sustentáveis no Brasil: a opinião dos especialistas','COUTO, Hugo Leonnardo Gomides do & RIBEIRO, Francis Lee','2016','https://www.scielo.br/j/rap/a/X5M39ysNDHK4Bw7rRY4SL7S/?lang=pt','compras públicas sustentáveis; Delphi de políticas; política pública; análise de conteúdo.'),
array('2.2','Inclusão de critérios de sustentabilidade','Licitações públicas e desenvolvimento sustentável municipal: o caso de Santa Rita do Passa Quatro','Octaviano, João Pedro Zorzi [UNESP]','2024','Metadados do item: Licitações públicas na perspectiva do desenvolvimento sustentável municipal: o caso de Santa Rita do Passa Quatro',' Direito Administrativo; Direito Ambiental; Lei de Licitações e Contratos Administrativos; Políticas Públicas; Sustentabilidade '),
array('2.2','Inclusão de critérios de sustentabilidade','Compras públicas sustentáveis: um estudo dos critérios de sustentabilidade na Infraero',' Rosset, Andrea Cecilia Soares','2018','http://www.repositorio.ufal.br/handle/riufal/2289','Administração pública; Compras; Critérios de sustentabilidade, aeroportos'),
array('2.2','Inclusão de critérios de sustentabilidade','Compras públicas sustentáveis: a influência do cenário de consumo sobre as preferências de gestores de compras governamentais','Couto, Hugo Leonnardo Gomides do','2015','http://repositorio.bc.ufg.br/tede/handle/tede/4781','Compras públicas sustentáveis , Cenário de consumo , Selos ambientais , Eficiência energética , Disposição a pagar ,'),
array('2.3','Alinhamento com os ODS da Agenda 2030','Compras públicas sostenibles en América Latina: análisis comparativo y normativo regional','ROJAS VICTORIO et al., ','2025','https://ve.scielo.org/scielo.php?script=sci_arttext&pid=S2739-00632026000102109','Compras públicas, Compras sustentáveis, Padronização, Desenvolvimento sustentável, ODS, América Latina, Regulação ambiental'),
array('2.3','Alinhamento com os ODS da Agenda 2030','La compra pública como mecanismo para alcanzar la sostenibilidad: propuesta para superar las brechas de información y la calidad de los datos para la formulación de un nuevo plan de acción nacional','BARRETO MORENO et al., ','2024','https://www.scielo.org.ar/scielo.php?pid=S2362-583X2024000200008&script=sci_abstract','Compra pública, intervenção estatal na economia, justiça social, sustentabilidade, objetivos de desenvolvimento sustentável, ODS'),
array('2.3','Alinhamento com os ODS da Agenda 2030','A Política Pública de Compras Sustentáveis no Governo do Ceará','Vasconcelos, Otávio Nunes de','2016','https://bdtd.ibict.br/vufind/Record/UECE-0_8d7403a446867dafcd840d36f6686a75/Details','compras, governo, ceará, política, pública'),
array('2.3','Alinhamento com os ODS da Agenda 2030','Compras públicas da agricultura familiar como indutoras do desenvolvimento rural sustentável na fronteira Brasil-Bolívia  ','Gisele Maria Barbosa da Cruz e Oliveira','2021','https://bdtd.ibict.br/vufind/Record/UFMS_6b0b38aa8213b91b9e2d44fc264d4222','públicas, agricultura, familiar, fronteira, preços'),
array('2.3','Alinhamento com os ODS da Agenda 2030','Objetivos do desenvolvimento sustentável e os desafios das compras públicas sustentáveis em unidades do exército  ','Camargo, Matheus Alexandre da Silva','2021','https://bdtd.ibict.br/vufind/Record/NOVE_7ec95c3321092b12e4f46315acd37c96','compras, públicas, sustentáveis, exército, objetivos'),
array('2.3','Alinhamento com os ODS da Agenda 2030','Pagamento por serviços ambientais para catadores de materiais recicláveis.','Silva, Pollyana Ferreira da','2022','https://doi.org/10.11606/T.6.2022.tde-12122022-121448','pagamento, serviços, ambientais, catadores, materiais'),
array('2.3','Alinhamento com os ODS da Agenda 2030','Consumo e produção responsáveis na agenda 2030 e o urgente compromisso em adequá-los às contratações públicas.','Alves Barros Cardoso, Silvia Karina;Macêdo Pederneiras, Maria Marcleide','2023','https://ojs.revistagesec.org.br/secretariado/article/view/1931','consumo, produção, responsáveis, agenda, urgente'),
array('2.3','Alinhamento com os ODS da Agenda 2030',' Proposta de modelo conceitual de critérios ambientais para contratação pública de obras rodoviárias federais',' Giamberardino, Guilherme Goncalves','2021',' http://repositorio.utfpr.edu.br/jspui/handle/1/26539','contratação pública, criterios, obras , obras, rodoviárias'),
array('2.3','Alinhamento com os ODS da Agenda 2030','GERENCIAMENTO DE RISCO DAS CONTRATAÇÕES PÚBLICAS DE UMA AUTARQUIA FEDERAL DE ENSINO','Nascimento,Fernanda Assis de Oliveira ','2020','https://app.uff.br/riuff/handle/1/15877','Contratações, Riscos, Autarquia, Gerenciamento'),
array('2.3','Alinhamento com os ODS da Agenda 2030','Compras públicas sustentáveis na Advocacia Geral da União: uma análise sob a ótica da Agenda 2030 da ONU para o Desenvolvimento Sustentável','Quirino, Marina Eliza Pacífico','2024','https://repositorio.fgv.br/items/839d0659-98d6-4401-8bc8-bcda747c539e','Agenda 2030, ODS, Critérios de Sustentabilidade, Contratações Públicas'),
array('3.1','Compras públicas para inovação (CPI)','Compras públicas de inovação como instrumento de implementação de política pública: a encomenda tecnológica aplicada pela Agência Espacial Brasileira  ','Nascimento, Henrique Fernandes','2024','https://repositorio.enap.gov.br/handle/1/7928','instrumento, política, pública, encomenda, tecnológica'),
array('3.1','Compras públicas para inovação (CPI)','Compras públicas de inovação: desafios dos gestores públicos  ','Wellington Pereira da Silva','2023','https://repositorio.uscs.edu.br/handle/123456789/1377','compras, inovação, gestores, públicos, públicas'),
array('3.1','Compras públicas para inovação (CPI)','Compras públicas para inovação e o desenvolvimento: um diagnóstico jurídico-institucional das encomendas tecnológicas no Brasil  ','Pimenta Filho, Luiz Cláudio','2021','https://hdl.handle.net/10438/30779','públicas, inovação, jurídicoinstitucional, compras, desenvolvimento'),
array('3.1','Compras públicas para inovação (CPI)','Compras públicas para inovação e offset na aeronáutica militar: o caso C-390  ','Sousa, Cairo Humberto da Cruz','2023','https://repositorio.ufu.br/handle/123456789/37889','compras, públicas, inovação, offset, aeronáutica'),
array('3.1','Compras públicas para inovação (CPI)','Metodologia ETECS: desenvolvendo soluções inovadoras nas compras públicas  ','Silva, Ívina Mariana Duarte Marinho e','2024',' https://repositorio.ufrn.br/handle/123456789/58437','metodologia, compras, públicas, etecs, desenvolvendo'),
array('3.1','Compras públicas para inovação (CPI)','A política de compras de entidades públicas como instrumento de capacitação tecnológica: o caso da Petrobrás','Cássio Garcia Ribeiro Soares da Silva, André Tosi Furtado','2004','https://www.bibliotecadigital.unicamp.br/bd/index.php/detalhes-material/?code=109685%0a','petrobrás, política, compras, entidades, públicas'),
array('3.1','Compras públicas para inovação (CPI)','QUANDO O GOVERNO É O MERCADO: COMPRAS GOVERNAMENTAIS E INOVAÇÃO EM SERVIÇOS DE SOFTWARE','MOREIRA, Marina Figueiredo; VARGAS, Eduardo Raupp de','2012','https://revistas.usp.br/rai/article/view/79268','Inovação em Serviços; Serviços de Software; Compras Públicas; Indução de Inovações'),
array('3.1','Compras públicas para inovação (CPI)','O papel dos processos de compras públicas nos projetos de PD&I : um estudo de caso nos projetos de inovação do CDT/UnB','Cortinhas, Luciana Maria de Oliveira','2020','https://repositorio.unb.br/handle/10482/36778',' Compras públicas Pesquisa e desenvolvimento Ciência, tecnologia e inovação Inovação tecnológica'),
array('3.1','Compras públicas para inovação (CPI)','Formulação de problemas complexos em compras públicas de inovação : (des) problematizando os objetos de aquisição pelo Estado','França, Joysse Vasconcelos','2024','https://repositorio.unb.br/handle/10482/50056','Compras Públicas, Inovação, Problemas Complexos'),
array('3.1','Compras públicas para inovação ','Compras públicas de inovação pelo governo federal: diferenças entre modalidades de compra',' Mendes, M. E. M.','2018','https://repositorio.fei.edu.br/handle/FEI/228','Licitação pública; Desenvolvimento organizacional; Compra pública; Modalidade de compra'),
array('3.1','Compras públicas para inovação','Compras públicas para inovação e o desenvolvimento: um diagnóstico jurídico-institucional das encomendas tecnológicas no Brasil','Pimenta Filho, Luiz Cláudio','2021','https://hdl.handle.net/10438/30779','Encomendas tecnológicas, Inovação; Direito e Desenvolvimento; Compras públicas'),
array('3.2','GovTech e soluções digitais','Inovação em compras públicas: utilização de soluções low-code como facilitador da transformação digital em uma empresa pública federal  ','LOSADA, André Gustavo Gomes','2024','https://rigeo.sgb.gov.br/handle/doc/24788','inovação, compras, públicas, utilização, soluções'),
array('3.2','GovTech e soluções digitais','O planejamento-orçamento das compras públicas na gestão digital: integração de dados no ciclo das políticas públicas','Barbosa, Cicero Alencar','2023','https://bdtd.ucb.br:8443/jspui/handle/tede/3197','públicas, integração, plano, planejamentoorçamento, compras'),
array('3.2','GovTech e soluções digitais','Desenvolvimento de um framework para compras públicas inovadoras a partir de uma revisão integrativa de literatura  ','Santos, Diego Manoel de Santana Oliveira','2024','https://repositorio.utfpr.edu.br/jspui/handle/1/35738','desenvolvimento, framework, compras, inovadoras, públicas'),
array('3.2','GovTech e soluções digitais','Contribuição da Tecnologia Blockchain em Processos de Compras Públicas sob a Ótica dos Custos de Transação: Um Estudo de Caso.','Andrade et al.,','2023','https://periodicos.ufpe.br/revistas/index.php/gestaoorg/article/view/251443','blockchain, processos, compras, custos, transação'),
array('3.2','GovTech e soluções digitais','O uso da tecnologia blockchain para compras públicas sustentáveis ​​de obras rodoviárias','Giamberardino, Guilherme GonçalvesGadda, Tatiana Maria CecyNagalli, André','2024','https://doi.org/10.1590/0034-761220230073','blockchain, públicas, sustentáveis, obras, rodoviárias'),
array('3.2','GovTech e soluções digitais','Um modelo de e-marketplace para compras públicas eficazes com o uso de inteligência artificial generativa.','Alencar et al., ','2024','https://sisbib.emnuvens.com.br/direitosegarantias/article/view/2496','emarketplace, generativa, modelo, compras, públicas'),
array('3.2','GovTech e soluções digitais','Inovações em contratações públicas uma revisão sistemática sobre o e-marketplace','Costa, Fabricio Barbosa da; Sano, Hironobu','2025','https://periodicos.ufes.br/ppgadm/article/view/43258','públicas, revisão, inovações, contratações, sistemática'),
array('3.2','GovTech e soluções digitais','Enhancing BIM implementation in Spanish public procurement: A framework approach','Pérez-García et al.','2024','https://www.cell.com/heliyon/fulltext/S2405-8440(24)06681-7?_returnURL=https%3A%2F%2Flinkinghub.elsevier.com%2Fretrieve%2Fpii%2FS2405844024066817%3Fshowall%3Dtrue','públicas, compras, melhorar, implementação, espanholas'),
array('3.2','GovTech e soluções digitais','Evaluating Corruption-Prone Public Procurement Stages for Blockchain Integration Using AHP Approach','ADJORLOLO et al.,  ','2025','https://www.mdpi.com/2079-8954/13/4/267','tecnologia blockchain; compras públicas; corrupção; transparência e responsabilização; processo de hierarquia analítica; Gana '),
array('3.2','GovTech e soluções digitais','Compras governamentais na administração pública 4.0: uma revisão sistemática','COSTA, Fabrício Barbosa & ARRAIS, Thalia Cléo Felizardo','2025','https://doi.org/10.55905/cuadv17n1-012','Contratações governamentais; governo inteligente; tecnologias inteligentes; inovação; administração pública 4.0.'),
array('3.2','GovTech e soluções digitais','Digital Transformation in Public Procurement: Blockchain Solutions and Legal Frameworks','MUTANGILI, Solomon Kyalo','2025','https://doi.org/10.53819/81018102t2456','Blockchain, compras públicas, contratos inteligentes, transparência, estrutura legal.'),
array('3.2','GovTech e soluções digitais','As estratégias do governo digital no Brasil: o caso das compras e contratações públicas','GREGORIO, Rosenilde Garcia dos Santos','2024','http://hdl.handle.net/10400.22/26824','Desburocratização;  Compras públicas; Governança digital; Transformação digital; Nova lei de licitações (NLLC)'),
array('3.3','Inteligência artificial e automação','Transformação digital aplicada no planejamento de demanda baseado em inteligência artificial  ','Correia, Caveiro Fernanda','2022','https://repositorio.fei.edu.br/handle/FEI/4585','transformação, digital, aplicada, planejamento, demanda'),
array('3.3','Inteligência artificial e automação','La inteligencia artificial: una herramienta que revoluciona la compra pública   Artificial Intelligence: A tool that revolutionizes public procurement ','Colmachi, Juan Francisco Diaz','2023','https://journal.nuped.com.br/index.php/revista/article/view/1253','artificial, inteligencia, herramienta, revoluciona, compra'),
array('3.3','Inteligência artificial e automação','Big Data como herramienta en la planeación contractual: una descarga obligada','Trejos López, Manuel David','2025','https://repository.urosario.edu.co/handle/10336/45344','Big Data, contratação estatal, princípio de planejamento.'),
array('3.3','Inteligência artificial e automação','Artificial Intelligence in Public Procurement: Legal Frameworks, Ethical Challenges, and Policy Solutions for Transparent and Efficient Governance','AYIBAM, Joanna Nyaposowo','2025','http://gnosipublishers.com.ng/index.php/alkebulan/article/view/24','Compras públicas; Inteligência Artificial; Transparência; Detecção de fraudes; Marcos legais'),
array('3.3','Inteligência artificial e automação','Assessing the value of artificial intelligence (AI) in governmental public procurement','ANDERSSON et al.,  ','2025','https://doi.org/10.1108/JOPP-05-2024-0057','Compras públicas; Inteligência Artificial; Inovação; Planejamento; Setor público.'),
array('3.3','Inteligência artificial e automação','Classificação de fraudes em licitações públicas através do agrupamento de empresas em conluios','GALVÃO JÚNIOR, David P.; SOUSA FILHO, Gilberto F. de; CABRAL, Lucídio dos Anjos F.','2023','https://sol.sbc.org.br/index.php/wcge/article/view/24861','Detecção de conluios; Aprendizagem de máquina; Análise de agrupamentos, contratações públicas'),
array('3.3','Inteligência artificial e automação','Regulamentação da IA (Inteligência Artificial) na administração pública brasileira: análise do Projeto de Lei n° 21 de 2020 e Projeto de Lei n° 2338 de 2023','COLOMBELLI, Wagner Godinho','2024','https://dspace.unila.edu.br/handle/123456789/7950',' Regulamentação da IA; Responsabilidade civil; Estratégia Brasileira de Inteligência Artificial (EBIA); Projeto de Lei N° 21 de 2020; Projeto de Lei N° 2338 de 2023.'),
array('3.3','Inteligência artificial e automação','Machine Learning Applied to Open Government Data for the Detection of Improprieties in the Application of Public Resources','VAQUEIRO et al.,','2023','https://dl.acm.org/doi/10.1145/3592813.3592908','Compras públicas; Transparência; Mineração de textos; Aprendizado de máquina; Controle social '),
array('3.3','Inteligência artificial e automação','O uso da inteligência artificial na atividade de compliance: riscos e benefícios','DIAS, Cíntia Coelho; FERREIRA, Roberta Valiatti','2023','https://www.researchgate.net/publication/374929195_O_uso_da_inteligencia_artificial_na_atividade_de_compliance_riscos_e_beneficios','inteligência artificial; compliance; compras públicas;  vieses de algorítmicos; iniciativas legislativas.'),
array('3.3','Inteligência artificial e automação','Inteligência artificial utilizada na auditoria de recursos públicos destinados para alimentação escolar','SILVA et al.,','2025','https://doi.org/10.5433/1984-7939.2025.v10.49741 ','Inteligência Artificial, Auditoria pública, Recursos públicos, Alimentação escolar, Análise de dados'),
array('3.3','Inteligência artificial e automação','A fiscalização dos contratos administrativos na nova Lei de Licitações: dos carimbos à inteligência artificial','OLIVEIRA, Rafael Carvalho Rezende','2024','https://sgpsolucoes.com.br/site/wp-content/uploads/2024/10/70-SLC-Janeiro-2024-Solucoes-Autorais-07.pdf','Fiscalização contratual, governança pública, gestão por competências, inteligência artificial, eficiência administrativa'),
array('3.3','Inteligência artificial e automação','Possíveis Implicações da Aplicação Combinada da Blockchain, Smart Contract e Inteligência Artificial nas Contratações e no Orçamento Público','BURITE, Alexsandro Souza; SACRAMENTO, Ana Rita Silva; RAUPP, Fabiano Maury.','2023','https://revista.cgu.gov.br/Revista_da_CGU/article/view/534','Blockchain, smart contract, inteligência artificial, contratações públicas, governança digital.'),
array('3.3','Inteligência artificial e automação','A Inteligência Artificial e seus impactos nas contratações e aquisições necessárias para o preparo e emprego da Força Aérea Brasileira','AMBROSIO, Lidia Borges','2023','https://www.redebia.dirensri.fab.mil.br/Direns_RI/acervo/detalhe/91730','Inteligência artificial, Licitações e contratos, TCU, Força Aérea Brasileira'),
array('3.3','Inteligência artificial e automação','Previsão de fraude em licitações no Brasil','MORAIS, Vinícius Souza','2024','https://repositorio.ufu.br/handle/123456789/43695','Licitação Pública; Fraudes; Serviço Público; Inteligência Artificial; Random Forest'),
array('3.3','Inteligência artificial e automação','Controle das compras públicas, inovação tecnológica e inteligência artificial : o paradigma da administração pública digital e os sistemas inteligentes na nova lei de licitações e contratos administrativos','SCHIEFLER, Eduardo André Carvalho','2021','https://repositorio.unb.br/handle/10482/43103','Contratações públicas; Inovação; Inteligência artificial; Direito e tecnologia'),
array('3.3','Inteligência artificial e automação','Usando Inteligência Artificial (IA) na classificação de licitações: um caso prático','VOLKMER, Glauber','2022','https://repositorio.enap.gov.br/handle/1/7397','licitação;  administração pública;  inteligência artificial'),
array('3.3','Inteligência artificial e automação','A Inteligência Artificial no combate à corrupção em licitações Públicas ','MOURA, Gustavo Lima; SANTOS NETO, Abílio Torres dos','2024','https://periodicos.ufms.br/index.php/EIGEDIN/article/view/20558/13934','Inteligência Artificial; CGE-PR; Licitações Públicas; Projeto Harpia.'),
array('3.3','Inteligência artificial e automação','Leveraging Artificial Intelligence in Contracting: A Digital Transformation for Public Institutions','CUNHA et al.,','2024','https://scispace.com/papers/leveraging-artificial-intelligence-in-contracting-a-digital-2zswu5bwal','Transformação digital, compras públicas, inteligência artificial, órgãos públicos.'),
array('3.3','Inteligência artificial e automação','Turning Tenders into Tinder: How AI and Open Data can spark Bidding Matches','KLASSEN et al.,  ','2024','https://dl.gi.de/items/6ebf2d34-6934-43ef-8041-5551c5d78bfc','Dados abertos, compras públicas, aprendizado de máquina, inteligência artificial.'),
array('3.3','Inteligência artificial e automação','Desenvolvimento de um Painel Integrado Inteligente para Auxílio na Identificação de Fraudes em Processos de Compras Públicas','PIEROTTI et al., ','2024','https://sol.sbc.org.br/index.php/sbbd_estendido/article/view/30808','Integração de dados, Prevenção de fraudes, Inteligência artificial'),
array('3.3','Inteligência artificial e automação','Leveraging Artificial Intelligence and Machine Learning for Predictive Bid Analysis in Supply Chain Management: A Data-Driven Approach to Optimize Procurement Strategies','KALISETTY, Srinivas & SEENU, Aaluri','2024','https://www.academia.edu/126836925/Leveraging_Artificial_Intelligence_and_Machine_Learning_for_Predictive_Bid_Analysis_in_Supply_Chain_Management_A_Data_Driven_Approach_to_Optimize_Procurement_Strategies','Análise Preditiva de Licitações, Inteligência Artificial (IA), Aprendizado de Máquina'),
array('3.3','Inteligência artificial e automação','Intelligent Methodologies for Preparing Reference Prices in Brazilian Public Biddings','FARIA, Eduardo Marques Braga de; SILVA, Gilton José Ferreira da; SOARES, Michel S.','2025','https://sol.sbc.org.br/index.php/sbsi/article/view/34340','Licitação Pública, Preço de Referência, Engenharia, Software, Administração Pública, Inteligencia Artificial'),
array('3.3','Inteligência artificial e automação','A inteligência artificial nos órgãos constitucionais de controle de contas da administração pública brasileira*','BITENCOURT, Caroline Muller; MARTINS, Luiza Helena Nicknig','2023','https://revistas.ufpr.br/rinc/article/view/e253','inteligência artificial; administração pública digital; controle da administração; tribunal de contas; Brasil'),
array('3.3','Inteligência artificial e automação','IA para identificar fraude e corrupção em compras públicas',' Menezes, Ana Paula Veras Carvalho','2021','https://repositorio.idp.edu.br//handle/123456789/4176','Inteligência artificial;Controle externo;Tribunal de Contas da União'),
array('4.1','Planejamento de compras','A inovação como um vetor de mudança no processo de compra pública da agricultura familiar oriunda do PNAE  ','Oliveira Júnior, José Mendes de','2021','https://bdtd.ibict.br/vufind/Record/UNB_e83456194b73722012f4e335a3152c14','agricultura, familiar, pnae, entre, inovação'),
array('4.1','Planejamento de compras','A introdução da circularidade nas compras públicas através da nova lei de licitações: o papel da governança e do planejamento nos processos licitatórios  ','Amorim, Rodrigo Mascarenhas','2024','https://bdtd.ibict.br/vufind/Record/UFS-2_a5920acc50d52fefa3656abe7c8bb0f8',' compras, públicas, governança, planejamento'),
array('4.1','Planejamento de compras','Compras públicas: estrutura organizacional, atibutos transacionais e comportamentais no desempenho da função na Receita Federal do Brasil  ','Pinto, Arthur Vinicius da Costa Ferreira','2023','https://bdtd.ibict.br/vufind/Record/UFBA-2_2ca85ceb71a774d66996cc599d1a6378','compras, desempenho, função, estrutura, transacionais'),
array('4.1','Planejamento de compras','Eficiência nas compras públicas: análise nos requisitos de contratação a partir do estudo técnico preliminar da lei de licitações e contratos administrativos na secretaria de segurança pública do Amazonas  ','Paiva, Anézio Brito de.','2025','https://biblioteca.sophia.com.br/terminalri/9575/acervo/detalhe/593315','estudo, requisitos, contratação, técnico, preliminar'),
array('4.1','Planejamento de compras','Instrumentalização das compras públicas da merenda escolar para o desenvolvimento no âmbito municipal de Fortaleza em função da garantia do direito à alimentação  ','Bastos, Bruno Costa','2023','https://bdtd.ibict.br/vufind/Record/UFOR_cc6a8bb0bcaa62d91fd7d94e84883fe0','compras, públicas, merenda, escolar, desenvolvimento'),
array('4.1','Planejamento de compras','Método simplificado para implementação de um sistema de gestão do conhecimento no planejamento das compras públicas brasileiras  ','Demarchi, Murilo Pedro','2023','https://bdtd.ibict.br/vufind/Record/UFSC_572d80a90846f37b8455dc3a00be6d42','implementação, sistema, gestão, conhecimento, planejamento'),
array('4.1','Planejamento de compras','Centralização de compras públicas: uma proposta para o Instituto Federal Sul-rio-grandense  ','Silveira, Juliana Passos','2021','https://bdtd.ibict.br/vufind/Record/UFPL_4fb203e546d40edc53f27ee512607521','compras, centralização, instituto, federal, sulriograndense'),
array('4.1','Planejamento de compras','Avaliação da Política Pública de Centralização das Compras Públicas do Parque Estadual de Veículos','de Aguiar, Marta Rodrigues Casqueiro Maçaroco Pimenta','2022','https://repositorio.ulisboa.pt/bitstream/10400.5/25572/2/02.Resumo_Abstract_%20Mestrado_GPP_MPA.pdf','política, pública, centralização, compras, parque'),
array('4.1','Planejamento de compras',' Configurações explicativas do desenvolvimento da resiliência nas redes de suprimentos da administração pública ','FREITAS et al., ','2023','https://www.scielo.br/j/rap/a/8D4kvQrP7S7dX65nvYGj6WM/?lang=pt','resiliência; teoria do capital social; compras públicas; análise de conteúdo de Honey; análise de coincidência'),
array('4.1','Planejamento de compras','Implantação de um plano de ação para a melhoria da performance do setor de licitações de uma instituição pública de ensino','Pugliese, Ana Carolina','2019','https://rima.ufrrj.br/jspui/handle/20.500.14407/15100','Administração Pública;Compras públicas;Gestão da qualidade;Indicadores de desempenho.;'),
array('4.1','Planejamento de compras',' A introdução da circularidade nas compras públicas através da nova lei de licitações: o papel da governança e do planejamento nos processos licitatórios','Amorim, Rodrigo Mascarenhas','2024','https://ri.ufs.br/jspui/handle/riufs/19483','Administração pública Economia circular Compras Governança pública Licitação pública Sustentabilidade Circularidade'),
array('4.2','Gestão de estoques','Compras públicas compartilhadas: uma análise sobre os principais antecedentes ao processo de formação de redes de compras entre hospitais públicos  ','Limberger, Anderson Luiz','2022','https://bdtd.ibict.br/vufind/Record/USIN_e2f2c8f5cae70e37d13cde9e9b44cc52','compras, compartilhadas, principais, antecedentes, processo'),
array('4.2','Gestão de estoques','Avaliação da gestão de compras públicas de tecnologia da informação na Universidade de Brasília  ','Tolentino, Luciano Cordova','2021','https://bdtd.ibict.br/vufind/Record/UNB_395c4a4868ae0d64d9ae614670ea3f00','gestão, compras, públicas, avaliação, tecnologia'),
array('4.2','Gestão de estoques','Gestão de compras públicas: uma proposta para melhorar o processo de compras em um setor de saúde pública  ','Reis, José Hildebrando Oliveira dos','2017','https://bdtd.ibict.br/vufind/Record/UFAM_3f09f4810c7a1599bc6d05fabe9604b0','compras, proposta, processo, saúde, estudo'),
array('4.2','Gestão de estoques','Inovação organizacional em compras públicas: análise da acurácia do planejamento participativo do Instituto Federal do Triângulo Mineiro de 2013 a 2016','Avigo, Ricardo Oliveira','2018','https://bdtd.uftm.edu.br/handle/tede/748','inovação, organizacional, compras, públicas, análise'),
array('4.2','Gestão de estoques','Mapeamento do processo de compras públicas: uma ferramenta para gestão de materiais em saúde  ','Faria, Suzi da Silva','2017','https://bdtd.ibict.br/vufind/Record/UFF-2_eafd60c141cb46ce7a5e6c678e737f79','mapeamento, compras, gestão, material, processo'),
array('4.3','Eficiência operacional','Appraisal of the efficiency of the tender adjudication method in public procurement of construction projects in South Africa ','Babalwa Damba','2025','https://core.ac.uk/download/pdf/11777509.pdf','Adjudicação de licitações, contratação pública, construção civil, eficiência, transparência, África do Sul, PPPFA, Western Cape'),
array('4.3','Eficiência operacional','Governança na segurança pública: um estudo de caso das compras públicas na intervenção federal no Estado do Rio de Janeiro.  ','Viana, Bruno Campos','2023','https://repositorio.enap.gov.br/bitstream/1/7623/1/Disserta%C3%A7%C3%A3o%20Bruno%20Viana%20-%20Vers%C3%A3o%20com%20ficha.pdf','compras, públicas, intervenção, federal, estado'),
array('4.3','Eficiência operacional','Efetividade das compras públicas: atos e exigências adequados à contratação.  ','Rabello, Luciana de Amorim','2022','https://repositorio.fgv.br/items/d9cd916c-d901-4882-b3d4-c9cc0b39c9f6','compras, públicas, efetividade, atos, exigências'),
array('4.3','Eficiência operacional','Gestão do conhecimento no planejamento das compras públicas do Instituto Nacional da Propriedade Industrial  ','Ferreira, Thaís Xavier de Paiva','2025','https://bdtd.ibict.br/vufind/Record/UFF-2_ba9488e682d4b6e8cbfb09fef6eaa539','conhecimento, planejamento, gestão, compras, públicas'),
array('4.3','Eficiência operacional','Inovação em compras públicas : análise e proposições para a etapa preliminar do processo licitatório do executivo catarinense  ','Bayestorff, Luana','2024','https://repositorio.udesc.br/entities/publication/3638ae1a-e638-4ced-aef4-c6f750b23fee','inovação, compras, públicas, preliminar, análise'),
array('4.3','Eficiência operacional','Proposta e aplicação de uma sistemática baseada na gestão do conhecimento para a melhoria do processo de compras públicas  ','Kariyado, Monica Yukie','2016','https://bdtd.ibict.br/vufind/Record/SCAR_9a9757465950ac8e7c67bafc4e1b2589','aplicação, sistemática, baseada, gestão, conhecimento'),
array('4.3','Eficiência operacional','Compras públicas inteligentes: um modelo de análise estratégica para a gestão das compras públicas – estudo de caso do instituto do meio ambiente e dos recursos hídricos do distrito federal  ','Terra, Antonio Carlos Paim','2016','https://repositorio.bc.ufg.br/tede/items/24b4b4cc-01fe-4810-8e92-2c299e37297a','compras, públicas, modelo, análise, estratégica'),
array('4.3','Eficiência operacional','Diretrizes para a concepção de indicadores de desempenho nas compras públicas de uma entidade de ensino público do estado do Rio de Janeiro  ','Melo, Luiz Lima de','2023','https://bdtd.ibict.br/vufind/Record/UERJ_26430ad70632b213133ee11d9a04f9b1','indicadores, desempenho, compras, públicas, ensino'),
array('4.3','Eficiência operacional','Public Logistics and Its Possible Application in Local Government Administration','Kauf, Sabina ','2014','https://yadda.icm.edu.pl/baztech/element/bwmeta1.element.baztech-726f4aa1-1a9f-446d-86d2-aca0e152c104','public, logistics, possible, application, local'),
array('4.3','Eficiência operacional','Procedimento gerencial para redução de desperdício de recursos em contratações públicas em uma instituição de ensino','Santos Galdino, Rondinelle Idalecio dos','2019','https://yadda.icm.edu.pl/baztech/element/bwmeta1.element.baztech-726f4aa1-1a9f-446d-86d2-aca0e152c104','Dispensa de licitação, Compras públicas, Pequeno valor, Desburocratização, Eficiência administrativa,  Contratações públicas ágeis'),
array('4.3','Eficiência operacional','Avaliação da celeridade dos processos de licitação, via pregão eletrônico, de uma Empresa Pública de Equipamentos Industriais','Gomes, Gabriella da Silva','2023','https://rima.ufrrj.br/jspui/handle/20.500.14407/15261','Avaliação, pregão eletrônico, licitação, celeridade'),
array('4.3','Eficiência operacional','Desenvolvimento de um plano de melhorias para o processo de compras de uma empresa de economia mista',' Souza, Alexandra Gomes de','2022','https://rima.ufrrj.br/jspui/handle/20.500.14407/15275','Compras Públicas;Melhoria de Processos;Gestão da Qualidade;BP'),
array('4.3','Eficiência operacional','Modelagem do gerenciamento de contrato da construção civil na administração pública usando a metodologia EKD',' Souza, Alexandra Gomes de','2022','https://repositorio.ufc.br/handle/riufc/27149','Contratos - Administração; Obras públicas; Licitação pública; EKD methodology;Rules'),
array('4.3','Eficiência operacional','Reputação e eficiência: a inovação da Lei de Licitações e Contratos Administrativos com a avaliação de desempenho','Wakay, Leandro ','2025','https://repositorio.unb.br/handle/10482/17810','Assimetria Informacional, avaliação de desempenho,execução contratual'),
array('4.3','Eficiência operacional','EFICIÊNCIA NAS COMPRAS PÚBLICAS: ANÁLISE NOS REQUISITOS DE CONTRATAÇÃO A PARTIR DO ESTUDO TÉCNICO PRELIMINAR DA LEI DE LICITAÇÕES E CONTRATOS ADMINISTRATIVOS NA SECRETARIA DE SEGURANÇA PÚBLICA DO AMAZONAS','Paiva, Anésio Brito de ','2025','https://biblioteca.sophia.com.br/terminalri/9575/acervo/detalhe/593315','Contratos Administrativos; Estudo Tecnico Preliminar; Eficiência nas Compras'),
array('4.3','Eficiência operacional','Mapeamento de processos de contratação de serviços terceirizados e aprendizagem coletiva para adotar a Instrução Normativa 05/2017: subsidiando capacitação no IFRJ - Campus Paracambi',' Siqueira, Ronian Grossi da Silva','2020','https://rima.ufrrj.br/jspui/handle/20.500.14407/15330','Aprendizagem coletiva;Autarquia federal;Pesquisa-Ação;Instrução Normativa 05/2017;Mapeamento de Processos de atividades;Contratação de serviços públicos'),
array('4.3','Eficiência operacional','Compras eletrônicas e incentivos à eficiência no setor público: evidências do Estado de São Paulo',' Santos, Carolina Tojal Ramos dos','2018','https://www.teses.usp.br/teses/disponiveis/12/12138/tde-17082018-162125/pt-br.php','Administração pública Compras públicas Corrupção Eficiência'),
array('4.3','Eficiência operacional','Análise da nova sistemática de concessão de diárias e passagens: desafios aos gestores militares','Cavalcante, Pedro Luiz Costa','2021','https://repositorio.idp.edu.br/handle/123456789/3006','SCDP;Forças armadas;Inovação;Gestão pública'),
array('4.3','Eficiência operacional','Licitação pública: fatores que influenciam a celeridade e a eficiência','Costa Júnior, José Carlos Pereira da','2016','https://repositorio.ufscar.br/handle/20.500.14289/8745','Licitação pública, Celeridade, Economicidade, Objetividade, Pregão, Competitive bidding,'),
array('4.3','Eficiência operacional','As compras públicas e a fase preparatória do processo licitatório da lei nº 14.133/2021: Uma abordagem à luz da teoria dos custos de transação  ','Cruz, Luiz Guilherme Soares','2022','https://repositorio.ufersa.edu.br/handle/prefix/8656','Compras públicas; Administração Pública; Licitação; Custos de transação; Eficiência'),
array('4.3','Eficiência operacional','Pregões eletrônicos: estudo em universidade pública de Goiás','Dias, Sebastião Carlos','2023','http://repositorio.jesuita.org.br/handle/UNISINOS/12946','Licitações públicas; Pregão eletrônico; Universidade Pública; Eficiência econômica; Aquisições de bens comuns'),
array('5.1','Compras como instrumento de política pública','La Política de Compra Pública como Estímulo a la Innovación y el Emprendimiento','Zabala-Iturriagagoitia,Jon Mikel ','2017','https://repositorio.idp.edu.br/handle/123456789/3006','Compra pública, Inovação, Empreendedorismo, Estratégia pública, Estímulo à inovação'),
array('5.1','Compras como instrumento de política pública','Government Contracting of Services to NGOs: An Analysis of Gradual Institutional Change and Political Control in China','Martin, Philippe ','2023','https://ruor.uottawa.ca/items/10b89e59-1521-408c-8e7b-1117185fbcad','China, ONG, provisão não estatal de bem-estar, contratação governamental, sociedade civil, política social'),
array('5.1','Compras como instrumento de política pública','Los Bonos con Impacto Social en el contexto de la reforma a la ley de contratación pública: retos y oportunidades.','CORREDOR CASTELLANOS, Guillermo Rodrigo','2018','http://www.scielo.org.co/scielo.php?script=sci_arttext&pid=S0122-98932018000200129&lng=en&nrm=iso','Social impact, inovação, gestão pública, contratação pública.'),
array('5.2','Fomento à economia local e regional','Compras públicas: estratégia e instrumento para a gestão do desenvolvimento local','Caldas, Eduardo de Lima','2023','https://www.scielo.br/j/inter/a/WRgvSX9PXzps3QhFy8D7rbG/?lang=pt','compras, públicas, gestão, desenvolvimento, local'),
array('5.2','Fomento à economia local e regional','O poder de compras e o desenvolvimento econômico local: a avaliação da política pública de compras governamentais no município de Ituverava/SP','Cardoso, Mateus Scapim','2017','https://repositorio.unesp.br/entities/publication/20bb0dbf-47ff-45b4-af5c-ea8df1889dfb','compras, poder, desenvolvimento, local, econômico'),
array('5.3','Apoio a micro e pequenas empresas','Participação de pequenas empresas locais nas compras públicas  ','Dutra, Cristiano','2019','https://bdtd.ibict.br/vufind/Record/UFES_e5e5570edb1b3db27f4b6f76c64b1841','participação, empresas, pequenas, locais, compras'),
array('5.3','Apoio a micro e pequenas empresas','Política pública de fomento às micro e pequenas empresas pelo poder das compras públicas no Estado de Goiás: controle externo pelo TCE/GO (2006-2019)  ','Barzellay, Larissa Sampaio','2021','https://repositorio.bc.ufg.br/tedeserver/api/core/bitstreams/cc0cc57d-ade9-4b3d-a486-dd91bed01b45/content','públicas, fomento, micro, pequenas, empresas'),
array('5.3','Apoio a micro e pequenas empresas','Política de inclusão de microempresas e empresas de pequeno porte: avaliação das compras públicas na Universidade Federal do Ceará-UFC com base na Lei Complementar nº 123/06  ','Gonçalves, Paulo Henrique Leite','2020','https://bdtd.ibict.br/vufind/Record/UFC-7_6a9da8a75603986dc9fd6be83df6f7f3','microempresas, empresas, pequeno, porte, públicas'),
array('5.3','Apoio a micro e pequenas empresas','Compras Públicas e Desenvolvimento Local: Micro e Pequenas Empresas Locais nas Licitações de uma Universidade Pública Mineira','CHAVES, Fernanda Rodrigues Drumond; BERTASSI, André Luís; SILVA, Gustavo Melo.','2019','https://regepe.org.br/regepe/article/view/867','Compras governamentais, Desenvolvimento local, Micro e pequenas empresas, Licitações públicas, Políticas públicas, Fomento regional '),
array('6.1','Auditorias de conformidade e desempenho','O combate ao desperdício no gasto público; uma reflexão baseada na comparação entre os sistemas de compra privado, público federal norte-americano e brasileiro','Motta, Alexandre Ribeiro','2010','https://repositorio.unicamp.br/acervo/detalhe/771507','combate, gasto, público, desperdício, reflexão'),
array('6.1','Auditorias de conformidade e desempenho','Controle de Práticas Concorrenciais Abusivas em Contratações Públicas: Uma Abordagem Comparada ao Direito Internacional e Europeu','dos Santos, Ruth Maria Pereira','2022','https://repositorio.ulisboa.pt/handle/10451/57090','controle, práticas, concorrenciais, abusivas, contratações'),
array('6.1','Auditorias de conformidade e desempenho','Criminal compliance: A mechanism to help prevent corruption in State procurement','DE LA CRUZ, Jaime Gerónimo','2023','https://revistas.pj.gob.pe/revista/index.php/ropj/article/view/705','Compliance penal; Corrupção; Contratações públicas; Meritocracia; Transparência'),
array('6.1','Auditorias de conformidade e desempenho','Essays on Political Corruption','GRAIFF GARCIA, Ricardo','2021','https://etd.ohiolink.edu/acprod/odb_etd/etd/r/1501/10?clear=10&p10_accession_num=osu1628326470819617','corrupção política, comportamento eleitoral, corrupção legislativa, compras públicas, auditoria governamental, Brasil '),
array('6.1','Auditorias de conformidade e desempenho','Controle interno da administração pública sob a constituição de 1988 e sua eficiência para a transparência e o enfrentamento da corrupção','Ungaro, Gustavo Gonçalves','2019','https://www.teses.usp.br/teses/disponiveis/2/2134/tde-07082020-005136/pt-br.php','Controle da Administração Pública Controle externo Controle interno Controle social Direito Integridade Organização do Estado Participação Prevenção da corrupção Transparência'),
array('6.2','Indicadores de risco e prevenção de fraudes','Avaliação experimental de um classificador para apoiar a detecção de fraudes em compras públicas  ','Fontes, Raphael Silva','2022','chrome-extension://efaidnbmnnnibpcajpcglclefindmkaj/https://ri.ufs.br/bitstream/riufs/15098/2/RAPHAEL_SILVA_FONTES.pdf','classificador, compras, públicas, avaliação, experimental'),
array('6.2','Indicadores de risco e prevenção de fraudes','Inteligência artificial para identificação de indícios de fraude e corrupção em compras públicas no TCU  ','Menezes, Ana Paula Veras Carvalho.','2021','https://repositorio.idp.edu.br/handle/123456789/3981','corrupção, públicas, inteligência, artificial, identificação'),
array('6.2','Indicadores de risco e prevenção de fraudes','Gestão de riscos em compras públicas: um estudo na Central de Compras do Estado da Paraíba  ','Soares, João Cláudio Araújo','2020','https://bdtd.ibict.br/vufind/Record/UFPB_584d4105212a3fb03adec417bb7bd05d','compras, gestão, riscos, públicas, estudo'),
array('6.2','Indicadores de risco e prevenção de fraudes','Transparência em compras públicas: proposta de um índice da transparência na gestão de compras públicas aplicado aos websites de municípios brasileiros com mais de 100 mil habitantes  ','Soares, Laura Letsch','2013','https://bdtd.ibict.br/vufind/Record/UFSC_5a5efb6d1b41c0cb8fff9ad7193d9783','transparência, compras, públicas, websites, municípios'),
array('6.2','Indicadores de risco e prevenção de fraudes','Medindo a transparência das compras públicas com um índice: explorando o papel dos sistemas e instituições de e-GP','  Khorana et al.,   ','2024','https://www.sciencedirect.com/science/article/pii/S0740624X24000443?via%3Dihub','transparência, compras, públicas, medindo, índice'),
array('6.2','Indicadores de risco e prevenção de fraudes','Aumentar a transparência nas compras públicas: uma abordagem analítica baseada em dados','Felizzola et al., ','2024','https://www.sciencedirect.com/science/article/abs/pii/S0306437924000887?via%3Dihub','transparência, compras, públicas, dados, aumentar'),
array('6.2','Indicadores de risco e prevenção de fraudes','Detecção de conluio em licitações utilizando algoritmos de machine learning','Nunes, Leonardo Vieira','2024','https://repositorio.ufsc.br/handle/123456789/255797','detecção, conluio, licitações, algoritmos, machine'),
array('6.2','Indicadores de risco e prevenção de fraudes','Identificação Automática de Conluio em Pregões do Comprasnet com Aprendizado de Máquina   ','Souza, Rodrigo Vilela Fonseca de','2023','https://repositorio.cgu.gov.br/xmlui/handle/1/77575','pregões, comprasnet, identificação, automática, conluio'),
array('6.2','Indicadores de risco e prevenção de fraudes','Tecnologias da informação para a luta contra a corrupção: análise da contratação pública costarriquenha','HERRERA MURILLO et al.,','2023','https://www.scielo.br/j/rap/a/9XV5ZwDbTns98bBn9FJnjZc/?lang=es','corrupção; transparência; procuração pública; sistema sócio técnico; análise de rede'),
array('6.2','Indicadores de risco e prevenção de fraudes','Transparency in Tender Waivers in Local Governments During Emergency Situations','AQUINO et al.,','2023','https://www.scielo.br/j/rcf/a/n9gpfsM3xV63M9wqLdLxBpH/?lang=en','transparência; paradoxos; contratação pública; dispensa de licitação; pandemia'),
array('6.2','Indicadores de risco e prevenção de fraudes','Corruption in Public Procurement Market','MIZOGUCHI, Tetsuro; QUYEN, Nguyen Van.','2014','https://onlinelibrary.wiley.com/doi/10.1111/1468-0106.12084','Corrupção em licitações, propostas multidimensionais, propina, eficiência ex post, mercado externo.'),
array('6.2','Indicadores de risco e prevenção de fraudes','MAPEAMENTO DE PROCESSO E ANÁLISE DE RISCOS DE FRAUDE NA DISPENSA DE LICITAÇÃO EM RAZÃO DA COVID-19','AUGUSTO, Edna Hercules; PUTI, Raquel; SANTOS, Alexandre Silva','2021','https://www.redalyc.org/journal/7338/733876310007/','COVID-19, Dispensa de licitação, Gestão por Processos, Gestão de Riscos, Fraude.'),
array('6.2','Indicadores de risco e prevenção de fraudes','Relações entre doações de campanha, denúncias de corrupção e variação de preço nas licitações de obras públicas','Pereira, João Ricardo','2015','https://repositorio.unb.br/handle/10482/17810','Doação, campanha eleitoral, denúncia, preços'),
array('6.2','Indicadores de risco e prevenção de fraudes','Métodos de classificação supervisionados aplicados à identificação de fraudes de fornecedores do Estado do Rio de Janeiro','Sá, Tainá Ayres','2022','https://www.bdtd.uerj.br:8443/handle/1/19530','Finanças públicas – Auditoria – Legislação Licitação pública Fraude – Prevenção Fraude em licitações Indicadores Algoritmos de classificação'),
array('6.2','Indicadores de risco e prevenção de fraudes','Alocação de riscos e equilíbrio econômico-financeiro nas contratações públicas','Oliveira, Simone Zanotello de','2020','https://sapientia.pucsp.br/handle/handle/23353','Contratos administrativos Concessões administrativas - Brasil Avaliação de riscos Equilíbrio econômico Public contracts'),
array('6.3','Controle, Auditoria e Combate à Corrupção','O Acórdão 598/2018 - TCU-Plenário: promovendo transparência, eficiência e competitividade nas compras públicas por dispensa de Licitação - o caso das estatais Serpro e Dataprev  ','Amaral, Uender Ferreira','2024','https://bdtd.ibict.br/vufind/Record/FGV_33dfc093a83ea723a404021d7aac5284','acórdão, transparência, eficiência, públicas, dispensa'),
array('6.3','Papel dos órgãos de controle','Controle do TCU e políticas públicas de infraestrutura','Alves, Renato José Ramalho','2024','https://www.teses.usp.br/teses/disponiveis/2/2133/tde-21012025-155413/pt-br.php','controle, políticas, públicas, infraestrutura, estudo'),
array('6.3','Papel dos órgãos de controle','O dever de transparência no Tribunal de Contas da União','Magami Junior, Roberto Tadao','2019','https://tede2.pucsp.br/handle/handle/22933','Transparência na administração pública Documentos públicos - Leis e legislação - Brasil Informações governamentais - Controle de acesso - Brasil Direito à informação'),
array('6.3','Papel dos órgãos de controle','O controle externo de licitações exercido pelo Tribunal de Contas da União à luz da Lei nº 8.666/1993 e Lei nº 14.133/2021','Beznos, Clovis','2023','https://tede2.pucsp.br/handle/handle/39366','Controle externo Tribunal de Contas da União Administração Pública Licitações públicas Lei nº 8.666/1993 Lei nº 14.133/2021'),
array('6.3','Papel dos órgãos de controle','O controle exercido pelo Tribunal de Contas da União em matéria de contratações públicas e a Lei nº 14.133/2021','Fernandes, Ana Luiza Queiroz Melo Jacoby','2022','https://sapientia.pucsp.br/handle/handle/25909','Tribunal de Contas Licitações e contratações públicas Limites do papel do controle Lei nº 14.133/2021'),
array('7.1','Interpretação da legislação','O espaço de negociação do gestor público em contratações públicas flexíveis','Cukiert, Tamara','2024','https://www.teses.usp.br/teses/disponiveis/2/2134/tde-07012025-180315/pt-br.php','negociação, gestor, público, contratações, espaço'),
array('7.1','Interpretação da legislação','Regime jurídico de contratações públicas: por que insistir em modelo único e fragmentado?','Correia, Bianca Soares Silva','2023','https://www.teses.usp.br/teses/disponiveis/2/2134/tde-25082023-145101/en.php','regime, modelo, fragmentado, jurídico, contratações'),
array('7.1','Interpretação da legislação','Prorrogação do prazo de validade de patentes farmacêuticas por meio de ações judiciais: efeitos na centralização de compras','Julia Paranhos','2025','https://pubmed.ncbi.nlm.nih.gov/39813566/','patentes, farmacêuticas, prorrogação, prazo, validade'),
array('7.1','Interpretação da legislação','Algumas implicações dos instrumentos soft law da Agência Nacional de Compras Públicas','Berrio, Luisa Fernanda Bernal ','2023','https://revistas.urosario.edu.co/index.php/sociojuridicos/article/view/13253','Regulação, contratação estatal, Colômbia-Compra Eficiente, sanções de fato ou legais, soft law.'),
array('7.1','Interpretação da legislação','O impacto e os benefícios da nova Lei de Licitações 14.133/2021 sob a perspectiva dos empresários: uma abordagem usando machine learning','MONTE, Vinícius Freitas','2024','https://riu.ufam.edu.br/handle/prefix/7710','Benefícios;Empresários; PNCP; NLGLC; ME/EPP'),
array('7.1','Interpretação da legislação','A nova lei de licitações como promotora da maldição do vencedor','SIGNOR et al.,','2021','https://www.redalyc.org/journal/2410/241070355008/','lei nº 14.133/2021, licitação, maldição do vencedor, sobrepreço, preço inexequível.'),
array('7.1','Interpretação da legislação ','A Regulamentação da Nova Lei de Licitações: Definição da Lógica Institucional Prevalente em um Campo','COTRIM, Rosana Ramos &  RYNGELBLUM, Arnaldo L.   ','2022','https://www.scielo.br/j/rac/a/CMSWFJdfRtHVyCJrm8fdjzK/?lang=pt','lógica institucional; poder; regulamentação; licitação'),
array('7.1','Interpretação da legislação ','Declaração de inidoneidade prevista na Lei nº 14.133/2021 como sanção regida pelo Direito Administrativo Sancionador','Moraes, Tassiane de Fatima','2024','https://repositorio.pucsp.br/jspui/handle/handle/41225','Idoneidade, sancionador, Direito, Declaração'),
array('7.1','Interpretação da legislação ','Reflexos da lei brasileira de inclusão das pessoas com deficiência nas licitações e contratos administrativos: a obediência ao percentual de contratação da lei nº 8.213/1991 como imposição legislativa estatal efetivamente inclusiva','Sabino, Carla Danielle Barreto De Sousa','2022','https://repositorio.cruzeirodosul.edu.br/items/1e89df0b-e34c-4017-99fa-74ac3a62b6c6','Pessoas com deficiência, Inclusão no mercado de trabalho, Lei brasileira de inclusão das pessoas com deficiência'),
array('7.1','Interpretação da legislação ','A natureza jurídica dos dispute boards nos contratos administrativos','Rocha, Silvio Luis Ferreira da','2025','https://repositorio.pucsp.br/jspui/handle/handle/44091','Dispute boards Comitês de prevenção e resolução de conflitos Contratos administrativos Lei Geral de Licitações e Contratos Métodos alternativos de resolução de disputas'),
array('7.1','Interpretação da legislação ','As prestações extracontratuais e a manutenção do equilíbrio econômico-financeiro dos contratos de obras públicas','Negrini Neto, João','2018','https://repositorio.pucsp.br/jspui/handle/handle/44091',' Contratos administrativos Obras públicas - Finanças'),
array('7.1','Interpretação da legislação','Análise dos aditivos de prazo e valor dos contratos de obras da UFRRJ','Souto, Edenilson do Nascimento de','2023','https://rima.ufrrj.br/jspui/handle/20.500.14407/19754','Aditivos Contratuais;Obras Públicas;Administração Pública;Licitações;'),
array('7.1','1 Interpretação da legislação','AS IMPLICAÇÕES GERADAS ÀS COMPRAS PÚBLICAS COM A APROVAÇÃO DA NOVA LEI DE LICITAÇÕES E CONTRATOS','SANTOS,Everton Mendes dos; VIERA, Felipe Nunes','2023','https://periodicorease.pro.br/rease/article/view/11637','Principais alterações. Nova lei de Licitações. Aproveitamento do dinheiro público.'),
array('7.1','1 Interpretação da legislação','A nova lei de licitações: análise do estudo técnico preliminar como pilar estratégico para contratações públicas eficientes','LIMA, Pedro Emanuel Marques de','','https://repositorio.ufrn.br/items/aca62450-5a5f-4390-a400-6748ed4af7ec','Licitações públicas; Estudo Técnico Preliminar; Lei nº 14.133/2021; Gestão pública; Contratações públicas'),
array('7.1','1 Interpretação da legislação','Impactos da Nova Lei de Licitações nos Contratos Administrativos do Setor Público ','COLARES, Jose; MARQUES, Lucas Sollar','2024','https://periodicos.unir.br/index.php/readpublicas/article/view/7941','Licitações. Contratos Administrativos. LEI Nº 14.133/2021.'),
array('7.1','Interpretação da legislação','As licitações públicas financiadas pelo BID no Brasi','Andrade, Leonardo Aureliano Monteiro de','2007','Metadados do item: As licitações públicas financiadas pelo BID no Brasil','Licitação pública; Concorrência; Direito internacional público; Banco Interamericano de Desenvolvimento'),
array('7.1','Interpretação da legislação','Declaração de inidoneidade prevista na Lei nº 14.133/2021 como sanção regida pelo Direito Administrativo Sancionador','Moraes, Tassiane de Fatima','2024','https://sapientia.pucsp.br/handle/handle/41225','Sanção administrativa Declaração de inidoneidade Nova Lei de Licitações e Contratos Direito Administrativo Sancionador'),
array('7.1','Interpretação da legislação','A responsabilidade do ordenador de despesas diante da Lei Federal nº 14.133/21: a duração dos contratos administrativos e sua relação com as leis orçamentárias','Serrano, Antonio Carlos Alves Pinto','2025','https://sapientia.pucsp.br/handle/handle/44173',' Ordenador de despesas Lei Federal 14.133/21 Duração dos contratos administrativos Leis orçamentárias Responsabilidades'),
array('7.2','Aspectos Jurídicos e Regulatórios','O Acórdão 598/2018-TCU-Plenário: Promovendo Transparência, Eficiência e Competitividade nas Compras Públicas por Dispensa de Licitação - O Caso das Estatais Serpro e Dataprev ','Amaral, Uender Ferreira ','2024','https://repositorio.fgv.br/items/e7432013-5184-44dc-81ff-7fa9d388cf7c','Governo Digital, Eficiência, Economicidade, Abertura de preços, Competitividade'),
array('7.2','Aspectos Jurídicos e Regulatórios','Uma análise do registro de oportunidade nas licitações de tecnologia da informação e comunicação: perspectivas a partir do acórdão nº 2569/2018 – TCU/Plenário','Santana, Luana Nunes','2022','https://repositorio.idp.edu.br/handle/123456789/3663','Tecnologia da Informação e Comunicação. Cade. TCU Registro de Oportunidade'),
array('7.2','Jurisprudência e decisões relevantes','Responsabilidade por pesquisa de preços em licitações na visão do Tribunal de Contas da União','Gamito, Rondini Ingrid','2017','https://tede2.pucsp.br/handle/handle/37306','Licitação Pesquisa de preços Economicidade Eficiência Responsabilização Servidores'),
array('7.3',' Modelos de contratação e tipos de licitação ','Contratação de eventos públicos no Brasil: um estudo sobre instrumentos contratuais e melhores práticas a partir dos modelos dos carnavais de rua','Santos, Alessandro Matheus Marques','2021','https://repositorio.fgv.br/items/9659a451-f3f4-4cdf-a57a-c4f98911ce79','Direito Administrativo. Licitações Públicas. Processo Administrativo. Patrocínio. Eventos Públicos. Carnaval. '),
array('7.3','Modelos de contratação e tipos de licitação','Leilão aberto versus leilão selado: evidência com dados brasileiros de compras governamentais','Souza, Anderson Cardoso Pinto de','2016','https://www.teses.usp.br/teses/disponiveis/12/12138/tde-04032016-113403/pt-br.php','Leilão aberto, leilão selado, dados, compras governamentais'),
array('7.3','Aspectos Jurídicos e Regulatórios','Uma proposta de arquitetura para gestão de Atas de Registro de Preços','Magalhães, Fábio Arruda','2022','https://repositorio.ufrn.br/items/d2497f1c-2dfd-43b9-97b6-3fbaa20301d6','Ata de registro de preços Arquitetura de software Design science research Compras governamentais'),
array('7.3','Aspectos Jurídicos e Regulatórios','Pregões eletrônicos de bens e serviços administrativos: uma análise das causas de cancelamentos de itens no âmbito do Ministério da Saúde','Souza, Suzana de Abreu Ribeiro de','2024','https://repositorio.ufsc.br/handle/123456789/263779','Pregões eletrônicos, bens e serviços,administrativos'),
array('7.3',' Modelos de contratação e tipos de licitação','O modelo centralizado de compras como potencializador da melhoria dos processos de aquisições: estudos de caso da central de compras do Distrito Federal','Araújo, Grice Barbosa Pinto de','2017','https://repositorio.idp.edu.br/handle/123456789/2400','Administração Pública;Compras Públicas'),
array('7.3','Modelos de contratação e tipos de licitação',' A modelagem das licitações como medida de combate a cartéis: uma análise sob o enfoque da transparência pública','Araújo, Paulo Henrique Figueredo de','2019','https://repositorio.idp.edu.br/handle/123456789/2633','Compras Governamentais;LicitaçõesTransparência,Teoria dos leilões,Cartéis,Modelagem'),
array('7.3','Modelos de contratação e tipos de licitação','Centralização de compras públicas: as regulamentações à nova lei de licitações e contratos administrativos pelas capitais da região norte','Miró, Mariana Pucci','2024','https://repositorio.idp.edu.br/handle/123456789/5318','Nova Lei de Licitações e Contratos Administrativos; Regulamentação; Centralização de Compras'),
array('8.1','Formação de agentes públicos','Cualificación, acreditación y certificación de la Contratación Pública en América Latina y el Caribe','CHINEA, Oscar','2013','https://www.redalyc.org/articulo.oa?id=357533688007','Contratações públicas, reformas institucionais, organismos internacionais, desempenho dos sistemas, modernização administrativa.'),
array('8.1','Formação de agentes públicos','GESTÃO DO CONHECIMENTO NO PLANEJAMENTO DAS COMPRAS PÚBLICAS DO INSTITUTO NACIONAL DA PROPRIEDADE INDUSTRIAL',' FERREIRA, Thais Xavier de Paiva','2023','https://app.uff.br/riuff/handle/1/36219','Gestão do conhecimento, planejamento de compras, propriedade industrial'),
array('8.1','Formação de agentes públicos','Cultura digital na administração pública : o trabalho remoto e as novas competências para procedimentos de negociações e compras do IFPB João Pessoa','Andrade, Állysson Albuquerque','2023','https://repositorio.ufpb.br/jspui/handle/123456789/30974','Compras públicas Cultura digital Competência em informação IFPB - Instituto Federal da Paraíba'),
array('8.1','Formação de agentes públicos','Discricionariedade da burocracia em áreas-meio:compras na saúde na prefeitura de São Paulo','Panseri, Bárbara de Oliveira','2023','https://repositorio.fgv.br/items/27cab85d-a903-49d1-af3d-ddd3279d18b1','Burocracia, Área meio, compras, Saúde'),
array('8.1','Formação de agentes públicos','Relatório técnico com plano de ação para capacitação na Nova Lei de Licitações e Contratos para o município da Grande Aracaju ','Braga, Martha Elizabeth Araújo de Mendonça','2025','https://ri.ufs.br/handle/riufs/21200','Governança pública; Licitação pública;Capacitação de gestores; Implementação da NLLC/2021; Teoria de Ostrom'),
array('8.1','Formação de agentes públicos','AVALIAÇÃO DE COMPETÊNCIAS NA FASE PREPARATÓRIA DAS COMPRAS COMPARTILHADAS DO IFRO CAMPUS CACOAL.','BUENO GUIMARÃES, KAMILA; DRUMOND E. CASTRO, MARIA CRISTINA','2024','https://openurl.ebsco.com/EPDB%3Agcd%3A7%3A8364631/detailv2?sid=ebsco%3Aplink%3Ascholar&id=ebsco%3Agcd%3A186094842&crl=c&link_origin=scholar.google.com','Capacitação, avaliação de competências, compras públicas, NLLC, '),
array('8.2','Gestão por competências','Gestão por competência em uma instituição de ensino superior: desenvolvimento de um plano de capacitação baseado em competências.','Landim, Denise Vasconcelos','2017','https://app.uff.br/riuff/handle/1/15890','Plano de capacitação, Gestão por Competência, servidores técnicos-administrativos'),
array('8.2','Gestão por competências','Programa de Desenvolvimento  de Competências para Fiscais de Contratos Administrativos','Criado, Pâmela Cristina','2020','https://app.uff.br/riuff/handle/1/15890','Fiscalização, Transparência, Competência, Eficiência'),
array('8.3','Capacitação e Gestão de Pessoas','O papel do comprador no processo de compras em instituições públicas de ciência e tecnologia em saúde (C&T/S)','BATISTA & MALDONADO  ','2008','https://www.scielo.br/j/rap/a/dyWWfBDcgZJvPDsHGknfbjp/?lang=pt','gestão das compras públicas; ética do comprador público; aquisição de materiais'),
array('9.1','Modelos de compras em outros países','Compras públicas compartilhadas: um estudo de caso comparando modelos de compras públicas eletrônicas adotados no Brasil, no Chile e nos Estados Unidos  ','Paixão, André Luís Soares da','2021','https://bdtd.ibict.br/vufind/Record/UFSC_723493b76d3ae0d28ef6e1f69821dd01','compras, públicas, compartilhadas, modelos, eletrônicas'),
array('9.1','Modelos de compras em outros países','Análise comparativa da legislação brasileira com o modelo europeu de compras públicas de inovação  ','D Albuquerque-César, Florence Vieira','2022','https://repositorio.ufpe.br/handle/123456789/50122','análise, comparativa, modelo, europeu, compras'),
array('9.1','Modelos de compras em outros países','Compras e contratações públicas no exterior : uma proposta de mudança para o Departamento de Ciência e Tecnologia do Exército Brasileiro','Freitas, Breno Vieira de','2019','https://repositorio.idp.edu.br/handle/123456789/2549','compras, públicas, exterior, departamento, ciência'),
array('9.1','Modelos de compras em outros países','Mapeamento internacional de compras públicas: estratégias nacionais e desenvolvimento','Macedo, Jaime Reis, Breno Salomon','2023','https://repositorio.enap.gov.br/handle/1/7887','mapeamento, internacional, compras, públicas, estratégias'),
array('9.1','Modelos de compras em outros países','Panorama da Produção Científica sobre Compras Públicas no Brasil: Agenda de Pesquisa e Perspectivas de Investigação.','Sorice da Silva, Soraia; Mesquita Oliveira, Míriam Aparecida;Lopes, André Vaz','2023','https://periodicos.ufpe.br/revistas/gestaoorg/article/view/248824','panorama, produção, compras, públicas, científica'),
array('9.1','Modelos de compras em outros países','Favoritismo local nas compras públicas da China: atritos de informação ou distorção de incentivos?','Tang et al., ','2025','https://www.sciencedirect.com/science/article/abs/pii/S009411902400086X?via%3Dihub','incentivos, carreira, locais, alocação, favoritismo'),
array('9.1',' Modelos de compras em outros países','9.1 Modelos de compras em outros países','da Costa, Nuno Miguel Ramos','2022','https://repositorio.ulisboa.pt/handle/10400.5/24978','saúde, contratação, pública, agregada, setor'),
array('9.1','Modelos de compras em outros países','Preventing Maladministration in Indonesian Public Procurement: A Good Public Procurement Law Approach and Comparison with the Netherlands and the United Kingdom','Wibowo, R.A','2017','https://dspace.library.uu.nl/handle/1874/347986','Compras públicas; Transparência; Corrupção; Governança; Remédios jurídicos; Indonésia; Direito comparado'),
array('9.1','Modelos de compras em outros países','Factors contributing to non-compliance in public procurement- a KwaZulu-Natal legislature case study.','Gabela, Sandile Eric.','2017','https://researchspace.ukzn.ac.za/items/de800e26-d684-419b-bfb7-c252e2e679c5','Compras públicas, não conformidade, governança, concorrência, transparência, África do Sul, KwaZulu-Natal Legislature.'),
array('9.2','Boas práticas internacionais','La buena administración en la contratación pública en Colombia: más allá de la apertura de datos','Grau Piñeres, Laura Andrea  ','2021','https://repository.urosario.edu.co/items/8d2794e9-17b3-46ee-9613-d297e3605df1','Boa administração, contratação estatal, participação cidadã, controle social, transparência, acesso à informação, corrupção, Colômbia.'),

    );
    foreach ($data as $row) {
        $result = $wpdb->insert(
            $table_name,
            array(
                'id_item' => $row[0],
                'categoria' => $row[1],
                'titulo' => $row[2],
                'autor' => $row[3],
                'ano' => $row[4],
                'link' => $row[5],
                'palavra_chave' => $row[6]
                
            ),
            array('%s','%s','%s','%s','%s','%s', '%s')
        );
        
        if ($result === false) {
            error_log('Erro ao inserir dados: ' . $wpdb->last_error);
        }
    }
}




