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

<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->plugin/ShippingAdmin/js/jquery.excolorboxform-0.1.3.js"></script>
<script>
    $(function(){
        var default_bg_color = "<!--{$arrORDERSTATUS_COLOR[$SelectedStatus]}-->";
        // すべてを選択 // memo:change()を使うためdefaultの関数を使わない
        $("input#move_check").change(function(){
            var $check = $(".check");
            if ($(this).prop('checked')) {
                $check.prop('checked', true).parent().parent().addClass("active").css("background-color", "#FFCC99");
            } else {
                $check.prop('checked', false).parent().parent().removeClass("active").css("background-color", default_bg_color);
            }
        });
        $("table.list tr").click(function(){
            if (!$(this).prop("id")){ return }
            var $child = $(this).children().children();

            if ($(this).hasClass("active")){
                $(this).css("background-color", default_bg_color).removeClass("active");
                $($child[0]).prop("checked", false);
            } else {
                $(this).css("background-color", "#FFCC99").addClass("active");
                $($child[0]).prop("checked", true);
            }
        });

        // 荷物追跡番号入力
        var isLastPage, complateContent = false;
        var option = {
            overlayClose: true
            ,escKey: true
            ,onComplete : function(api){
                isLastPage = !api.getContents().find('form').size();
                if (isLastPage) {
                    complateContent = api.getContents().find('.complate-content').html();
                    api.close();
                }
            }
            ,onClosed : function(api){
                if (isLastPage) {
                    api.getTarget().html(complateContent);
                }
            }
        };
        $("td.plg_delive a").exColorboxForm(option);
    });
</script>

            <table class="list">
                <col width="4%" />
                <col width="10%" />
                <col width="8%" />
                <col width="12%" />
                <col width="10%" />
                <col width="10%" />
                <col width="10%" />
                <col width="12%" />
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
                    <th>発送日</th>
                    <th>荷物追跡番号</th>
                </tr>
                <!--{section name=cnt loop=$arrStatus}-->
                <!--{assign var=status value="`$arrStatus[cnt].status`"}-->
                <tr style="background:<!--{$arrORDERSTATUS_COLOR[$status]}-->;" id="<!--{$arrStatus[cnt].order_id}-->">
                    <td class="center"><input type="checkbox" name="move[]" value="<!--{$arrStatus[cnt].order_id}-->" class="check"></td>
                    <td class="center"><!--{$arrORDERSTATUS[$status]}--></td>
                    <td class="center"><a href="#" onclick="eccube.openWindow('./disp.php?order_id=<!--{$arrStatus[cnt].order_id}-->','order_disp','800','900',{resizable:'no',focus:false}); return false;" ><!--{$arrStatus[cnt].order_id}--></a></td>
                    <td class="center"><!--{$arrStatus[cnt].create_date|sfDispDBDate}--></td>
                    <td><!--{$arrStatus[cnt].order_name01|h}--> <!--{$arrStatus[cnt].order_name02|h}--></td>
                    <!--{assign var=payment_id value=`$arrStatus[cnt].payment_id`}-->
                    <td class="center"><!--{$arrPayment[$payment_id]|h}--></td>
                    <td class="right"><!--{$arrStatus[cnt].total|number_format}--></td>
                    <td class="center"><!--{if $arrStatus[cnt].payment_date != ""}--><!--{$arrStatus[cnt].payment_date|sfDispDBDate:false}--><!--{else}-->未入金<!--{/if}--></td>
                    <td class="plg_delive center"><!--{if $arrStatus[cnt].status eq 5}--><!--{$arrStatus[cnt].commit_date|sfDispDBDate:false}--><!--{else}-->未発送<!--{/if}--></td>
                    <td class="plg_delive center" nowrap>
                        <a href="plg_ShippingAdmin_delive_edit.php?order_id=<!--{$arrStatus[cnt].order_id}-->">
                            <!--{assign var=deliv_id value="`$arrStatus[cnt].deliv_id`"}--><!--{$arrDeliv[$deliv_id]}-->
                            <!--{foreach name=shipping from=$arrStatus[cnt].shippings item=arrShipping key=shipping_index}-->
                                <br />
                                <!--{if count($arrStatus[cnt].shippings) > 1}-->
                                    配送先<!--{$smarty.foreach.shipping.iteration}-->:
                                <!--{/if}-->
                                <!--{if $arrShipping.plg_shippingadmin_tracking_no}-->
                                    <!--{$arrShipping.plg_shippingadmin_tracking_no}-->
                                <!--{else}-->
                                    未登録
                                <!--{/if}-->
                            <!--{/foreach}-->
                            <br />
                        </a>
                    </td>
                </tr>
                <!--{/section}-->
            </table>
