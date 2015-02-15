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
 * プラグイン のメインクラス.
 *
 * @package ShippingAdmin
 * @author clicktx
 */
class ShippingAdmin extends SC_Plugin_Base {

    /**
     * コンストラクタ
     *
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
    }

    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    function install($arrPlugin) {
        $class_name = get_class();

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        // dtb_shipping テーブルに plg_shippingadmin_tracking_no カラムを追加する
        // memo: レコードが多い場合でもALTER TABLEして大丈夫？
        // memo: index付けたほうが...
        $arrSql = array(
            "ALTER TABLE dtb_shipping ADD plg_shippingadmin_tracking_no VARCHAR(30);"
        );
        foreach ($arrSql as $sql) {
            $objQuery->exec($sql);
        }
        $objQuery->commit();

        // ファイルのコピー
        // memo: plugin用HTML_dirにコピーするのでuninnstall時には削除必要ない？
        if(!file_exists(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "js"))mkdir(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code']. "/js");
        if(copy(PLUGIN_UPLOAD_REALDIR . "ShippingAdmin/html/js/jquery.excolorboxform-0.1.3.js", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/js/jquery.excolorboxform-0.1.3.js") === false) print_r("失敗");
        if(!file_exists(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "css"))mkdir(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/css");
        if(copy(PLUGIN_UPLOAD_REALDIR . "ShippingAdmin/html/css/plg_ShippingAdmin.css", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/css/plg_ShippingAdmin.css") === false) print_r("失敗");

        // plugin用HTML_dir以外の場所はアンインストール時に削除する
        if(copy(PLUGIN_UPLOAD_REALDIR . "ShippingAdmin/class/admin/order/plg_ShippingAdmin_delive_edit.php", HTML_REALDIR . "admin/order/plg_ShippingAdmin_delive_edit.php") === false) print_r("失敗");
    }

    /**
     * アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function uninstall($arrPlugin) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        // plg_shippingadmin_tracking_no カラムを削除する
        $arrSql = array(
            "ALTER TABLE dtb_shipping DROP plg_shippingadmin_tracking_no;"
        );
        foreach ($arrSql as $sql) {
            $objQuery->exec($sql);
        }
        $objQuery->commit();

        // ファイル削除
        if(SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . "admin/order/plg_ShippingAdmin_delive_edit.php") === false); // TODO エラー処理
    }

    /**
     * アップデート
     * updateはアップデート時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function update($arrPlugin) {
        // nop
    }

    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function enable($arrPlugin) {
    }

    /**
     * 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function disable($arrPlugin) {
    }

    /**
     * SC_系クラス読込コールバック関数
     *
     * @param string &$classname クラス名
     * @param string &$classpath クラスファイルパス
     * @return void
     */
    function loadClassFileChange(&$classname, &$classpath) {
    }

    /**
     * プレフィルタコールバック関数
     *
     * @param string     &$source  テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage  ページオブジェクト
     * @param string     $filename テンプレートのファイル名
     * @return void
     */
    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        $objTransform = new SC_Helper_Transform($source);
        $template_dir = PLUGIN_UPLOAD_REALDIR ."ShippingAdmin/templates/";

