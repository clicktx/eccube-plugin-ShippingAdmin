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
plg_ShippingAdmin
    <!--{if count($arrErr) == 0}-->

        <!--★★検索結果一覧★★-->
        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="search" />
            <input type="hidden" name="order_id" value="" />
            <!--{foreach key=key item=item from=$arrHidden}-->
                <!--{if is_array($item)}-->
                    <!--{foreach item=c_item from=$item}-->
                    <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
                    <!--{/foreach}-->
                <!--{else}-->
                    <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
                <!--{/if}-->
            <!--{/foreach}-->
            <h2>検索結果一覧</h2>
                <div class="btn">
                <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
                <!--{if $smarty.const.ADMIN_MODE == '1'}-->
                <a class="btn-normal" href="javascript:;" onclick="eccube.setModeAndSubmit('delete_all','',''); return false;"><span>検索結果を全て削除</span></a>
                <!--{/if}-->
                <a class="btn-normal" href="javascript:;" onclick="eccube.setModeAndSubmit('csv','',''); return false;">CSV ダウンロード</a>
                <a class="btn-normal" href="../contents/csv.php?tpl_subno_csv=order">CSV 出力項目設定</a>
                <a class="btn-normal" href="javascript:;" onclick="fnSelectCheckSubmit('pdf.php'); return false;"><span>PDF一括出力</span></a>
                <a class="btn-normal" href="javascript:;" onclick="fnSelectMailCheckSubmit('mail.php'); return false;"><span>メール一括通知</span></a>
            </div>
            <!--{if count($arrResults) > 0}-->

                <!--{include file=$tpl_pager}-->

                <!--{* 検索結果表示テーブル *}-->
                <table class="list">
                    <col width="10%" />
                    <col width="8%" />
                    <col width="15%" />
                    <col width="8%" />
                    <col width="10%" />
                    <col width="10%" />
                    <col width="10%" />
                    <col width="10%" />
                    <col width="5%" />
                    <col width="9%" />
                    <col width="5%" />
                    <!--{* ペイジェントモジュール連携用 *}-->
                    <!--{assign var=path value=`$smarty.const.MODULE_REALDIR`mdl_paygent/paygent_order_index.tpl}-->
                    <!--{if file_exists($path)}-->
                        <!--{include file=$path}-->
                    <!--{else}-->
                        <tr>
                            <th>受注日</th>
                            <th>注文番号</th>
                            <th>お名前</th>
                            <th>支払方法</th>
                            <th>購入金額(円)</th>
                            <th>全商品発送日</th>
                            <th>対応状況</th>
                            <th><label for="pdf_check">帳票</label> <input type="checkbox" name="pdf_check" id="pdf_check" onclick="eccube.checkAllBox(this, 'input[name=pdf_order_id[]]')" /></th>
                            <th>編集</th>
                            <th>メール <input type="checkbox" name="mail_check" id="mail_check" onclick="eccube.checkAllBox(this, 'input[name=mail_order_id[]]')" /></th>
                            <th>削除</th>
                        </tr>

                        <!--{section name=cnt loop=$arrResults}-->
                            <!--{assign var=status value="`$arrResults[cnt].status`"}-->
                            <tr style="background:<!--{$arrORDERSTATUS_COLOR[$status]}-->;">
                                <td class="center"><!--{$arrResults[cnt].create_date|sfDispDBDate}--></td>
                                <td class="center"><a href="#" onclick="eccube.openWindow('./disp.php?order_id=<!--{$arrResults[cnt].order_id}-->','order_disp','800','900',{resizable:'no',focus:false}); return false;"><!--{$arrResults[cnt].order_id}--></a></td>
                                <td class="center"><!--{$arrResults[cnt].order_name01|h}--> <!--{$arrResults[cnt].order_name02|h}--></td>
                                <!--{assign var=payment_id value="`$arrResults[cnt].payment_id`"}-->
                                <td class="center"><!--{$arrPayments[$payment_id]}--></td>
                                <td class="right"><!--{$arrResults[cnt].total|number_format}--></td>
                                <td class="center"><!--{$arrResults[cnt].commit_date|sfDispDBDate|default:"未発送"}--></td>
                                <td class="center" nowrap>
                                    <!--{$arrORDERSTATUS[$status]}-->
                                    <!--{if ($arrResults[cnt].plg_shippingadmin_tracking_no)}-->
                                        <!--{assign var=deliv_id value="`$arrResults[cnt].deliv_id`"}-->
                                        <br /><!--{$arrDeliv[$deliv_id]}-->
                                        <br /><!--{$arrResults[cnt].plg_shippingadmin_tracking_no}-->
                                    <!--{/if}-->
                                </td>
                                <td class="center">
                                    <input type="checkbox" name="pdf_order_id[]" value="<!--{$arrResults[cnt].order_id}-->" id="pdf_order_id_<!--{$arrResults[cnt].order_id}-->"/><label for="pdf_order_id_<!--{$arrResults[cnt].order_id}-->">一括出力</label><br />
                                    <a href="./" onClick="eccube.openWindow('pdf.php?order_id=<!--{$arrResults[cnt].order_id}-->','pdf_input','620','650'); return false;"><span class="icon_class">個別出力</span></a>
                                </td>
                                <td class="center"><a href="?" onclick="eccube.changeAction('<!--{$smarty.const.ADMIN_ORDER_EDIT_URLPATH}-->'); eccube.setModeAndSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_edit">編集</span></a></td>
                                <td class="center">
                                    <!--{if $arrResults[cnt].order_email|strlen >= 1}-->
                                        <input type="checkbox" name="mail_order_id[]" value="<!--{$arrResults[cnt].order_id}-->" id="mail_order_id_<!--{$arrResults[cnt].order_id}-->"/><label for="mail_order_id_<!--{$arrResults[cnt].order_id}-->">一括通知</label><br />
                                        <a href="?" onclick="eccube.changeAction('<!--{$smarty.const.ADMIN_ORDER_MAIL_URLPATH}-->'); eccube.setModeAndSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_mail">個別通知</span></a>
                                    <!--{/if}-->
                                </td>
                                <td class="center"><a href="?" onclick="eccube.setModeAndSubmit('delete', 'order_id', <!--{$arrResults[cnt].order_id}-->); return false;"><span class="icon_delete">削除</span></a></td>
                            </tr>
                        <!--{/section}-->
                    <!--{/if}-->
                </table>
                <!--{* 検索結果表示テーブル *}-->

            <!--{/if}-->

        </form>
    <!--{/if}-->

