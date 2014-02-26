<?php

class M_user extends CI_Model {
    
    function cek_login() {
        $query="select p.*, u.username, u.password, un.nama as unit, k.id as id_pegawai, u.status, 
            un.jenis, ug.id as id_group
            from users u
            join penduduk p on (u.id = p.id)
            join unit un on (un.id = p.unit_id)
            join kepegawaian k on (p.id = k.penduduk_id)
            left join user_group ug on (ug.id = u.user_group_id)
        where u.username = '".post_safe('username')."' and u.password = '".md5(post_safe('password'))."'";
        //echo $query;
        $hasil=$this->db->query($query);
        return $hasil->row();
    }
    
    function module_load_data($id=null) {
        $q = null;
        if ($id != null) {
            $q.="and pp.user_group_id = '$id' ";
        }else{
            $q = "and pp.user_group_id = '0'";
        }
        $sql = "select m.* from user_group_privileges pp
            join privileges p on (pp.privileges_id = p.id)
            join module m on (p.module_id = m.id)
            where p.show_desktop = '1' $q group by p.module_id";
        
        return $this->db->query($sql);
    }
    
    function menu_user_load_data($id = null, $module = null) {
        $q = null;
        if ($id !== null) {
            $q.="and u.id = '".$this->session->userdata('id_user')."' ";
        }
        if ($module !== null) {
            $q .=  "and p.module_id = '$module' and pp.user_group_id = '".$this->session->userdata('id_group')."'";
        }
        $sql = "select m.*, p.form_nama, p.url, p.module_id, p.id as id_privileges 
            from user_group_privileges pp
            join user_group g on (pp.user_group_id = g.id)
            join privileges p on (pp.privileges_id = p.id)
            join users u on (g.id = u.user_group_id)
            join module m on (p.module_id = m.id)
            where p.id is not null $q and p.show_desktop = '1'
            order by p.form_nama";
        
        return $this->db->query($sql);
    }
}
?>