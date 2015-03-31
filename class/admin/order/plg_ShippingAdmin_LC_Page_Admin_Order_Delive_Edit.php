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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_Order_Edit_Ex.php';

/**
 * ヘルプ のページクラス.
 *
 * @package Page
 * @author clicktx
 * @version $Id: LC_Page_ShippingAdmin 1 2014-07-17 00:00:00Z $
 */
class plg_ShippingAdmin_LC_Page_Admin_Order_Delive_Edit extends LC_Page_Admin_Order_Edit_Ex {

    public $arrShippingKeys = array(
        'shipping_id',
        'shipping_date_year',
        'shipping_date_month',
        'shipping_date_day',
        'time_id',
        'plg_shippingadmin_tracking_no',
    );

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR ."ShippingAdmin/templates/admin/order/delive_edit.tpl";
        $this->tpl_mainno = 'order';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = '配送情報登録';

        $objShippingDate = new SC_Date_Ex(RELEASE_YEAR);
        $this->arrYearShippingDate = $objShippingDate->getYear('', date('Y'), '');
        $this->arrMonthShippingDate = $objShippingDate->getMonth(true);
        $this->arrDayShippingDate = $objShippingDate->getDay(true);

        // 配送業者の取得
        $this->arrDeliv = SC_Helper_Delivery_Ex::getIDValueList();

        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objFormParam = new SC_FormParam_Ex();

        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();
        $order_id = $objFormParam->getValue('order_id');

        // DBから受注情報を読み込む
        $this->setOrderToFormParam($objFormParam, $order_id);

        switch ($this->getMode()) {
            case 'edit':
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $this->arrErr = $this->lfCheckError($objFormParam);

                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    $deliv_id = $objFormParam->getValue('deliv_id');
                    $order_id = $this->doUpdateDB($order_id, $objPurchase, $objFormParam);

                    $this->tpl_deliv_name = $this->arrDeliv[$deliv_id];
                    $this->tpl_complete = 1;
                    $this->arrTrackingNo = $objFormParam->arrValue['plg_shippingadmin_tracking_no'];
                }
                break;
            case 'deliv':
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                break;
            default:
                break;
        }

        $this->arrForm = $objFormParam->getFormParamList();
        $this->arrAllShipping = $objFormParam->getSwapArray($this->arrShippingKeys);
        $this->arrDelivTime = SC_Helper_Delivery_Ex::getDelivTime($objFormParam->getValue('deliv_id'));

        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        // parent::destroy();
    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        // 検索条件のパラメーターを初期化
        // parent::lfInitParam($objFormParam);
        $objFormParam->addParam('注文番号', 'order_id');
        $objFormParam->addParam('配送ID', 'shipping_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), 0);


        $objFormParam->addParam('配送業者', 'deliv_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('荷物追跡番号', 'plg_shippingadmin_tracking_no', STEXT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('お届け時間ID', 'time_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お届け日(年)', 'shipping_date_year', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お届け日(月)', 'shipping_date_month', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お届け日(日)', 'shipping_date_day', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お届け日', 'shipping_date', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * 受注データを取得して, SC_FormParam へ設定する.
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param  integer      $order_id     取得元の受注ID
     * @return void
     */
    public function setOrderToFormParam(&$objFormParam, $order_id)
    {
        $objPurchase = new SC_Helper_Purchase_Ex();

        // 受注詳細を設定
        $arrOrderDetail = $objPurchase->getOrderDetail($order_id, false);
        $objFormParam->setParam(SC_Utils_Ex::sfSwapArray($arrOrderDetail));

        $arrShippingsTmp = $objPurchase->getShippings($order_id);
        $arrShippings = array();
        foreach ($arrShippingsTmp as $row) {
            // お届け日の処理
            if (!SC_Utils_Ex::isBlank($row['shipping_date'])) {
                $ts = strtotime($row['shipping_date']);
                $row['shipping_date_year'] = date('Y', $ts);
                $row['shipping_date_month'] = date('n', $ts);
                $row['shipping_date_day'] = date('j', $ts);
            }
            $arrShippings[$row['shipping_id']] = $row;
        }
        $objFormParam->setParam(SC_Utils_Ex::sfSwapArray($arrShippings));

        /*
         * 受注情報を設定
         * $arrOrderDetail と項目が重複しており, $arrOrderDetail は連想配列の値
         * が渡ってくるため, $arrOrder で上書きする.
         */
        $arrOrder = $objPurchase->getOrder($order_id);
        $objFormParam->setParam($arrOrder);
    }

    /**
     * DB更新処理
     *
     * @param  integer            $order_id        受注ID
     * @param  SC_Helper_Purchase $objPurchase     SC_Helper_Purchase インスタンス
     * @param  SC_FormParam       $objFormParam    SC_FormParam インスタンス
     * @return integer            $order_id 受注ID
     *
     * エラー発生時は負数を返す。
     */
    public function doUpdateDB($order_id, &$objPurchase, &$objFormParam)
    {
        $arrValues = $objFormParam->getDbArray();

        // 受注テーブルの更新(配送業者のみ更新)
        $arrDeliv = array('deliv_id' => $arrValues['deliv_id']);
        $order_id = $objPurchase->registerOrder($order_id, $arrDeliv);

        // 配送情報テーブルの更新
        $arrDelivTime = SC_Helper_Delivery_Ex::getDelivTime($objFormParam->getValue('deliv_id'));
        $arrAllShipping = $objFormParam->getSwapArray($this->arrShippingKeys);
        $arrShippingValues = array();
        foreach ($arrAllShipping as $shipping_index => $arrShipping) {
            $shipping_id = $arrShipping['shipping_id'];
            $arrShippingValues[$shipping_index] = $arrShipping;

            $arrShippingValues[$shipping_index]['shipping_date']
                = SC_Utils_Ex::sfGetTimestamp($arrShipping['shipping_date_year'],
                                              $arrShipping['shipping_date_month'],
                                              $arrShipping['shipping_date_day']);
            // 配送業者IDを取得
            $arrShippingValues[$shipping_index]['deliv_id'] = $objFormParam->getValue('deliv_id');

            // お届け時間名称を取得
            $arrShippingValues[$shipping_index]['shipping_time'] = $arrDelivTime[$arrShipping['time_id']];
        }

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'dtb_shipping';
        $where = 'order_id = ? AND shipping_id = ?';

        $objQuery->begin();
        foreach ($arrShippingValues as $key => $arrShipping) {
            $arrValues = $objQuery->extractOnlyColsOf($table, $arrShipping);
            $arrValues['order_id'] = $order_id;
            $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->update($table, $arrValues, $where, array($order_id, $arrValues['shipping_id']));
        }
        $objQuery->commit();
    }
}
?>
