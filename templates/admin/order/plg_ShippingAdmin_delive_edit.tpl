<!--{*
/*
 * ShippingAdmin
 *
 * Copyright(c) 2014 clicktx. All Rights Reserved.
 *
 * http://perl.no-tubo.net/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
    $(function(){
        var $select = $("select[name=deliv_id]");
        if (!$select.val()){
            $select.focus();
        } else {
            $("input[type=text]:first").focus();
        }
    });
</script>

<!--{if $tpl_complete}-->
    <h2>登録完了</h2>
    <div class="complate-content">
        <!--{$tpl_deliv_name|h}--><br />
        <!--{foreach name=shipping from=$arrTrackingNo item=arrShipping key=shipping_index}-->
            <!--{if count($arrTrackingNo) > 1}-->
                配送先<!--{$smarty.foreach.shipping.iteration}-->:
            <!--{/if}-->
            <!--{$arrShipping}--><br />
        <!--{/foreach}-->
    </div>
<!--{else}-->
    <!--▼配送情報フォームここから-->
    <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="edit" />
            <input type="hidden" name="order_id" value="<!--{$arrForm.order_id.value|h}-->" />
            <h2>配送業者</h2>
            <table class="form">
                <tr>
                    <th>配送業者</th>
                    <td>onchange="eccube.setModeAndSubmit('deliv','anchor_key','deliv');"
                        <!--{assign var=key value="deliv_id"}-->
                        <span class="attention"><!--{$arrErr[$key]}--></span>
                        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                            <option value="" selected="">選択してください</option>
                            <!--{html_options options=$arrDeliv selected=$arrForm[$key].value}-->
                        </select>
                    </td>
                </tr>
            </table>
        <!--{foreach name=shipping from=$arrAllShipping item=arrShipping key=shipping_index}-->
            <h2>お届け先<!--{$smarty.foreach.shipping.iteration}--></h2>
            <table class="form">
                <tr>
                    <th>お届け日</th>
                    <td>
                        <!--{assign var=key1 value="shipping_date_year"}-->
                        <!--{assign var=key2 value="shipping_date_month"}-->
                        <!--{assign var=key3 value="shipping_date_day"}-->
                        <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--></span>
                        <span class="attention"><!--{$arrErr[$key2][$shipping_index]}--></span>
                        <span class="attention"><!--{$arrErr[$key3][$shipping_index]}--></span>
                        <select name="<!--{$key1}-->[<!--{$shipping_index}-->]" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrYearShippingDate selected=$arrShipping[$key1]|default:""}-->
                        </select>年
                        <select name="<!--{$key2}-->[<!--{$shipping_index}-->]" style="<!--{$arrErr[$key2][$shipping_index]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrMonthShippingDate selected=$arrShipping[$key2]|default:""}-->
                        </select>月
                        <select name="<!--{$key3}-->[<!--{$shipping_index}-->]" style="<!--{$arrErr[$key3][$shipping_index]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrDayShippingDate selected=$arrShipping[$key3]|default:""}-->
                        </select>日
                    </td>
                </tr>
                <tr>
                    <th>お届け時間</th>
                    <td>
                        <!--{assign var=key value="time_id"}-->
                        <span class="attention"><!--{$arrErr[$key][$shipping_index]}--></span>
                        <select name="<!--{$key}-->[<!--{$shipping_index}-->]" style="<!--{$arrErr[$key][$shipping_index]|sfGetErrorColor}-->">
                            <option value="">指定無し</option>
                            <!--{html_options options=$arrDelivTime selected=$arrShipping[$key]}-->
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>荷物追跡番号</th>
                    <td>
                        <!--{assign var=key1 value="plg_shippingadmin_tracking_no"}-->
                        <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--></span>
                        <input type="text" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key1]|h}-->" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->" size="30" class="box30" />
                    </td>
                </tr>
            </table>
        <!--{/foreach}-->

        <div class="btn-area">
            <ul>
                <li><button class="btn-action"><span class="btn-next">この内容で登録する</span></button></li>
            </ul>
        </div>

    </form>

    <!--▲配送情報フォームここまで-->
<!--{/if}-->

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
