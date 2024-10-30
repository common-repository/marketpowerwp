<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 5/27/2016
 * Time: 11:32 PM
 */

namespace Multisoft\MPP\Replication;

use LePlugin\Core\AbstractController;
use Multisoft\MPP\Core\CoreController;
use Multisoft\MPP\Settings\SettingsGateway;
use LePlugin\Core\View;
use LePlugin\Core\Utils;
use Katzgrau\KLogger\Logger;
use Psr\Log\LogLevel;

class ReplicationController extends AbstractController
{

    const MENU_SLUG = 'replication';
    const SECTION_MPP_REPLICATION = 'mpp_replication_section';
    const OPTION_NON_EXIST_REDIRECTION = 'nonexistent_site_redirection';
    const OPTION_AUTOPREFIX = 'autoprefix';
    const OPTION_REPLICATION_PATH = 'replication_path';
    const OPTION_REPLICATION_PATH_QUERY_FORMAT = 'replication_path_query_format';
    const OPTION_CHECK_SITE_NAME_PATH = 'check_site_name_path';
    const OPTION_CHECK_SITE_NAME_PATH_QUERY_FORMAT = 'check_site_name_path_query_format';
    const OPTION_DEFAULT_REPLICATED_SITE_NAME = 'default_replicated_site_name';
    const OPTION_REPLICATION_CF7_FORMS = 'replication_cf7_forms';

    private $replicationGateway;
    private $mpp_error_view;

    protected function setup()
    {
        $this->enable_activation_hook();
        $this->setup_settings();
        $this->add_action('init', 'mpp_replication_check_site_name');
        $this->add_action('admin_notices', 'mppe_replication_message');
        $this->add_shortcode('mppe', 'mppe_replication_info');
        if (!defined('WP_HOME')) {
            $this->add_filter('option_home', 'replication_home_url');
        }
        if (!defined('WP_SITEURL')) {
            $this->add_filter('option_siteurl', 'replication_site_url');
        }
        if (is_plugin_active('wordpress-seo/wp-seo.php')) {
            $this->add_filter('wpseo_canonical', 'replication_seo_canon_compat');
        }
        if (is_plugin_active('all-in-one-seo-pack-pro/all_in_one_seo_pack.php')) {
            $this->add_filter('aioseop_canonical_url', 'replication_seo_canon_compat');
        }
        if (is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
            $this->add_action('wpcf7_before_send_mail', 'replication_cf7');
        }
        if (file_exists(ABSPATH . "logs")) {
            $logDir = ABSPATH . "logs";
        } else {
            $logDir = $this->_plugin_dir . '/logs';
        }

        if (MPP_DEBUG) {
            $logger = new Logger($logDir, LogLevel::DEBUG, array(
                'extension' => 'log',
                'prefix' => 'mpp_replication_debug_'
            ));
        } else {
            $logger = new Logger($logDir, LogLevel::ERROR, array(
                'extension' => 'log',
                'prefix' => 'mpp_replication_'
            ));
        }
        $this->replicationGateway = ReplicationGateway::getInstance($logger);

        $this->add_submenu_page(
            CoreController::MENU_SLUG,
            "Multisoft MarketPowerPRO Replication Shortcodes",
            "Replication Shortcodes",
            CoreController::CAP,
            self::MENU_SLUG,
            [$this, 'index']
        );

        $this->add_action('wp_enqueue_scripts', 'mppe_enqueue_autoprefix', 99999);

    }

    /**
     * @param $canonical_url
     */
    public function replication_seo_canon_compat($canonical_url)
    {
        /**
         * @var $replicationGateway ReplicationGateway
         */
        $replicationGateway = ReplicationGateway::getInstance();
        $replicatedSiteName = $replicationGateway->get_replication_site_name(true);
        if ($replicatedSiteName) {
            return Utils::str_replace_first($replicatedSiteName . ".", "", $canonical_url);
        }
        return $canonical_url;
    }

