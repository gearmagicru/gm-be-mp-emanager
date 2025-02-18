<?php
/**
 * Расширение модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Marketplace\ExtensionManager\Controller;

use Gm;
use Gm\Panel\Http\Response;
use Gm\Filesystem\Filesystem;
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Controller\BaseController;

/**
 * Контроллер удаления и демонтажа расширения модуля.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\ExtensionManager\Controller
 * @since 1.0
 */
class Extension extends BaseController
{
    /**
     * @var BaseModule|\Gm\Backend\Marketplace\ExtensionManager\Extension
     */
    public BaseModule $module;

    /**
     * Действие "unmount" выполняет удаление установленного расширения модуля без 
     * удаления его из репозитория.
     * 
     * @return Response
     */
    public function unmountAction(): Response
    {
        /** @var \Gm\ExtensionManager\ExtensionManager */
        $extensions = Gm::$app->extensions;
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var \Gm\Http\Request $request */
        $request = Gm::$app->request;

        // идентификатор модуля в базе данных
        $extensionId = $request->getPost('id', null, 'int');
        if (empty($extensionId)) {
            $response
                ->meta->error(Gm::t('app', 'Parameter "{0}" not specified', [$extensionId]));
            return $response;
        }

        /** @var null|array Конфигурация установленного модуля */
        $extensionConfig = $extensions->getRegistry()->getInfo($extensionId, true);
        if ($extensionConfig === null) {
            $response
                ->meta->error($this->module->t('Extension with specified id "{0}" not found', [$extensionId]));
            return $response;
        }

        // локализация модуля
        $localization = $extensions->selectName($extensionConfig['rowId']);
        if ($localization) {
            $name = $localization['name'] ?? SYMBOL_NONAME;
        } else {
            $name = $extensionConfig['name'] ?? SYMBOL_NONAME;
        }

        // если расширение не имеет установщика "Installer\Installer.php"
        if (!$extensions->installerExists($extensionConfig['path'])) {
            $response
                ->meta->error(
                    $this->module->t('The extension installer at the specified path "{0}" does not exist', [$extensionConfig['path']])
                );
            return $response;
        }

        // каждое расширение обязано иметь установщик, управление установщиком передаётся текущему модулю
        /** @var \Gm\ExtensionManager\ExtensionInstaller $installer Установщик расширения */
        $installer = $extensions->getInstaller([
            'response'    => $response,
            'module'      => $this->module, 
            'namespace'   => $extensionConfig['namespace'],
            'path'        => $extensionConfig['path'], 
            'extensionId' => $extensionId
        ]);

        // если не получилось создать установщик
        if ($installer === null) {
            $response
                ->meta->error($this->t('Unable to create extension installer'));
            return $response;
        }

        // демонтируем расширение
        if ($installer->unmount()) {
            $response
                ->meta
                    ->cmdPopupMsg(
                        $this->module->t('Unmounting of extension "{0}" completed successfully', [$name]), 
                        $this->t('Unmounting'), 
                        'accept'
                    )
                    ->cmdReloadGrid($this->module->viewId('grid'));
        } else {
            $response
                ->meta->error($installer->getError());
        }
        return $response;
    }

