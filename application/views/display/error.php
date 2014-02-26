<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function(){
            $('.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#deletion').button({ icons: {secondary: 'ui-icon-circle-close'}});
            //initial
            $('#showAll').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            });

            $('#deletion').click(function(){
                $('<div></div>')
              .html("Anda yakin akan menghapus semua data ?")
              .dialog({
                 title : "Hapus Semua Data",
                 modal: true,
                 buttons: [ 
                    { 
                        text: "Ok", 
                        click: function() { 
                            $.ajax({
                                type : 'GET',
                                url: '<?= base_url('display/delete_error_all') ?>/',
                                cache: false,
                                success: function(data) {
                                    $('#loaddata').html(data);
                                    alert_delete();
                                },
                                error: function() {
                                    alert_delete_failed();
                                }
                            });
                            $( this ).dialog( "close" ); 
                        } 
                    }, 
                    { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
                ]
            });
            });
        });

        function get_error_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('display/error') ?>/'+p,
                cache: false,
                success: function(data) {
                    $('#loaddata').html(data);
                }
            });
        }

        function delete_data(id){
            $('<div></div>')
              .html("Anda yakin akan menghapus data ini ?")
              .dialog({
                 title : "Hapus Data",
                 modal: true,
                 buttons: [ 
                    { 
                        text: "Ok", 
                        click: function() { 
                            $.ajax({
                                type : 'GET',
                                url: '<?= base_url('display/delete_error') ?>/'+id+'/'+$('.noblock').html(),
                                cache: false,
                                success: function(data) {
                                    $('#loaddata').html(data);
                                    alert_delete();
                                },
                                error: function() {
                                    alert_delete_failed();
                                }
                            });
                            $( this ).dialog( "close" ); 
                        } 
                    }, 
                    { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
                ]
            });
           
        }

        function paging(p){
            get_error_list(p);
        }

        function display(key){
            var data = $('#detail'+key).html();
            $('<div></div>')
              .html(data)
              .dialog({
                 title : "Detail Error",
                 modal: true,
                 height: 500,
                 width: 700,
                 buttons: [ 
                    { 
                        text: "Ok", 
                        click: function() { 
                            $( this ).dialog( "close" ); 
                        } 
                    }
                ]
            });

        }
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <?= form_button('', 'Hapus Semua', 'id=deletion') ?>
    <?= form_button('', 'Reset', 'class=resetan id=showAll') ?>
    <div class="data-list">
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
       <table cellpadding="0" cellspacing="0" class="tabel" width="100%">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="25%">Menu</th>
                <th width="15%">Waktu</th>
                <th width="15%">Status</th>
                <th width="35%">URL</th>
                <th>Response</th>
                <th width="5%">#</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($list != null): ?>
            <?php foreach ($list as $key => $prov) : ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                    <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                    <td><?= $prov->menu ?></td>
                    <td align="center"><?= datetimefmysql($prov->waktu, true) ?></td>
                    <td><?= $prov->status ?></td>
                    <td><?= $prov->url ?></td>
                    <td align="center"><span class="link_button" onclick="display('<?= $key ?>')">detail</span>
                        <span style="display:none" id="detail<?= $key ?>"><?=  $prov->response ?></span></td> 
                     <td class="aksi">
                        <a class="delete" onclick="delete_data('<?= $prov->id ?>')"></a>
                    </td>                  
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <br/>
    <div id="paging"><?= $paging ?></div>
    </div>

</div>
