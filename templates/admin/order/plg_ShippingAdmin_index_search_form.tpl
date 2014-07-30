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

            <tr>
                <th>荷物追跡番号</th>
                <td>
                    <!--{assign var=key value="search_plg_shippingadmin_tracking_no"}-->
                    <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="6" class="box30">
                </td>
                <th>配送業者</th>
                <td>
                    <!--{assign var=key value="search_deliv_id"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                        <option value="" selected="">選択してください</option>
                        <!--{html_options options=$arrDeliv selected=$arrForm[$key].value}-->
                    </select>
                </td>
            </tr>
            <tr>
                <th>出荷日</th>
                <td colspan="3">
                    <!--{if $arrErr.search_sdelivedyear}--><span class="attention"><!--{$arrErr.search_sdelivedyear}--></span><!--{/if}-->
                    <!--{if $arrErr.search_edelivedyear}--><span class="attention"><!--{$arrErr.search_edelivedyear}--></span><!--{/if}-->
                    <select name="search_sdelivedyear" style="<!--{$arrErr.search_sdelivedyear|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrRegistYear selected=$arrForm.search_sdelivedyear.value}-->
                    </select>年
                    <select name="search_sdelivedmonth" style="<!--{$arrErr.search_sdelivedyear|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMonth selected=$arrForm.search_sdelivedmonth.value}-->
                    </select>月
                    <select name="search_sdelivedday" style="<!--{$arrErr.search_sdelivedyear|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDay selected=$arrForm.search_sdelivedday.value}-->
                    </select>日～
                    <select name="search_edelivedyear" style="<!--{$arrErr.search_edelivedyear|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrRegistYear selected=$arrForm.search_edelivedyear.value}-->
                    </select>年
                    <select name="search_edelivedmonth" style="<!--{$arrErr.search_edelivedyear|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMonth selected=$arrForm.search_edelivedmonth.value}-->
                    </select>月
                    <select name="search_edelivedday" style="<!--{$arrErr.search_edelivedyear|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDay selected=$arrForm.search_edelivedday.value}-->
                    </select>日
                </td>
            </tr>
