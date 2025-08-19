<?php
 
global $wpdb;
require_once('../../../wp-config.php');
if(isset($_POST['input'])){
    $input = $_POST['input'];
    $query = $wpdb->get_results("SELECT * FROM wp_biblioteca WHERE 
        categoria LIKE '%{$input}%' OR
        titulo LIKE '%{$input}%' OR 
        autor LIKE '%{$input}%' OR 
        ano LIKE '%{$input}%' OR 
        link LIKE '%{$input}%'
        LIMIT 20
    ");

    if($query > 0){
        
        ?>
        <div class="content-pat">

            <table class="table table-bordered table-striped mt-4" border="1" cellpadding="10" width="90%">
                <thead>
                    <tr>
                        <th>ID</th>  
                        <th>Categoria</th>
                        <th>Titulo</th>  
                        <th>Autor</th>
                        <th>Ano</th>
                        <th>Link</th>
                        <th>Editar</th>
                        <th>Deletar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach($query as $row){?>
                    <tr>
                        <td><?php echo $row->id;?></td>
                        <td><?php echo $row->categoria;?></td>
                        <td><?php echo $row->titulo;?></td>
                        <td><?php echo $row->autor;?></td>
                        <td><?php echo $row->ano;?></td>
                        <td><?php echo $row->link;?></td>
                        <!-- <td><?php //echo $row->telefone;?></td> -->
                        <td><a href="admin.php?page=update-pat&id=<?php echo $row->id;?>" class="btn-editar">EDITAR</a></td>
                        <td><a href="admin.php?page=delete-pat&id=<?php echo $row->id;?>" class="btn-deletar">DELETAR</a></td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
    <?php
    }else{
        echo "<h6 class='text-danger text-center mt-3'>Não foi encontrado informações</h6>";
    }
}
