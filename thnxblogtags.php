<?php

/*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2018 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
class thnxblogtags extends Module implements WidgetInterface {
	public $css_files = array(
		array(
			'key' => 'thnxblogtags',
			'src' => 'thnxblogtags.css',
			'priority' => 50,
			'media' => 'all',
			'load_theme' => false,
		),
	);
	public $js_files = array(
		array(
			'key' => 'thnxblogtags',
			'src' => 'thnxblogtags.js',
			'priority' => 50,
			'position' => 'bottom', // bottom or head
			'load_theme' => false,
		),
	);
	public function __construct(){
		$this->name = 'thnxblogtags';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'thanksit.com';
		$this->bootstrap = true;
		// $this->dependencies = array('thnxblog');
		parent::__construct();
		$this->displayName = $this->l('thnxBlog Tags');
		$this->description = $this->l('Display Tags Module');
		$this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
	}
	// For installation service
	public function install() {
		if (!parent::install()
			// || !$this->registerHook('displayheader')
			// || !$this->registerHook('displayhome')
			// || !$this->registerHook('LeftColumn')
			// || !$this->registerHook('RightColumn')
			|| !$this->registerHook('displaythnxblogleft')
			|| !$this->registerHook('displaythnxblogright')
			)
			return false;
		$languages = Language::getLanguages(false);
           	foreach($languages as $lang){
        		Configuration::updateValue('thnxbt_title_'.$lang['id_lang'],"Blog Tags");
           	}
        	Configuration::updateValue('thnxbt_tagcount',4);
			return true;
	}
	// For uninstallation service
	public function uninstall() {
		if (!parent::uninstall()
			)
			return false;
		else
			return true;
	}
	// Helper Form for Html markup generate
	public function SettingForm() {

		$default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Setting'),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button',
			),
		);
			$this->fields_form[0]['form']['input'][] = array(
				'type' => 'text',
				'label' => $this->l('Title'),
				'name' => 'thnxbt_title',
				'lang' => true,
			);
			$this->fields_form[0]['form']['input'][] = array(
				'type' => 'text',
				'label' => $this->l('How Many Tag You Want To Display'),
				'name' => 'thnxbt_tagcount',
			);
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
		foreach (Language::getLanguages(false) as $lang) {
			$helper->languages[] = array(
				'id_lang' => $lang['id_lang'],
				'iso_code' => $lang['iso_code'],
				'name' => $lang['name'],
				'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0),
			);
		}
		$helper->toolbar_btn = array(
			'save' => array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . 'token=' . Tools::getAdminTokenLite('AdminModules'),
			),
		);
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;
		$helper->toolbar_scroll = true;
		$helper->submit_action = 'save' . $this->name;
		$languages = Language::getLanguages(false);
           	foreach($languages as $lang){
           		$helper->fields_value['thnxbt_title'][$lang['id_lang']] = Configuration::get('thnxbt_title_'.$lang['id_lang']);
           	}
           	$helper->fields_value['thnxbt_tagcount'] = Configuration::get('thnxbt_tagcount');
		return $helper;
	}
	// All Functional Logic here.
	public function getContent() {
		$html = '';
		if (Tools::isSubmit('save' . $this->name)) {
			$languages = Language::getLanguages(false);
               foreach($languages as $lang){
            		Configuration::updateValue('thnxbt_title_'.$lang['id_lang'],Tools::getvalue('thnxbt_title_'.$lang['id_lang']));
               }
            	Configuration::updateValue('thnxbt_tagcount',Tools::getvalue('thnxbt_tagcount'));
		}
		$helper = $this->SettingForm();
		$html .= $helper->generateForm($this->fields_form);
		return $html;
	}
    public static function isEmptyFileContet($path = null){
    	if($path == null)
    		return false;
    	if(file_exists($path)){
    		$content = Tools::file_get_contents($path);
    		if(empty($content)){
    			return false;
    		}else{
    			return true;
    		}
    	}else{
    		return false;
    	}
    }
    public function Register_Css()
    {
        if(isset($this->css_files) && !empty($this->css_files)){
        	$theme_name = $this->context->shop->theme_name;
    		$page_name = $this->context->controller->php_self;
    		$root_path = _PS_ROOT_DIR_.'/';
        	foreach($this->css_files as $css_file):
        		if(isset($css_file['key']) && !empty($css_file['key']) && isset($css_file['src']) && !empty($css_file['src'])){
        			$media = (isset($css_file['media']) && !empty($css_file['media'])) ? $css_file['media'] : 'all';
        			$priority = (isset($css_file['priority']) && !empty($css_file['priority'])) ? $css_file['priority'] : 50;
        			$page = (isset($css_file['page']) && !empty($css_file['page'])) ? $css_file['page'] : array('all');
        			if(is_array($page)){
        				$pages = $page;
        			}else{
        				$pages = array($page);
        			}
        			if(in_array($page_name, $pages) || in_array('all', $pages)){
        				if(isset($css_file['load_theme']) && ($css_file['load_theme'] == true)){
        					$theme_file_src = 'themes/'.$theme_name.'/assets/css/'.$css_file['src'];
        					if(self::isEmptyFileContet($root_path.$theme_file_src)){
        						$this->context->controller->registerStylesheet($css_file['key'], $theme_file_src , ['media' => $media, 'priority' => $priority]);
        					}
        				}else{
        					$module_file_src = 'modules/'.$this->name.'/css/'.$css_file['src'];
        					if(self::isEmptyFileContet($root_path.$module_file_src)){
        						$this->context->controller->registerStylesheet($css_file['key'], $module_file_src , ['media' => $media, 'priority' => $priority]);
        					}
        				}
    				}
        		}
        	endforeach;
        }
        return true;
    }
    public function Register_Js()
    {
        if(isset($this->js_files) && !empty($this->js_files)){
	    	$theme_name = $this->context->shop->theme_name;
			$page_name = $this->context->controller->php_self;
			$root_path = _PS_ROOT_DIR_.'/';
        	foreach($this->js_files as $js_file):
        		if(isset($js_file['key']) && !empty($js_file['key']) && isset($js_file['src']) && !empty($js_file['src'])){
        			$position = (isset($js_file['position']) && !empty($js_file['position'])) ? $js_file['position'] : 'bottom';
        			$priority = (isset($js_file['priority']) && !empty($js_file['priority'])) ? $js_file['priority'] : 50;
        			$page = (isset($css_file['page']) && !empty($css_file['page'])) ? $css_file['page'] : array('all');
        			if(is_array($page)){
        				$pages = $page;
        			}else{
        				$pages = array($page);
        			}
        			if(in_array($page_name, $pages) || in_array('all', $pages)){
	        			if(isset($js_file['load_theme']) && ($js_file['load_theme'] == true)){
	        				$theme_file_src = 'themes/'.$theme_name.'/assets/js/'.$js_file['src'];
	        				if(self::isEmptyFileContet($root_path.$theme_file_src)){
	        					$this->context->controller->registerJavascript($js_file['key'], $theme_file_src , ['position' => $position, 'priority' => $priority]);
	        				}
	        			}else{
		        			$module_file_src = 'modules/'.$this->name.'/js/'.$js_file['src'];
	        				if(self::isEmptyFileContet($root_path.$module_file_src)){
		        				$this->context->controller->registerJavascript($js_file['key'], $module_file_src , ['position' => $position, 'priority' => $priority]);
	        				}
	        			}
        			}
        		}
        	endforeach;
        }
        return true;
    }
    public function hookdisplayheader($params) {
		$this->Register_Css();
		$this->Register_Js();
	}
	public function renderWidget($hookName = null, array $configuration = [])
	{
		if(Module::isInstalled('thnxblog') && Module::isEnabled('thnxblog')){
			$this->smarty->assign($this->getWidgetVariables($hookName,$configuration));
			return $this->fetch('module:'.$this->name.'/views/templates/front/'.$this->name.'.tpl');
		}else{
	    	return false;
	    }	
	}
	public function getWidgetVariables($hookName = null, array $configuration = [])
	{
	    if(Module::isInstalled('thnxblog') && Module::isEnabled('thnxblog')){
		    $id_lang = (int)$this->context->language->id;
		    $thnxbt_title = Configuration::get('thnxbt_title_'.$id_lang);
		    $thnxbt_tagcount = Configuration::get('thnxbt_tagcount');
		    $thnxblogtags = array();
		    $thnxblogtags = thnxpostsclass::GetBlogTags($thnxbt_tagcount,'tag');
		    return array(
	    		'thnxbt_title' => $thnxbt_title,
	    		'thnxbt_tagcount' => $thnxbt_tagcount,
	    		'hookName' => $hookName,
	    		'thnxblogtags' => $thnxblogtags,
	    	);
	    }else{
	    	return false;
	    }
	}
}