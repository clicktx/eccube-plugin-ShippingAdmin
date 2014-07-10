<!--{*
 * SippingAdmin
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
                <th>出荷日</th>
                <td colspan="3">
                    <!--{if $arrErr.search_supdateyear}--><span class="attention"><!--{$arrErr.search_supdateyear}--></span><!--{/if}-->
                    <!--{if $arrErr.search_eupdateyear}--><span class="attention"><!--{$arrErr.search_eupdateyear}--></span><!--{/if}-->
                    <select name="search_supdateyear" style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrRegistYear selected=$arrForm.search_supdateyear.value}-->
                    </select>年
                    <select name="search_supdatemonth" style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMonth selected=$arrForm.search_supdatemonth.value}-->
                    </select>月
                    <select name="search_supdateday" style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDay selected=$arrForm.search_supdateday.value}-->
                    </select>日～
                    <select name="search_eupdateyear" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrRegistYear selected=$arrForm.search_eupdateyear.value}-->
                    </select>年
                    <select name="search_eupdatemonth" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMonth selected=$arrForm.search_eupdatemonth.value}-->
                    </select>月
                    <select name="search_eupdateday" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDay selected=$arrForm.search_eupdateday.value}-->
                    </select>日
                </td>
            </tr>
            <tr>
                <th>宅配便荷物番号</th>
                <td>
                    <!--{assign var=key value="search_plg_deliveadmin_tracking_no"}-->
                    <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="6" class="box30">
                </td>
                <th>----</th>
                <td>
                    <input type="text" name="search_xxx_xxx" value="" maxlength="50" style="" size="6" class="box30">
                </td>
            </tr>