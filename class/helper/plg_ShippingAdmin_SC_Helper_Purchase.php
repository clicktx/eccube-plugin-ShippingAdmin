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
 * 商品購入関連のヘルパークラス. オーバーライド
 *
 * @author clicktx
 * @version
 */
class plg_ShippingAdmin_SC_Helper_Purchase extends SC_Helper_Purchase
{
    /**
     * 注文受付メールを送信する.override
     *
     * 端末種別IDにより, 携帯電話の場合は携帯用の文面,
     * それ以外の場合は PC 用の文面でメールを送信する.
     *
     * plugin:
     *      - 支払い方法によって送信メールを変える
     *      - 振込の場合はオーダーステータスを入金待ちにする
     *          - LC_Page_Shopping_Confirm  mode=confirmの場合はリダイレクトされてしまうため
     *          - LC_Page_Shopping_Confirm_action_afterフック出来ないため
     *
     * @param integer $order_id 受注ID
     * @param  object  $objPage LC_Page インスタンス
     * @return boolean 送信に成功したか。現状では、正確には取得できない。
     */
    public static function sendOrderMail($order_id, &$objPage = NULL)
    {
        $arrOrder = SC_Helper_Purchase::getOrder($order_id);
        if (empty($arrOrder)) {
            return false; // 失敗
        }
        $payment_id = $arrOrder['payment_id'];

        // オーダーステータス変更
        SC_Helper_Purchase_EX::plg_ShippingAdmin_changeStatusOrderPayWait($order_id, $payment_id);

        // 受注メール送信
        $objMail = new SC_Helper_Mail_Ex();

        // setPageは、プラグインの処理に必要(see #1798)
        if (is_object($objPage)) {
            $objMail->setPage($objPage);
        }

        // 支払い方法によってメールテンプレートを変更する
        $arrOrderMailTemplate = SC_Helper_Purchase_EX::plg_ShippingAdmin_getTemplateId();
        $template_id = $arrOrderMailTemplate[$payment_id];

        // $template_id = $arrOrder['device_type_id'] == DEVICE_TYPE_MOBILE ? 2 : 1;
        if (!$template_id){ $template_id = 1; }
        $objMail->sfSendOrderMail($order_id, $template_id);

        return true; // 成功
    }

    /**
     * 入金待ちにする支払い方法IDを取得
     *
     * とりあえずベタ書き。あとで設定ページを作れば親切。
     *
     * @return array
     */
    public static function plg_ShippingAdmin_getPaymentId(){
        $arrPaymentId = array(3);
        return $arrPaymentId;
    }

    /**
     * 銀行振込の場合はステータスを入金待ちにする
     *
     * @param integer $order_id   受注ID
     * @param integer $payment_id 支払い方法ID
     * @return void
     */
    public static function plg_ShippingAdmin_changeStatusOrderPayWait($order_id, $payment_id){
        // オーダーステータス変更
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $arrPaymentId = $objPurchase->plg_ShippingAdmin_getPaymentId();
        $objQuery->begin();
        if (in_array($payment_id, $arrPaymentId)){
            $objPurchase->sfUpdateOrderStatus($order_id, ORDER_PAY_WAIT);
        }
        $objQuery->commit();
    }

    /**
     * 受注メール自動振り分け用テンプレート取得
     *
     * @return array  支払いIDとテンプレートIDの配列
     */
    public static function plg_ShippingAdmin_getTemplateId(){
        $arrOrderMailTemplate = array(
            3 => 30, // JNB
            5 => 50, // PayPal
        );
        return $arrOrderMailTemplate;
    }
}