    public function activate()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $default_replication_path = $this->_config->__get('default_multisoft_mpp_replication_path');
        if ($default_replication_path) {
            $settingsGateway->update(
                self::OPTION_REPLICATION_PATH,
                $default_replication_path
            );
        }
        $default_replication_path_qf = $this->_config->__get('default_multisoft_mpp_replication_query_format');
        if ($default_replication_path_qf) {
            $settingsGateway->update(
                self::OPTION_REPLICATION_PATH_QUERY_FORMAT,
                $default_replication_path_qf
            );
        }
        $default_check_site_name_path = $this->_config->__get('default_multisoft_mpp_check_site_name_path');
        if ($default_check_site_name_path) {
            $settingsGateway->update(
                self::OPTION_CHECK_SITE_NAME_PATH,
                $default_check_site_name_path
            );
        }
        $default_check_site_name_path_qf = $this->_config->__get('default_multisoft_mpp_check_site_name_query_format');
        if ($default_check_site_name_path_qf) {
            $settingsGateway->update(
                self::OPTION_CHECK_SITE_NAME_PATH_QUERY_FORMAT,
                $default_check_site_name_path_qf
            );
        }

        $default_replicated_site_name = $this->_config->__get('default_replicated_site_name');
        if ($default_replicated_site_name) {
            $settingsGateway->update(
                self::OPTION_DEFAULT_REPLICATED_SITE_NAME,
                $default_replicated_site_name
            );
        }

