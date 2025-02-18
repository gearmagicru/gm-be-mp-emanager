<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Marketplace\ExtensionManager\Controller;

use Gm;
use Gm\Panel\Http\Response;
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Controller\FormController;

/**
 * Контроллер настройки шорткода расширения модуля.
 * 
 * Действия контроллера:
 * - view, вывод интерфейса настроек шорткода расширения модуля.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\ExtensionManager\Controller
 * @since 1.0
 */
class ShortcodeSettings extends FormController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Gm\Backend\Marketplace\ExtensionManager\Extension
     */
    public BaseModule $module;

    /**
     * {@inheritdoc}
     */
    public function translateAction(mixed $params, string $default = null): ?string
    {
        switch ($this->actionName) {
            // вывод интерфейса
            case 'view':
                return Gm::t(BACKEND, "{{$this->actionName} settings action}");

            default:
                return parent::translateAction(
                    $params,
                    $default ?: Gm::t(BACKEND, "{{$this->actionName} settings action}")
                );
        }
    }

    /**
     * Возвращает идентификатор выбранного расширения модуля.
     *
     * @return int
     */
    public function getIdentifier(): int
    {
        return (int) Gm::$app->router->get('id');
    }

    /**
     * Действие "view" выводит интерфейс настроек шорткода расширения модуля.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var null|int $id Идентификатор расширения модуля */
        $id = $this->getIdentifier();
        if (empty($id)) {
            return $this->errorResponse(
                GM_MODE_DEV ?
                    Gm::t('app', 'Parameter "{0}" not specified', ['id']) :
                    $this->module->t('Unable to show extension shortcode settings')
            );
        }

        /** @var null|string $tagName Имя тега */
        $tagName = Gm::$app->request->getQuery('name');
        if (empty($tagName)) {
            return $this->errorResponse(
                GM_MODE_DEV ?
                    Gm::t('app', 'Parameter "{0}" not specified', ['name']) :
                    $this->module->t('Unable to show extension shortcode settings')
            );
        }

        /** @var null|array $extParams Параметры расширения модуля */
        $extParams = Gm::$app->extensions->getRegistry()->getAt($id);
        if ($extParams === null) {
            return $this->errorResponse(
                GM_MODE_DEV ?
                    Gm::t('app', 'There is no widget with the specified id "{0}"', ['$id']) :
                    $this->module->t('Unable to show extension shortcode settings')
            );
        }

        /** @var null|array $install Параметры установки расширения модуля */
        $install = Gm::$app->extensions->getRegistry()->getConfigInstall($id);
        // если параметры установки не найдены
        if ($install === null) {
            return $this->errorResponse(
                GM_MODE_DEV ?
                    Gm::t('app', 'There is no widget with the specified id "{0}"', ['$id']) :
                    $this->module->t('Unable to show extension shortcode settings')
            );
        }

        /** @var array|null $shortcode Параметры указанного шорткода расширения модуля */
        $shortcode = $install['editor']['shortcodes'][$tagName] ?? null;
        if (empty($shortcode)) {
            return $this->errorResponse(
                GM_MODE_DEV ?
                    Gm::t('app', 'Parameter passed incorrectly "{0}"', ['shortcodes[' . $tagName . ']']) :
                    $this->module->t('Unable to show extension shortcode settings')
            );
        }

        // если нет настроек шорткода
        if (empty($shortcode['settings'])) {
            return $this->errorResponse(
                GM_MODE_DEV ?
                    Gm::t('app', 'The value for parameter "{0}" is missing', ['shortcodes[settings]']) :
                    $this->module->t('Unable to show extension shortcode settings')
            );
        }

        // для доступа к пространству имён объекта
        Gm::$loader->addPsr4($extParams['namespace']  . NS, Gm::$app->modulePath . $extParams['path'] . DS . 'src');

        $settingsClass = $extParams['namespace'] . NS . $shortcode['settings'];
        if (!class_exists($settingsClass)) {
            return $this->errorResponse(
                $this->module->t('Unable to create widget object "{0}"', [$settingsClass])
            );
        }

        // добавляем шаблон локализации расширения модуля (которому принадлежит шорткод)
        $category = Gm::$app->translator->getCategory($this->module->id);
        // ключ шаблона при подключении не имеет значение
        $category->patterns['shortcodeSettings'] = [
            'basePath' => Gm::$app->modulePath . $extParams['path'] . DS . 'lang',
            'pattern'  => 'text-%s.php',
        ];
        $this->module->addTranslatePattern('shortcodeSettings');

        /** @var object|Gm\Panel\Widget\ShortcodeSettingsWindow $widget Виджет настроек шорткода */
        $widget = Gm::createObject($settingsClass);
        if ($widget instanceof Gm\Panel\Widget\ShortcodeSettingsWindow) {
            $widget->form->controller = 'gm-mp-emanager-shortcodesettings';
            $widget
                ->setNamespaceJS('Gm.be.mp.emanager')
                ->addRequire('Gm.be.mp.emanager.ShortcodeSettingsController');    
        }

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }
}
