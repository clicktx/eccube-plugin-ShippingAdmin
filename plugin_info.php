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
 * プラグイン の情報クラス.
 *
 * @package ShippingAdmin
 * @author clicktx
 */
class plugin_info {
    /** プラグインコード(必須)：システム上でのキーとなります。プラグインコードは一意である必要があります。 */
    static $PLUGIN_CODE        = "ShippingAdmin";
    /** プラグイン名(必須)：プラグイン管理・画面出力（エラーメッセージetc）にはこの値が出力されます。 */
    static $PLUGIN_NAME        = "管理機能 出荷業務効率化プラグイン";
    /** プラグインメインクラス名(必須)：本体がプラグインを実行する際に呼ばれるクラス。拡張子は不要です。 */
    static $CLASS_NAME         = "ShippingAdmin";
    /** プラグインバージョン(必須) */
    static $PLUGIN_VERSION     = "0.1.2";
    /** 本体対応バージョン(必須) */
    static $COMPLIANT_VERSION  = "2.13.0～2.13.3";
    /** 作者(必須) */
    static $AUTHOR             = "clicktx";
    /** 説明(必須) */
    static $DESCRIPTION        = "管理機能の出荷業務効率化するプラグイン。宅配伝票入力や出荷処理後自動でメール送信等。";
    /** 作者用のサイトURL：設定されている場合はプラグイン管理画面の作者名がリンクになります。 */
    static $AUTHOR_SITE_URL    = "http://perl.no-tubo.net/";
    /** プラグインのサイトURL : 設定されている場合はプラグイン管理画面の作者名がリンクになります。 */
    static $PLUGIN_SITE_URL   = "https://github.com/clicktx/eccube-plugin-ShippingAdmin";
    /** 使用するフックポイント：使用するフックポイントを設定すると、フックポイントが競合した際にアラートが出ます。 */
    /**
     * 使用するフックポイント・コールバック関数。
     * 使用するフックポイントを設定すると、フックポイントが競合した際にアラートが出ます。
     * ここで宣言することで、インストール時にdtb_plugin_hoolpointsに登録され、register関数を書かずにフックポイントでの介入が可能です。*/
    static $HOOK_POINTS = "LC_Page_Admin_Order_Edit_action_before, LC_Page_Admin_Order_Disp_action_before, LC_Page_Admin_Order_action_before, LC_Page_Admin_Order_Status_action_after, prefilterTransform, loadClassFileChange, SC_FormParam_construct";
    /** ライセンス */
    static $LICENSE        = "LGPL";
}
?>
