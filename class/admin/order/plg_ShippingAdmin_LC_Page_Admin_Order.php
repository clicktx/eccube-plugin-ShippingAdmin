<?php
/*
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
 */


/**
 * 受注管理 のページクラス
 *
 * @package Page
 * @author clicktx
 */
class plg_ShippingAdmin_LC_Page_Admin_Order
{
    /**
    * 管理機能 受注情報リスト.
    *
    * @param LC_Page_Admin_Order $objPage 管理受注情報リスト のページクラス.
    * @return void
    */
    function action_before($objPage) {
        // modeを書き換えて LC_Page_Admin_Order の処理を無効にする
        $mode = $_REQUEST['mode'];
        $_REQUEST['mode'] = 'plg_ShippingAdmin_' . $mode;
        $objPage->arrDeliv = SC_Helper_Delivery_Ex::getIDValueList();

        $objFormParam = new SC_FormParam_Ex();
        $objPage->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objPage->arrHidden = $objFormParam->getSearchArray();
        $objPage->arrForm = $objFormParam->getFormParamList();
        $objPurchase = new SC_Helper_Purchase_Ex();

        switch ($objPage->getMode()) {
            // 削除
            case 'plg_ShippingAdmin_delete':
                $order_id = $objFormParam->getValue('order_id');
                $objPurchase->cancelOrder($order_id, ORDER_CANCEL, true);
                // 削除後に検索結果を表示するため breakしない

            // 検索パラメーター生成後に処理実行するため breakしない
            case 'plg_ShippingAdmin_csv':
            case 'plg_ShippingAdmin_delete_all':

            // 検索パラメーターの生成
            case 'plg_ShippingAdmin_search':
                $objFormParam->convParam();
                $objFormParam->trimParam();
                $objPage->arrErr = $this->plg_ShippingAdmin_lfCheckError($objFormParam);
                $arrParam = $objFormParam->getHashArray();

                if (count($objPage->arrErr) == 0) {
                    $where = 'del_flg = 0';
                    $arrWhereVal = array();
                    foreach ($arrParam as $key => $val) {
                        if ($val == '') {
                            continue;
                        }
                        $objPage->buildQuery($key, $where, $arrWhereVal, $objFormParam);
                        $this->plg_ShippingAdimn_buildQuery($key, $where, $arrWhereVal, $objFormParam);
                    }

                    $order = 'update_date DESC';

                    /* -----------------------------------------------
                     * 処理を実行
                     * ----------------------------------------------- */
                    switch ($objPage->getMode()) {
                        // CSVを送信する。
                        case 'plg_ShippingAdmin_csv':
                            $objPage->doOutputCSV($where, $arrWhereVal, $order);

                            SC_Response_Ex::actionExit();
                            break;

                        // 全件削除(ADMIN_MODE)
                        case 'plg_ShippingAdmin_delete_all':
                            // $page_max = 0;
                            // $arrResults = $objPage->findOrders($where, $arrWhereVal,
                            //                                $page_max, 0, $order);
                            // foreach ($arrResults as $element) {
                            //     $objPurchase->cancelOrder($element['order_id'], ORDER_CANCEL, true);
                            // }
                            break;

                        // 検索実行
                        default:
                            // 行数の取得
                            $objPage->tpl_linemax = $objPage->getNumberOfLines($where, $arrWhereVal);
                            // ページ送りの処理
                            $page_max = SC_Utils_Ex::sfGetSearchPageMax($objFormParam->getValue('search_page_max'));
                            // ページ送りの取得
                            $objNavi = new SC_PageNavi_Ex($objPage->arrHidden['search_pageno'],
                                                          $objPage->tpl_linemax, $page_max,
                                                          'eccube.moveNaviPage', NAVI_PMAX);
                            $objPage->arrPagenavi = $objNavi->arrPagenavi;

                            // 検索結果の取得
                            $objPage->arrResults = $objPage->findOrders($where, $arrWhereVal,
                                                                  $page_max, $objNavi->start_row, $order);
                            break;
                    }
                }
                break;
            default:
                break;
        }
    }

    function action_after ($objPage){}

