<?php
/**
 * Этот файл является частью пакета GM Panel.
 * 
 * @link https://gearmagic.ru/framework/
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Marketplace\ExtensionManager\Model;

use Gm;
use Gm\Db\Sql;
use Gm\Panel\Data\Model\Combo\ComboModel;

/**
 * Модель данных элементов выпадающего списка установленных расширений модулей 
 * (реализуемых представленим с использованием компонента Gm.form.Combo ExtJS).
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\ExtensionManager\Model
 * @since 1.0
 */
class ExtensionCombo extends ComboModel
{
    /**
     * {@inheritdoc}
     */
    protected array $allowedKeys = [
        'id'          => 'id',
        'extensionId' => 'extension_id'
    ];

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{extension_locale}}',
            'primaryKey' => 'extension_id',
            'searchBy'   => 'name',
            'order'      => ['name' => 'ASC'],
            'fields'     => [
                ['name', 'direct' => 'extl.name'],
                ['description']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function selectAll(string $tableName = null): array
    {
        /** @var \Gm\Db\Sql\Select $select */
        $select = $this->builder()->select();
        $select
            ->columns(['id', 'extension_id', 'name', 'description'])
            ->quantifier(new Sql\Expression('SQL_CALC_FOUND_ROWS'))
            ->from(['ext' => '{{extension}}'])
            ->join(
                ['extl' => '{{extension_locale}}'],
                'extl.extension_id = ext.id AND extl.language_id = ' . (int) Gm::$app->language->code,
                ['loName' => 'name', 'loDescription' => 'description'],
                $select::JOIN_LEFT
            );

        /** @var \Gm\Db\Adapter\Driver\AbstractCommand $command */
        $command = $this->buildQuery($select);
        $rows = $this->fetchRows($command);
        $rows = $this->afterFetchRows($rows);
        return $this->afterSelect($rows, $command);
    }

    /**
     * {@inheritdoc}
     */
    public function afterFetchRow(array $row, array &$rows): void
    {
        if ($row['loName']) {
            $row['name'] = $row['loName'];
        }
        if ($row['loDescription']) {
            $row['description'] = $row['loDescription'];
        }
        $rows[] = [$row[$this->key], $row['name'], $row['description']];
    }
}