        $settingsGateway->update(self::OPTION_AUTOPREFIX, true);
    }

    private function setup_settings()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $settingsGateway->addSettingsSection(
            $settingsGateway::GENERAL_TAB,
            self::SECTION_MPP_REPLICATION,
            'Replication Settings',
            [$this, "mpp_replication_general_settings"]
        );
        $settingsGateway->registerSetting(
            $settingsGateway::GENERAL_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_NON_EXIST_REDIRECTION
        );
        $settingsGateway->registerSetting(
            $settingsGateway::GENERAL_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_AUTOPREFIX
        );
        $settingsGateway->registerSetting(
            $settingsGateway::GENERAL_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_DEFAULT_REPLICATED_SITE_NAME
        );

        $settingsGateway->addSettingsSection(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_REPLICATION,
            'Replication Settings',
            [$this, "mpp_replication_advanced_settings"]
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_REPLICATION_PATH
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_REPLICATION_PATH_QUERY_FORMAT
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_CHECK_SITE_NAME_PATH
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_CHECK_SITE_NAME_PATH_QUERY_FORMAT
        );

        $settingsGateway->addSettingsSection(
            $settingsGateway::INTEGRATIONS_TAB,
            self::SECTION_MPP_REPLICATION,
            'Replication Integration Settings',
            [$this, "mpp_replication_integration_settings"]
        );
        $settingsGateway->registerSetting(
            $settingsGateway::INTEGRATIONS_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_REPLICATION_CF7_FORMS
        );
    }

    public function index()
    {
        $view = new View($this, 'mpp/replication/shortcodes.php');
        $sample_content = "[mppe]Hello my name is MPPE_FIRSTNAME, " .
            "I live in MPPE_ADDRESS1 MPPE_ADDRESS2 MPPE_CITY, MPPE_COUNTRYNAME MPPE_POSTALCODE. " .
            "My MPP Distributor ID is MPPE_DISTRIBUTORID, my e-mail address is MPPE_EMAIL and my common id is MPPE_COMMONID[/mppe]!";
        $parsed = do_shortcode($sample_content);
        $view->assign('sample', $sample_content);
        $view->assign('parsed_sample', $parsed);
        $view->display();
    }

    public function replication_site_url($url)
    {
        if (is_admin()) {
            return $url;
        }
        $href = parse_url($url);
        return $href['scheme'] . '://' . $_SERVER['HTTP_HOST'];
    }

    public function replication_home_url($url)
    {
        if (is_admin()) {
            return $url;
        }
        $href = parse_url($url);
        return $href['scheme'] . '://' . $_SERVER['HTTP_HOST'];
    }

    public function mppe_enqueue_autoprefix()
    {
        $settingsGateway = SettingsGateway::getInstance();
        $autoprefix = $settingsGateway->get(self::OPTION_AUTOPREFIX);

        if ($autoprefix) {
            wp_enqueue_script('mppe-jurlp', $this->_js_dir_url . 'jurlp/jurlp.min.js', ['jquery'], null, true);
            wp_register_script('mppe-autoprefix', $this->_js_dir_url . 'mpp/autoprefix.js', ['jquery', 'mppe-jurlp'], '0', true);

            $replicationGateway = $this->replicationGateway;
            $xxx = $replicationGateway->get_replication_site_name(true);
            $host = $replicationGateway->get_original_site_url();
            $web_address_host = Utils::get_domain($settingsGateway->get(CoreController::OPTION_WEB_ADDRESS));
            wp_localize_script('mppe-autoprefix', 'MPPAUTOPREFIX', [
                    "replicated_site_name" => $xxx,
                    "mpp_base_url" => $web_address_host,
                    "site_url" => $host
                ]
            );

            wp_enqueue_script('mppe-autoprefix');
        }
    }

    public function mppe_replication_info($atts, $content = null)
    {
        /* @var $replicationGateway ReplicationGateway */

        $replicationGateway = ReplicationGateway::getInstance();

        $info = false;
        $default = false;

        extract(shortcode_atts(array(
            "info" => false,
            "default" => false
        ), $atts));

        $mppe_info = $replicationGateway->get_replication_info(MPP_DEBUG);
        if (is_wp_error($mppe_info)) {
            $error_view = new View($this, 'mpp/error_view.php');
            $error_view->assign('code', $mppe_info->get_error_code());
            $error_view->assign('message', $mppe_info->get_error_message());
            $content = $error_view->getHtml() . $content;
        } else if ($mppe_info) {
            if ($info) {
                $info = str_replace("MPPE_", "", str_replace("&quot;", "", $info));
                $mppe_data = trim($mppe_info[strtoupper($info)]);
                if (empty($mppe_data) && $default) {
                    $defaults = explode(",", $default);
                    foreach ($defaults as $default) {
                        $default = trim(str_replace("&quot;", "", $default));
                        $key_default = strtoupper($default);
                        if (strpos($key_default, "MPPE_") === 0) {
                            $key = str_replace("MPPE_", "", $key_default);
                            if (isset($mppe_info[$key]) && !empty(trim($mppe_info[$key]))) {
                                $mppe_data = $mppe_info[$key];
                                break;
                            }
                        } else {
                            $mppe_data = $default;
                            break;
                        }
                    }
                }
                return $mppe_data;
            }
            if ($content) {
                foreach ($mppe_info as $key => $value) {
                    $content = str_replace("MPPE_" . $key, $value, $content);
                }
            }
        }

        return $content;
    }

    public function mpp_replication_check_site_name()
    {
        /* @var $replicationGateway ReplicationGateway */
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $replicationGateway = $this->replicationGateway;
        $site_name = $replicationGateway->get_replication_site_name(true);
        if ($site_name) {
            $valid = $replicationGateway->check_site_name($site_name);
            $this->mpp_error_view = new View($this, '');
            if (is_wp_error($valid)) {
                /* @var $valid \WP_Error */
                $this->mpp_error_view->addNotice(
                    'Multisoft MarketPowerPRO ' .
                    $valid->get_error_code() . ': ' . $valid->get_error_message(),
                    'notice-error', false
                );
            } else if ($valid === false) {
                if (is_admin()) {
                    $this->mpp_error_view->addNotice(
                        'Multisoft MarketPowerPRO INVALID_SITE_NAME: ' .
                        'Invalid/non-existent replication site name: ' . $site_name,
                        'notice-error', false
                    );
                } else {
                    add_action("template_redirect", [$this, "mppe_invalid_site_name_redirect"]);
                }
            }
        }
    }

    public function mppe_invalid_site_name_redirect()
    {
        $settingsGateway = SettingsGateway::getInstance();
        $redirection_page = $settingsGateway->get(self::OPTION_NON_EXIST_REDIRECTION);
        global $wpdb;
        $prefix = $wpdb->prefix;
        $original_site_url = $wpdb->get_var("SELECT option_value FROM {$prefix}options WHERE option_name = 'siteurl'");
        if ($redirection_page) {
            $url = strtolower(get_permalink($redirection_page));
            $url = Utils::str_replace_first(strtolower(site_url()), $original_site_url, $url);
        } else {
            $url = strtolower($original_site_url);
        }
        wp_redirect($url);
        exit();
    }

    public function mppe_replication_message()
    {
        if ($this->mpp_error_view) {
            /* @var $errorView \LePlugin\Core\View */
            $errorView = $this->mpp_error_view;
            $errorView->displayNotices();
        }
    }

    public function mpp_replication_general_settings()
    {

        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $replicationView = new View($this, 'mpp/replication/general_settings_section.php');
        $replicationView->assign('page_drop_down_args', array(
            'depth' => 0,
            'child_of' => 0,
            'selected' => $settingsGateway->get(self::OPTION_NON_EXIST_REDIRECTION, 0),
            'echo' => 1,
            'name' => self::OPTION_NON_EXIST_REDIRECTION,
            'id' => null,
            'class' => null,
            'show_option_none' => "Select Page",
            'show_option_no_change' => null,
            'option_none_value' => null
        ));
        $replicationView->assign('autoprefix', $settingsGateway->get(
            self::OPTION_AUTOPREFIX, false
        ));
        $replicationView->assign('replicated_site_name', $settingsGateway->get(
            self::OPTION_DEFAULT_REPLICATED_SITE_NAME
        ));
        $replicationView->display();
    }

    public function mpp_replication_advanced_settings()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $replicationView = new View($this, 'mpp/replication/advanced_settings_section.php');
        $replicationView->assign('replication_path',
            $settingsGateway->get(self::OPTION_REPLICATION_PATH, ''));
        $replicationView->assign('replication_path_query_format',
            $settingsGateway->get(self::OPTION_REPLICATION_PATH_QUERY_FORMAT, ''));
        $replicationView->assign('check_site_name_path',
            $settingsGateway->get(self::OPTION_CHECK_SITE_NAME_PATH, ''));
        $replicationView->assign('check_site_name_path_query_format',
            $settingsGateway->get(self::OPTION_CHECK_SITE_NAME_PATH_QUERY_FORMAT, ''));
        $replicationView->display();
    }

    public function mpp_replication_integration_settings()
    {
        $settingsGateway = SettingsGateway::getInstance();
        $replicationView = new View($this, 'mpp/replication/integration_settings_section.php');
        $replicationView->assign('active_integrations', [
            "cf7" => is_plugin_active('contact-form-7/wp-contact-form-7.php')
        ]);
        $cf7Forms = get_posts(['post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1]);
        $replicationView->assign('all_cf7_forms', $cf7Forms);
        $replicationView->assign('replication_cf7_forms',
            $settingsGateway->get(self::OPTION_REPLICATION_CF7_FORMS, ''));
        $replicationView->display();
    }

    public function replication_cf7($cf7)
    {
        /**
         * @var $cf7 \WPCF7_ContactForm
         */
        $replicationGateway = ReplicationGateway::getInstance();
        $siteName = $replicationGateway->get_replication_site_name(true);
        if ($siteName) {
            $replicationInfo = $replicationGateway->get_replication_info(MPP_DEBUG);
            if (!empty($replicationInfo['EMAIL'])) {
                $submission = \WPCF7_Submission::get_instance();
                $cf7Mail = $cf7->get_properties('mail');
                $cf7Mail['mail']['recipient'] = $replicationInfo['EMAIL'];
                $cf7->set_properties(['mail' => $cf7Mail['mail']]);
            }
        }
        return $cf7;
    }
}