    /**
     * Действие "uninstall" полностью удаляет установленное расширение модуля.
     * 
     * @return Response
     */
    public function uninstallAction()
    {
        /** @var \Gm\ExtensionManager\ExtensionManager */
        $extensions = Gm::$app->extensions;
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var \Gm\Http\Request $request */
        $request = Gm::$app->request;

        // идентификатор расширения модуля в базе данных
        $extensionId = $request->getPost('id', null, 'int');
        if (empty($extensionId)) {
            $response
                ->meta->error(Gm::t('app', 'Parameter "{0}" not specified', ['id']));
            return $response;
        }

        /** @var null|array Конфигурация установленного расширения */
        $extensionConfig = $extensions->getRegistry()->getInfo($extensionId, true);
        if ($extensionConfig === null) {
            $response
                ->meta->error($this->module->t('Extension with specified id "{0}" not found', [$extensionId]));
            return $response;
        }

        // локализация модуля
        $localization = $extensions->selectName($extensionConfig['rowId']);
        if ($localization) {
            $name = $localization['name'] ?? SYMBOL_NONAME;
        } else {
            $name = $extensionConfig['name'] ?? SYMBOL_NONAME;
        }

        // если расширение не имеет установщика "Installer\Installer.php"
        if (!$extensions->installerExists($extensionConfig['path'])) {
            $response
                ->meta->error(
                    $this->module->t('The extension installer at the specified path "{0}" does not exist', [$extensionConfig['path']])
                );
            return $response;
        }

        // каждое расширение обязано иметь установщик, управление установщиком передаётся текущему модулю
        /** @var \Gm\ExtensionManager\ExtensionInstaller $installer Установщик расширения */
        $installer = $extensions->getInstaller([
            'response'    => $response,
            'module'      => $this->module, 
            'namespace'   => $extensionConfig['namespace'],
            'path'        => $extensionConfig['path'], 
            'extensionId' => $extensionId
        ]);

        // если не получилось создать установщик
        if ($installer === null) {
            $response
                ->meta->error($this->t('Unable to create extension installer'));
            return $response;
        }

        // удаление расширения
        if ($installer->uninstall()) {
            $response
                ->meta
                    ->cmdPopupMsg(
                        $this->module->t('Uninstalling of extension "{0}" completed successfully', [$name]), 
                        $this->t('Uninstalling'), 
                        'accept'
                    )
                    ->cmdReloadGrid($this->module->viewId('grid'));
        } else {
            $response
                ->meta->error($installer->getError());
        }
        return $response;
    }

    /**
     * Действие "update" обновляет конфигурацию установленных расширений модулей.
     * 
     * @return Response
     */
    public function updateAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        // обновляет конфигурацию установленных расширений модулей
        Gm::$app->extensions->update();
        $response
            ->meta->success(
                $this->t('Extensions configuration files are updated'), 
                $this->t('Updating extensions'), 
                'custom', 
                $this->module->getAssetsUrl() . '/images/icon-update-config.svg'
            );
        return $response;
    }

    /**
     * Действие "delete" удаляет не установленные расширения модуля из репозитория.
     * 
     * @return Response
     */
    public function deleteAction(): Response
    {
        /** @var \Gm\ExtensionManager\ExtensionManager */
        $extensions = Gm::$app->extensions;
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var null|string Идентификатор установки расширения */
        $installId = Gm::$app->request->post('installId');

        /** @var string|array Расшифровка идентификатора установки расширения */
        $decrypt = $extensions->decryptInstallId($installId);
        if (is_string($decrypt)) {
            $response
                ->meta->error($decrypt);
            return $response;
        }

        /** @var null|array Параметры конфигурации установки расширения */
        $installConfig = $extensions->getConfigInstall($decrypt['path']);
        if (empty($installConfig)) {
            $response
                ->meta->error(
                    $this->module->t('Extension installation configuration file is missing')
                );
            return $response;
        }

        // если расширение установлено
        if ($extensions->getRegistry()->has($installConfig['id'])) {
            $response
                ->meta->error(
                    $this->module->t('It is not possible to remove the extension from the repository because it\'s installed')
                );
            return $response;
        }

        // попытка удаления всех файлов расширения
        if (Filesystem::deleteDirectory(Gm::$app->modulePath . $decrypt['path'])) {
            $response
                ->meta
                    ->cmdPopupMsg(
                        $this->t('Deleting of extension completed successfully'), 
                        $this->t('Deleting'), 
                        'accept'
                    )
                    ->cmdReloadGrid($this->module->viewId('grid'));
        } else {
            $response
                ->meta->error(
                    Gm::t('app', 'Could not perform directory deletion "{0}"', [Gm::$app->modulePath . $decrypt['path']])
                );
        }
        return $response;
    }
}
