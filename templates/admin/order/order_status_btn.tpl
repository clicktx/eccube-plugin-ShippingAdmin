<!--{*
 * ShippingAdmin
 *
 * Copyright(c) 2014 clicktx. All Rights Reserved.
 *
 * http://perl.no-tubo.net/
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *}-->
    <a class="btn-normal" href="javascript:;" onclick="plg_ShippingAdmin_fnSelectCheckSubmit(); return false;"><span>移動</span></a>
    <label><input type="checkbox" name="del_check" value="1">削除確認</label>
    <label><input type="checkbox" name="disable_auto_mail" value="1">自動メールを送信しない</label>
    <span>
        (
        <!--{foreach key=key item=item from=$tpl_auto_mail_order_status}-->
            <!--{if $item}-->
                <span><!--{$arrORDERSTATUS[$key]}--></span>
            <!--{/if}-->
        <!--{/foreach}-->
        に変更した場合は自動でメールが送信されます )
    </span>

<script type="text/javascript">
<!--
function plg_ShippingAdmin_fnSelectCheckSubmit(){
    var selectflag = 0;
    var fm = document.form1;

    if (fm.change_status.options[document.form1.change_status.selectedIndex].value == "") {
        selectflag = 1;
    }

    if (selectflag == 1) {
        alert('セレクトボックスが選択されていません');
        return false;
    }
    var i;
    var checkflag = 0;
    var max = fm["move[]"].length;

    if (max) {
        for (i=0;i<max;i++){
            if(fm["move[]"][i].checked == true) {
                checkflag = 1;
            }
        }
    } else {
        if (fm["move[]"].checked == true) {
            checkflag = 1;
        }
    }

    if (checkflag == 0){
        alert('チェックボックスが選択されていません');
        return false;
    }

    if (selectflag == 0 && checkflag == 1) {
        document.form1.mode.value = 'plg_shippingadmin_update';
        document.form1.submit();
    }
}
//-->
</script>