        switch($objPage->arrPageLayout['device_type_id']) {
            // 端末種別：PC
            case DEVICE_TYPE_PC:
                $template_dir .= "default/";
                break;
            // 端末種別：モバイル
            case DEVICE_TYPE_MOBILE:
                $template_dir .= "mobile/";
                break;
            // 端末種別：スマートフォン
            case DEVICE_TYPE_SMARTPHONE:
                break;
            // 端末種別：管理画面
            case DEVICE_TYPE_ADMIN:
            default:
                $template_dir .= "admin/";
                // CSS読み込み
                $objTransform->select("head")->appendChild('<link rel="stylesheet" href="' . ROOT_URLPATH . 'plugin/ShippingAdmin/css/plg_ShippingAdmin.css" type="text/css" media="all">');

                // 管理機能 受注管理
                if(strpos($filename, "order/index.tpl") !== false) {
                    $objTransform->select("form#search_form table", 0)->appendChild(file_get_contents($template_dir . "order/index_search_form.tpl"));
                    $objTransform->select("form#form1")->replaceElement(file_get_contents($template_dir . "order/index_form1.tpl"));
                }
                // 受注情報登録・編集画面
                elseif(strpos($filename, "order/edit.tpl") !== false) {
                    $objTransform->select("table.form", 2)->appendChild(file_get_contents($template_dir . "order/order_edit.tpl"));
                }
                // 受注管理＞対応状況管理
                elseif(strpos($filename, "order/status.tpl") !== false) {
                    $objTransform->select("div.btn", 1)->find("a.btn-normal")->replaceElement(file_get_contents($template_dir . "order/order_status_btn.tpl"));
                    $objTransform->select("table.list")->replaceElement(file_get_contents($template_dir . "order/order_status_table.tpl"));
                }
                // 管理機能＞受注情報表示
                elseif(strpos($filename, "order/disp.tpl") !== false) {
                    $objTransform->select("table.form", 2)->appendChild(file_get_contents($template_dir . "order/order_disp.tpl"));
                }
                break;
        }
        $source = $objTransform->getHTML();
    }


    /**
    * フォームパラメーター追加
    * @return void
    */
    function addParam($class_name, $param) {
        if (strpos($class_name, 'LC_Page_Admin_Order') !== false
                or strpos($class_name, 'ShippingAdmin') !== false
        ) {
            $param->addParam('荷物追跡番号', 'search_plg_shippingadmin_tracking_no', STEXT_LEN, 'n', array('MAX_LENGTH_CHECK'));
            $param->addParam('配送業者', 'search_deliv_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
            $param->addParam('開始年', 'search_sdelivedyear', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $param->addParam('開始月', 'search_sdelivedmonth', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $param->addParam('開始日', 'search_sdelivedday', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $param->addParam('終了年', 'search_edelivedyear', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $param->addParam('終了月', 'search_edelivedmonth', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $param->addParam('終了日', 'search_edelivedday', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        }
        if (strpos($class_name, 'LC_Page_Admin_Order_Edit') !== false
                or strpos($class_name, 'LC_Page_Admin_Order_Disp') !== false
        ) {
            $param->addParam('荷物追跡番号', 'plg_shippingadmin_tracking_no', STEXT_LEN, 'n', array('MAX_LENGTH_CHECK'));
        }
        if ($this->getMode() == 'plg_shippingadmin_update') {
            $param->addParam('変更後対応状況', 'change_status', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $param->addParam('移動注文番号', 'move', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $param->addParam('削除確認', 'del_check', INT_LEN, 'n');
        }
    }

    /**
     * SC_系処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     *
     * @param SC_Helper_Plugin $objHelperPlugin
     */
    function register(SC_Helper_Plugin $objHelperPlugin) {
        $objHelperPlugin->addAction("LC_Page_Admin_Order_Edit_action_before", array(&$this, "push_arrShippingKeys"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("LC_Page_Admin_Order_Disp_action_before", array(&$this, "push_arrShippingKeys"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("LC_Page_Admin_Order_action_before", array(&$this, "admin_order_before"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("LC_Page_Admin_Order_Status_action_after", array(&$this, "admin_order_status_after"), $this->arrSelfInfo['priority']);

        $objHelperPlugin->addAction("prefilterTransform", array(&$this, "prefilterTransform"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("SC_FormParam_construct", array(&$this, "addParam"), $this->arrSelfInfo['priority']);
    }


    /**
    * 管理機能 受注登録(編集)、受注情報表示
    *
    * @param  $objPage
    * @return void
    */
    function push_arrShippingKeys($objPage) {
        // キーワードにplg_shippingadmin_tracking_noを追加しておく
        array_push($objPage->arrShippingKeys, 'plg_shippingadmin_tracking_no');
    }

    /**
    * 管理機能 受注情報リスト.
    *
    * @param LC_Page_Admin_Order $objPage 管理受注情報リスト のページクラス.
    * @return void
    */
    function admin_order_before($objPage) {
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

    /** memo: mode=csv時の SC_Response_Ex::actionExit(); でエラーがでるための対策
     * リクエストパラメーター 'mode' を取得する.
     *
     * 1. $_REQUEST['mode'] の値を取得する.
     * 2. 存在しない場合は null を返す.
     *
     * mode に, 半角英数字とアンダーバー(_) 以外の文字列が検出された場合は null を
     * 返す.
     *
     * @access protected
     * @return string|null $_REQUEST['mode'] の文字列
     */
    public function getMode()
    {
        $pattern = '/^[a-zA-Z0-9_]+$/';
        $mode = null;
        if (isset($_REQUEST['mode']) && preg_match($pattern, $_REQUEST['mode'])) {
            $mode =  $_REQUEST['mode'];
        }

        return $mode;
    }

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

    /**
    * 管理機能 対応状況管理 のページクラス.
    *
    * @param LC_Page_Admin_Order_Status $objPage 管理対応状況管理 のページクラス.
    * @return void
    */
    function admin_order_status_after($objPage) {
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
    * @param
    */
    function lfCheckTrackingNo(&$arrShippings){
        $checkFlag = 0;
        foreach ($arrShippings as $index => $shippings) {
            if (!$shippings['plg_shippingadmin_tracking_no']){ ++$checkFlag; }
        }
        return $checkFlag;
    }
}
?>
