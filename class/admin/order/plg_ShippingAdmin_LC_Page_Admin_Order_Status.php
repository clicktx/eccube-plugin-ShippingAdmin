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
 * 対応状況管理 のページクラス.
 *
 * @package Page
 * @author clicktx
 */
class plg_ShippingAdmin_LC_Page_Admin_Order_status
{

    function action_before ($objPage){}

    /**
    * 管理機能 対応状況管理 のページクラス.
    *
    * @param LC_Page_Admin_Order_Status $objPage 管理対応状況管理 のページクラス.
    * @return void
    */
    function action_after($objPage) {
        $objPurchase = new SC_Helper_Purchase_Ex();

        // 配送業者一覧を取得
        $objPage->arrDeliv = SC_Helper_Delivery_Ex::getIDValueList();

        // memo: オリジナルのコードと２重で実行することになるが...
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター情報の初期化
        $objPage->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        // 入力値の変換
        $objFormParam->convParam();

        switch ($objPage->getMode()) {
            case 'plg_shippingadmin_update':
                $changeStatus = $objFormParam->getValue('change_status');
                $arrMoveOderId = $objFormParam->getValue('move');

                switch ($changeStatus) {
                    // 削除
                    case 'delete':
                        // 削除確認がチェックされているか？
                        $del_check = $objFormParam->getValue('del_check');
                        if ($del_check){
                            $objPage->lfDelete($arrMoveOderId);
                        } else {
                            $objPage->tpl_onload = "window.alert('削除時は削除確認にチェックを入れて下さい。');";
                        }

                        break;
                    // 発送済み
                    case '5':
                        // 発送済みに変更する場合は荷物追跡番号が登録されているかチェック
                        $checkFlag = 0;
                        foreach ($arrMoveOderId as $index => $order_id) {
                            $arrShippings = $objPurchase->getShippings($order_id, false);
                            $checkFlag += $this->lfCheckTrackingNo($arrShippings);
                        }
                        if ($checkFlag){
                            $objPage->tpl_onload = "window.alert('荷物追跡番号が登録されていないため、選択項目を" . $arrORDERSTATUS[$changeStatus] . "へ移動出来ませんでした');";
                            break;
                        }
                        // 正常の場合はbreak; しない
                    // 更新
                    default:
                        $objPage->lfStatusMove($changeStatus, $arrMoveOderId);
                        break;
                }
                break;

            case 'search':
            default:
                break;
        }

        // 対応状況
        $status = $objFormParam->getValue('status');
        if (strlen($status) === 0) {
                //デフォルトで新規受付一覧表示
                $status = ORDER_NEW;
        }
        $objPage->SelectedStatus = $status;
        // 検索結果の表示
        $objPage->lfStatusDisp($status, $objFormParam->getValue('search_pageno'));
        // 配送情報を代入
        foreach ($objPage->arrStatus as $key_index => $value) {
            $order_id = $objPage->arrStatus[$key_index]['order_id'];
            $arrShippings = $objPurchase->getShippings($order_id, false);
            $objPage->arrStatus[$key_index]['shippings'] = $arrShippings;
        }
    }

    /**
    * 荷物追跡番号が登録されているかチェック
    * @param  array  $arrShippings  受注情報の配列
    * @return int    全てに宅配番号欄が登録されていれば0。
    */
    function lfCheckTrackingNo(&$arrShippings){
        $checkFlag = 0;
        foreach ($arrShippings as $index => $shippings) {
            if (!$shippings['plg_shippingadmin_tracking_no']){ ++$checkFlag; }
        }
        return $checkFlag;
    }
}
