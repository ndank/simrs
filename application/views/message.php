<?php
//header('Cache-Control: max-age=0');
?>
<script type="text/javascript">

function generate_msg(status,tipe){
    if (status === 'ok') {
        if(tipe === ''){
            alert_tambah();                                    
        }else{
            alert_edit();
        }
    }else{
        if(tipe === ''){
            alert_tambah_failed();                                    
        }else{
            alert_edit_failed();
        }
    }
    
}

function custom_message(title, content, element){
    $("<div></div>")
      .html(content)
      .dialog({
           title : title,
           modal: true,
           closeOnEscape: true,
           buttons: [ 
              { text: "Ok", click: function() { $( this ).dialog( "close" ); $(element).focus();} } 
          ]
     });
}

function alert_tambah() {
    $( "#tambah" ).dialog({
        modal: true,
        buttons: {
          Ok: function() {
            $( this ).dialog( "close" );
          }
        }
    });
}

function alert_tambah_failed() {
    $( "#tambah_failed" ).dialog({
        modal: true,
        close : function(){
          location.reload();
        },
        buttons: {
          Ok: function() {
            $( this ).dialog( "close" );
            location.reload();
          }
        }
    });
}

function alert_edit() {
    $( "#edit" ).dialog({
        modal: true,
        buttons: {
          Ok: function() {
            $( this ).dialog( "close" );
          }
        }
    });
}

function alert_edit_failed() {
    $( "#edit_failed" ).dialog({
        modal: true,
        close : function(){
          location.reload();
        },
        buttons: {
          Ok: function() {
            $( this ).dialog( "close" );
            location.reload();
          }
        }
    });
}

function alert_delete() {
    $( "#delete" ).dialog({
        modal: true,
        buttons: {
          Ok: function() {
            $( this ).dialog( "close" );
          }
        }
    });
}
function alert_delete_failed() {
    $( "#delete_failed" ).dialog({
        modal: true,
        buttons: {
          Ok: function() {
            $( this ).dialog( "close" );
          }
        }
    });
}

function alert_resets() {
    $( "#resets" ).dialog({
        modal: true,
        buttons: {
          Ok: function() {
            $( this ).dialog( "close" );
          }
        }
    });
}

function alert_empty(variable, focus) {
    $( "<div title='Alert: Warning'>Data "+variable+" tidak boleh kosong !</div>" ).dialog({
        autoOpen: true,
        modal: true,
        buttons: {
          Ok: function() {
            $( this ).dialog( "close" );
            $(focus).focus();
          }
        }
    });
}

function alert_dinamic(variable, focus) {
    $( "<div title='Alert: Warning'>"+variable+"</div>" ).dialog({
        autoOpen: true,
        modal: true,
        buttons: {
          Ok: function() {
            $( this ).dialog( "close" );
            $(focus).focus();
          }
        }
    });
}

function alert_refresh(content) {
    $( "<div>"+content+"</div>" ).dialog({
        modal: true,
        title: 'Alert: informasi',
        buttons: {
          Ok: function() {
            location.reload();
          }
        },
        close: function() {
            location.reload();
        }
    });
}
</script>
<div id="tambah" style="display: none" title="Information Alert">
    <p>
      <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
      Data Telah Berhasil di Tambahkan
    </p>
</div>
<div id="tambah_failed" style="display: none" title="Information Alert">
    <p>
      <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
      Data Gagal ditambahkan, cek kembali data yang dimasukkan
    </p>
</div>
<div id="sukses" style="display: none" title="Information Alert">
    <p>
      <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
      Data Telah Berhasil disimpan
    </p>
</div>
<div id="edit" style="display: none" title="Information Alert">
    <p>
      <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
      Data Telah Berhasil di Update
    </p>
</div>
<div id="edit_failed" style="display: none" title="Information Alert">
    <p>
      <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
      Data Gagal di Update
    </p>
</div>
<div id="delete" style="display: none" title="Information Alert">
    <p>
      <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
      Data Telah Berhasil di Hapus
    </p>
</div>
<div id="delete_failed" style="display: none" title="Information Alert">
    <p>
      <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
      Data Gagal di Hapus, Karena digunakan pada transaksi lain !
    </p>
</div>
<div id="resets" style="display: none" title="Information Alert">
    <p>
      <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
      Reset data berhasil dilakukan
    </p>
</div>

<div id="trans" style="display: none" title="Information Alert">
    <p>
      <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
      Transaksi data berhasil dilakukan
    </p>
</div>