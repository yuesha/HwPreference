<?php

namespace TypechoPlugin\HelloWorld;

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Text;
use Typecho\Widget\Helper\Form\Element\Radio;
use Typecho\Widget\Helper\Form\Element\Select;
use Widget\Options;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * 管理后台右上角的欢迎语及保存按键码设置
 *
 * @package HelloWorld
 * @author yuesha
 * @version 1.0.1
 * @link https://hw13.cn
 */
class Plugin implements PluginInterface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     */
    public static function activate()
    {
        \Typecho\Plugin::factory('admin/menu.php')->navBar = __CLASS__ . '::render';
        \Typecho\Plugin::factory('admin/write-js.php')->write = __CLASS__ . '::contentWriteJs';
        return _t('插件已激活，请前往设置');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     */
    public static function deactivate()
    {
    }

    /**
     * 获取插件配置面板
     *
     * @param Form $form 配置面板
     */
    public static function config(Form $form)
    {
        $keyCodes = [
            65 => 'Ctrl + A',
            66 => 'Ctrl + B',
            67 => 'Ctrl + C',
            68 => 'Ctrl + D',
            69 => 'Ctrl + E',
            70 => 'Ctrl + F',
            71 => 'Ctrl + G',
            72 => 'Ctrl + H',
            73 => 'Ctrl + I',
            74 => 'Ctrl + J',
            75 => 'Ctrl + K',
            76 => 'Ctrl + L',
            77 => 'Ctrl + M',
            78 => 'Ctrl + N',
            79 => 'Ctrl + O',
            80 => 'Ctrl + P',
            81 => 'Ctrl + Q',
            82 => 'Ctrl + R',
            83 => 'Ctrl + S',
            84 => 'Ctrl + T',
            85 => 'Ctrl + U',
            86 => 'Ctrl + V',
            87 => 'Ctrl + W',
            88 => 'Ctrl + X',
            89 => 'Ctrl + Y',
            90 => 'Ctrl + Z',
            48 => 'Ctrl + 0(顶部)',
            49 => 'Ctrl + 1(顶部)',
            50 => 'Ctrl + 2(顶部)',
            51 => 'Ctrl + 3(顶部)',
            52 => 'Ctrl + 4(顶部)',
            53 => 'Ctrl + 5(顶部)',
            54 => 'Ctrl + 6(顶部)',
            55 => 'Ctrl + 7(顶部)',
            56 => 'Ctrl + 8(顶部)',
            57 => 'Ctrl + 9(顶部)'
        ];
        /** 分类名称 */
        $name = new Text('word', null, '欢迎您，尊敬的管理员', _t('管理后台右上角的欢迎语'));
        /** 保存的快捷键码 */
        $saveKeyCode = new Select('saveKeyCode', $keyCodes, 83, _t('新增或编辑文章时保存的快捷键码') );
        /** 文章编辑-是否自动点击自定义字段菜单 */
        $clickField = new Radio('clickField', [0 => '关闭', 1 => '开启'], 0, _t('新增或编辑文章时是否自动点击自定义字段菜单'));
        /** 文章编辑-是否自动开启全屏 */
        $openFullScreen = new Radio('openFullScreen', [0 => '关闭', 1 => '开启'], 1, _t('新增或编辑文章时是否自动开启全屏'));
        /** 文章编辑-是否自动开启大纲目录 */
        $openCatalogue = new Radio('openCatalogue', [0 => '关闭', 1 => '开启'], 1, _t('新增或编辑文章时是否自动开启大纲目录'));

        $form->addInput($name);
        $form->addInput($saveKeyCode);
        $form->addInput($clickField);
        $form->addInput($openFullScreen);
        $form->addInput($openCatalogue);
    }

    /**
     * 个人用户的配置面板
     *
     * @param Form $form
     */
    public static function personalConfig(Form $form)
    {
    }

    /**
     * 插件实现方法
     *
     * @access public
     * @return void
     */
    public static function render()
    {
        $helloWorld = Options::alloc()->plugin('HelloWorld')->word;
        $helloWorld = htmlspecialchars($helloWorld);

        echo "<span class=\"message success\">{$helloWorld}</span>";
    }

    /**
     * 插件实现文章页面内js附加代码
     *
     * @access public
     * @return void
     */
    public static function contentWriteJs()
    {
        $saveKeyCode = Options::alloc()->plugin('HelloWorld')->saveKeyCode;
        $clickField = Options::alloc()->plugin('HelloWorld')->clickField;
        $openFullScreen = Options::alloc()->plugin('HelloWorld')->openFullScreen;
        $openCatalogue = Options::alloc()->plugin('HelloWorld')->openCatalogue;

        $jsCode = "
            // Ctrl+S时调起保存
            document.onkeydown = function () {
                // 过滤非组合键监听
                if (window.event.ctrlKey && window.event.keyCode == {$saveKeyCode}) {
                    event.keyCode = 0;
                    event.returnValue = false;

                    // 模拟点击
                    btnSave.click();
                }
            }
            setTimeout(() => {
        ";
        if ($clickField) $jsCode .= "
                // 默认关闭自定义字段
                $('#custom-field-expand a').click();
        ";
        if ($openFullScreen) $jsCode .= "
                // 开启全屏
                $('[data-type=fullscreen]')[0].click();
        ";
        if ($openCatalogue) $jsCode .= "
                // 开启大纲
                $('[data-type=outline]')[0].click();
        ";
        $jsCode .= "}, 1000);";

        echo '<script type="text/javascript">';
        echo '$(document).ready(function() {';
        echo 'let btnSave = $("#btn-save");';
        echo $jsCode;
        echo '});';
        echo '</script>';
    }
}
