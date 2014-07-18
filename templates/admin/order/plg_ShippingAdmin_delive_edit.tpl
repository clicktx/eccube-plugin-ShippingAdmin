<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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
    <!--
    self.moveTo(20,20);self.focus();
    //-->
</script>

<!--▼配送情報フォームここから-->
<!--{$arrForm.order_id.value|h}-->
    <input type="hidden" name="order_id" value="<!--{$arrForm.order_id.value|h}-->" />
    <h2>配送情報</h2>
    <table class="form">
        <tr>
            <th>配送業者</th>
            <td>aaa</td>
        </tr>
        <tr>
            <th>荷物追跡番号</th>
            <td>aaa</td>
        </tr>
        <tr>
            <th>お届け日</th>
            <td>aaa</td>
        </tr>
        <tr>
            <th>お届け時間</th>
            <td>aaa</td>
        </tr>
    </table>
    <!--▲配送情報フォームここまで-->

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
