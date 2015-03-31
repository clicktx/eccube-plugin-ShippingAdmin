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

// 管理
require_once PLUGIN_UPLOAD_REALDIR . 'ShippingAdmin/class/admin/order/plg_ShippingAdmin_LC_Page_Admin_Order.php';
require_once PLUGIN_UPLOAD_REALDIR . 'ShippingAdmin/class/admin/order/plg_ShippingAdmin_LC_Page_Admin_Order_Status.php';


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
        if(!file_exists(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/js"))mkdir(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code']. "/js");
        if(copy(PLUGIN_UPLOAD_REALDIR . "ShippingAdmin/html/js/jquery.excolorboxform-0.1.3.js", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/js/jquery.excolorboxform-0.1.3.js") === false) {
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', false, PLUGIN_UPLOAD_REALDIR . PLUGIN_HTML_REALDIR . ' に書き込めません。パーミッションをご確認ください。');
        }
        if(!file_exists(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/css"))mkdir(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/css");
        if(copy(PLUGIN_UPLOAD_REALDIR . "ShippingAdmin/html/css/plg_ShippingAdmin.css", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/css/plg_ShippingAdmin.css") === false) {
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', false, PLUGIN_UPLOAD_REALDIR . PLUGIN_HTML_REALDIR . ' に書き込めません。パーミッションをご確認ください。');
        }

        // plugin用HTML_dir以外の場所はアンインストール時に削除する
        if(copy(PLUGIN_UPLOAD_REALDIR . "ShippingAdmin/plg_ShippingAdmin_delive_edit.php", HTML_REALDIR . "admin/order/plg_ShippingAdmin_delive_edit.php") === false) {
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', false, PLUGIN_UPLOAD_REALDIR . HTML_REALDIR . ' に書き込めません。パーミッションをご確認ください。');
        }
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
        if ($classname == 'SC_Helper_Purchase_Ex') {
            $classpath = PLUGIN_UPLOAD_REALDIR . 'ShippingAdmin/class/helper/plg_ShippingAdmin_SC_Helper_Purchase.php';
            $classname = 'plg_ShippingAdmin_SC_Helper_Purchase';
        }
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
            $param->addParam('自動メールを送信チェック', 'disable_auto_mail', INT_LEN, 'n');
            $param->addParam('削除確認', 'del_check', INT_LEN, 'n');
        }
    }

    /**
     * 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     *
     * @param SC_Helper_Plugin $objHelperPlugin
     */
    function register(SC_Helper_Plugin $objHelperPlugin) {
        $objHelperPlugin->addAction("LC_Page_Admin_Order_Edit_action_before", array(&$this, "push_arrShippingKeys"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("LC_Page_Admin_Order_Disp_action_before", array(&$this, "push_arrShippingKeys"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("LC_Page_Admin_Order_action_before", array(new plg_ShippingAdmin_LC_Page_Admin_Order(), "action_before"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("LC_Page_Admin_Order_Status_action_after", array(new plg_ShippingAdmin_LC_Page_Admin_Order_Status(), "action_after"), $this->arrSelfInfo['priority']);

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
}
?>