    /**
     * 入力内容のチェックを行う.
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function plg_ShippingAdmin_lfCheckError(&$objFormParam)
    {
        $objErr = new SC_CheckError_Ex($objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();

        // 相関チェック
        $objErr->doFunc(array('注文番号1', '注文番号2', 'search_order_id1', 'search_order_id2'), array('GREATER_CHECK'));
        $objErr->doFunc(array('年齢1', '年齢2', 'search_age1', 'search_age2'), array('GREATER_CHECK'));
        $objErr->doFunc(array('購入金額1', '購入金額2', 'search_total1', 'search_total2'), array('GREATER_CHECK'));
        // 受注日
        $objErr->doFunc(array('開始', 'search_sorderyear', 'search_sordermonth', 'search_sorderday'), array('CHECK_DATE'));
        $objErr->doFunc(array('終了', 'search_eorderyear', 'search_eordermonth', 'search_eorderday'), array('CHECK_DATE'));
        $objErr->doFunc(array('開始', '終了', 'search_sorderyear', 'search_sordermonth', 'search_sorderday', 'search_eorderyear', 'search_eordermonth', 'search_eorderday'), array('CHECK_SET_TERM'));
        // 更新日
        $objErr->doFunc(array('開始', 'search_supdateyear', 'search_supdatemonth', 'search_supdateday'), array('CHECK_DATE'));
        $objErr->doFunc(array('終了', 'search_eupdateyear', 'search_eupdatemonth', 'search_eupdateday'), array('CHECK_DATE'));
        $objErr->doFunc(array('開始', '終了', 'search_supdateyear', 'search_supdatemonth', 'search_supdateday', 'search_eupdateyear', 'search_eupdatemonth', 'search_eupdateday'), array('CHECK_SET_TERM'));
        // 生年月日
        $objErr->doFunc(array('開始', 'search_sbirthyear', 'search_sbirthmonth', 'search_sbirthday'), array('CHECK_DATE'));
        $objErr->doFunc(array('終了', 'search_ebirthyear', 'search_ebirthmonth', 'search_ebirthday'), array('CHECK_DATE'));
        $objErr->doFunc(array('開始', '終了', 'search_sbirthyear', 'search_sbirthmonth', 'search_sbirthday', 'search_ebirthyear', 'search_ebirthmonth', 'search_ebirthday'), array('CHECK_SET_TERM'));
        // 発送日
        $objErr->doFunc(array('開始', 'search_sdelivedyear', 'search_sdelivedmonth', 'search_sdelivedday'), array('CHECK_DATE'));
        $objErr->doFunc(array('終了', 'search_edelivedyear', 'search_edelivedmonth', 'search_edelivedday'), array('CHECK_DATE'));
        $objErr->doFunc(array('開始', '終了', 'search_sdelivedyear', 'search_sdelivedmonth', 'search_sdelivedday', 'search_edelivedyear', 'search_edelivedmonth', 'search_edelivedday'), array('CHECK_SET_TERM'));

        return $objErr->arrErr;
    }

    /**
     * 通常のクエリ構築後に宅配便お問い合わせ番号の条件を追加する.
     *
     * 構築内容は, 引数の $where 及び $arrValues にそれぞれ追加される.
     *
     * @param  string       $key          検索条件のキー
     * @param  string       $where        構築する WHERE 句
     * @param  array        $arrValues    構築するクエリパラメーター
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function plg_ShippingAdimn_buildQuery($key, &$where, &$arrValues, &$objFormParam)
    {
        // 絞り込み条件を追加する
        switch ($key){
            case 'search_sdelivedyear':
                $date = SC_Utils_Ex::sfGetTimestamp($objFormParam->getValue('search_sdelivedyear'),
                                                    $objFormParam->getValue('search_sdelivedmonth'),
                                                    $objFormParam->getValue('search_sdelivedday'));
                $where.= ' AND commit_date >= ?';
                $arrValues[] = $date;
                break;
            case 'search_edelivedyear':
                $date = SC_Utils_Ex::sfGetTimestamp($objFormParam->getValue('search_edelivedyear'),
                                                    $objFormParam->getValue('search_edelivedmonth'),
                                                    $objFormParam->getValue('search_edelivedday'), true);
                $where.= ' AND commit_date <= ?';
                $arrValues[] = $date;
                break;
            case 'search_plg_shippingadmin_tracking_no':
                $where .= ' AND EXISTS (SELECT 1 FROM dtb_shipping ds WHERE ds.order_id = dtb_order.order_id AND ds.plg_shippingadmin_tracking_no LIKE ?)';
                $arrValues[] = sprintf('%%%s%%', $objFormParam->getValue($key));
                break;
            case 'search_deliv_id':
                $where.= ' AND deliv_id = ?';
                $arrValues[] = $objFormParam->getValue($key);
                break;
            default:
                break;
        }
    }
}
