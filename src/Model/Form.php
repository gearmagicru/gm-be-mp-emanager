<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Marketplace\ExtensionManager\Model;

use Gm;
use Gm\Panel\Data\Model\FormModel;

/**
 * Модель данных изменения расширения.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\ExtensionManager\Model
 * @since 1.0
 */
class Form extends FormModel
{
    /**
     * {@inheritdoc}
     */
    public array $localizerParams = [
        'tableName'  => '{{extension_locale}}',
        'foreignKey' => 'extension_id',
        'modelName'  => '\Gm\ExtensionManager\Model\ExtensionLocale',
    ];


    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'useAudit'   => true,
            'tableName'  => '{{extension}}',
            'primaryKey' => 'id',
            'fields'     => [
                ['id'],
                ['name'],
                ['description'],
                [
                    'extension_id',
                    'alias' => 'extensionId'
                ],
                [
                    'enabled', 
                    'title' => 'Enabled'
                ],
                /**
                 * поля добавленные динамически:
                 * - title, имя расширения (для заголовка окна)
                 */
            ],
            // правила форматирования полей
            'formatterRules' => [
                [['enabled'], 'logic']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                // если всё успешно
                if ($result) {
                    /** @var \Gm\ExtensionManager\ExtensionRegistry $installed */
                    $installed = Gm::$app->extensions->getRegistry();
                    $extension = $installed->get($this->extensionId);
                    if ($extension) {
                        $lock = (bool) ($extension['lock'] ?? false);
                        // если модуль не системный
                        if (!$lock) {
                            // обвновление конфигурации установленных модулей
                            $installed->set($this->extensionId, [
                                'enabled'     => (bool) $this->enabled,
                                'name'        => $this->name,
                                'description' => $this->description
                            ], true);
                        }
                    }
                }
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                /** @var \Gm\Panel\Controller\FormController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            })
            ->on(self::EVENT_AFTER_DELETE, function ($result, $message) {
                // обвновление конфигурации установленных расширений
                Gm::$app->extensions->update();
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                /** @var \Gm\Panel\Controller\FormController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            });
    }

    /**
     * {@inheritdoc}
     */
    public function processing(): void
    {
        parent::processing();

        // для формирования загаловка по атрибутам
        $locale = $this->getLocalizer()->getModel();
        if ($locale) {
            $this->title = $locale->name ?: '';
        }
    }

    /**
     * {@inheritDoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            if (!Gm::$app->extensions->getRegistry()->has($this->extensionId)) {
                $this->setError($this->module->t('There is no extension with the specified id "{0}"', [$this->extensionId]));
                return false;
            }
        }
        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function getActionTitle():string
    {
        return isset($this->title) ? $this->title : parent::getActionTitle();
    }
}
