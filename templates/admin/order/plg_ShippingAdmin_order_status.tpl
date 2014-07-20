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
plg_ShippingAdmin
            <table class="list">
                <col width="5%" />
                <col width="10%" />
                <col width="8%" />
                <col width="13%" />
                <col width="20%" />
                <col width="10%" />
                <col width="10%" />
                <col width="12%" />
                <col width="12%" />
                <tr>
                    <th><label for="move_check">選択<br /></label> <input type="checkbox" name="move_check" id="move_check" /></th>
                    <th>対応状況</th>
                    <th>注文番号</th>
                    <th>受注日</th>
                    <th>お名前</th>
                    <th>支払方法</th>
                    <th>購入金額（円）</th>
                    <th>入金日</th>
                    <th>発送日<br />荷物追跡番号</th>
                </tr>
                <!--{section name=cnt loop=$arrStatus}-->
                <!--{assign var=status value="`$arrStatus[cnt].status`"}-->
                <tr style="background:<!--{$arrORDERSTATUS_COLOR[$status]}-->;" class="<!--{$arrStatus[cnt].order_id}-->">
                    <td class="center"><input type="checkbox" name="move[]" value="<!--{$arrStatus[cnt].order_id}-->" class="check <!--{$arrStatus[cnt].order_id}-->"></td>
                    <td class="center"><!--{$arrORDERSTATUS[$status]}--></td>
                    <td class="center"><a href="#" onclick="eccube.openWindow('./disp.php?order_id=<!--{$arrStatus[cnt].order_id}-->','order_disp','800','900',{resizable:'no',focus:false}); return false;" ><!--{$arrStatus[cnt].order_id}--></a></td>
                    <td class="center"><!--{$arrStatus[cnt].create_date|sfDispDBDate}--></td>
                    <td><!--{$arrStatus[cnt].order_name01|h}--> <!--{$arrStatus[cnt].order_name02|h}--></td>
                    <!--{assign var=payment_id value=`$arrStatus[cnt].payment_id`}-->
                    <td class="center"><!--{$arrPayment[$payment_id]|h}--></td>
                    <td class="right"><!--{$arrStatus[cnt].total|number_format}--></td>
                    <td class="center"><!--{if $arrStatus[cnt].payment_date != ""}--><!--{$arrStatus[cnt].payment_date|sfDispDBDate:false}--><!--{else}-->未入金<!--{/if}--></td>
                    <td class="plg_delive center" nowrap><!--{if $arrStatus[cnt].status eq 5}--><!--{$arrStatus[cnt].commit_date|sfDispDBDate:false}--><br/ ><!--{assign var=deliv_id value="`$arrStatus[cnt].deliv_id`"}--><!--{$arrDeliv[$deliv_id]}--><br /><!--{$arrStatus[cnt].plg_shippingadmin_tracking_no}--><!--{else}--><a href="plg_ShippingAdmin_delive_edit.php?order_id=<!--{$arrStatus[cnt].order_id}-->">未発送</a><!--{/if}--></td>
                </tr>
                <!--{/section}-->
            </table>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->plugin/ShippingAdmin/js/jquery.excolorboxform-0.1.3.js"></script>
<script>
    $(function(){
        var default_bg_color = "<!--{$arrORDERSTATUS_COLOR[$status]}-->";
        // すべてを選択 // memo:change()を使うためdefaultの関数を使わない
        $("#move_check").change(function(){
            var $check = $(".check");
            if ($(this).attr('checked')) {
                $check.attr('checked', true).parent().parent().css("background-color", "#FFCC99");
            } else {
                $check.attr('checked', false).parent().parent().css("background-color", default_bg_color);
            }
        });
        // 行クリック時に対象チェックボックスをチェックする
        $("table.list tr").click(function(){
            var klass = $(this).attr("class");
            if (!klass){ return }
            var checked_flag = $("input."+klass).attr('checked');
            if (checked_flag){
                $("tr."+klass).css("background-color", default_bg_color);
                $("input."+klass).attr("checked", false);
            }
            else{
                $("tr."+klass).css("background-color", "#FFCC99");
                $("input."+klass).attr("checked", true);
            }
            // return false;
        });
        // 荷物追跡番号入力
        // $html = '<h1>Welcome</h1><input type="text" name="" value="" placeholder="">';
        // $("td.plg_delive a").click(function(){
        //     // alert();
        //     $.colorbox({
        //         rel: "plg_shippingadmin_content"
        //         ,right: "10%"
        //     });
        // });
        $("td.plg_delive a").exColorboxForm({
            overlayClose: true
            ,escKey: true
        });
    });
</script>
