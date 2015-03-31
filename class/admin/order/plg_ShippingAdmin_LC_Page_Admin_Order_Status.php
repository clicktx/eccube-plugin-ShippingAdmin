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

        // 自動メールを送信するステータス一覧を取得
        $arrAutoMailOrderStatus = $this->getOrderStatusMailTemplateIds();
        $objPage->tpl_auto_mail_order_status = $arrAutoMailOrderStatus;

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

                        // メール送信
                        $disable_auto_mail = $objFormParam->getValue('disable_auto_mail');
                        if (!$disable_auto_mail && $arrAutoMailOrderStatus[$changeStatus]){
                            $this->doSendMail($arrMoveOderId, $arrAutoMailOrderStatus[$changeStatus]);
                        }
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

    /**
    * ステータス変更で送信するメールテンプレート
    * memo: とりあえずベタ書き。設定ページがあると親切(後で作る！)  
    * @param
    * @return  array  ステータスに関連付けられたメールテンプレートIDの配列
    */
    function getOrderStatusMailTemplateIds(){
        return array(
            // 1 => false, // 新規受付
            // 2 => false, // 入金待ち
            3 => 3, // キャンセル時
            // 4 => false, // 取り寄せ中（発送準備中）
            5 => 50, // 発送時
            6 => 20, // 入金時
            // 7 => false, // 決済処理中
        );
    }

    /**
    * ステータス変更時のメール送信
    * @param  array  $arrSendOderId   送信するorderID
    * @param  int    $template_id  メールテンプレートID
    * @return void
    */
    function doSendMail($arrSendOderId, $template_id){
        $objMail = new SC_Helper_Mail_Ex();

        foreach ($arrSendOderId as $order_id) {
            $objMail->sfSendOrderMail($order_id, $template_id);
        }
    }
}
