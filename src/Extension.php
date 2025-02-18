<?php
/**
 * Расширение модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Marketplace\ExtensionManager;

/**
 * Расширение "Менеджер расширений модулей".
 * 
 * Расширение принадлежит модулю "Маркетплейс".
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\ExtensionManager
 * @since 1.0
 */
class Extension extends \Gm\Panel\Extension\Extension
{
    /**
     * {@inheritdoc}
     */
    public string $id = 'gm.be.mp.emanager';

    /**
     * {@inheritdoc}
     */
    public string $defaultController = 'grid';

    /**
     * {@inheritdoc}
     */
    public function controllerMap(): array
    {
        return [
            'hsettings' => 'ShortcodeSettings'
        ];
    }
}