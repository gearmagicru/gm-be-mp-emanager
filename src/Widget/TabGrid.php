<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Marketplace\ExtensionManager\Widget;

use Gm\Panel\Helper\ExtGrid;
use Gm\Panel\Helper\HtmlGrid;
use Gm\Panel\Helper\HtmlNavigator as HtmlNav;

/**
 * Виджет для формирования интерфейса вкладки с сеткой данных.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Marketplace\ExtensionManager\Widget
 * @since 1.0
 */
class TabGrid extends \Gm\Panel\Widget\TabGrid
{
    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        // столбцы (Gm.view.grid.Grid.columns GmJS)
        $this->grid->columns = [
            ExtGrid::columnNumberer(),
            ExtGrid::columnAction(),
            [
                'xtype'     => 'templatecolumn',
                'text'      => '#Name',
                'dataIndex' => 'name',
                'tpl'       => HtmlGrid::tag(
                    'div',
                    [
                        HtmlGrid::tag(
                            'div', 
                            '', 
                            [
                                'class' => 'gm-mp-emanager-grid-cell-i__icon', 
                                'style' => 'background-image:url({icon})'
                            ]
                        ),
                        HtmlGrid::tag(
                            'div', 
                            '{name}', 
                            ['class' => 'gm-mp-emanager-grid-cell-i__name']
                        ),
                        HtmlGrid::tag(
                            'div', 
                            '{description}', 
                            ['class' => 'gm-mp-emanager-grid-cell-i__desc']
                        ),
                        HtmlGrid::tag(
                            'div', 
                            $this->creator->t('Version') . ': <span>{details}</span>', 
                            ['class' => 'gm-mp-emanager-grid-cell-i__dets']
                        ),
                    ],
                    ['class' => 'gm-mp-emanager-grid-cell-i gm-mp-emanager-grid-cell-i_offset {clsCellLock}']
                ),
                'cellTip'   => '{description}',
                'filter'    => ['type' => 'string'],
                'width'     => 450
            ],
            [
                'xtype'     => 'templatecolumn',
                'text'      => '#Module name',
                'dataIndex' => 'moduleName',
                'cellTip'   => '{moduleDesc}',
                'tpl'       => HtmlGrid::tag(
                    'div',
                    [
                        HtmlGrid::tag('div', '{moduleName}', ['class' => 'gm-mp-emanager-grid-cell-i__name']),
                        HtmlGrid::tag('div', '{moduleDesc}', ['class' => 'gm-mp-emanager-grid-cell-i__desc']),
                    ],
                    ['class' => 'gm-mp-emanager-grid-cell-i {clsCellLock}']
                ),
                'filter'    => ['type' => 'string'],
                'tdCls'     => 'gm-mp-emanager-grid-td_offset',
                'width'     => 200
            ],
            [
                'text'      => '#Extension id',
                'dataIndex' => 'extensionId',
                'cellTip'   => '{extensionId}',
                'filter'    => ['type' => 'string'],
                'hidden'    => true,
                'tdCls'     => 'gm-mp-emanager-td_offset',
                'width'     => 150
            ],
            [
                'text'      => '#Path',
                'dataIndex' => 'path',
                'cellTip'   => '{path}',
                'filter'    => ['type' => 'string'],
                'hidden'    => true,
                'tdCls'     => 'gm-mp-emanager-td_offset',
                'width'     => 150
            ],
            [
                'text'      => '#Route',
                'dataIndex' => 'route',
                'cellTip'   => '{route}',
                'filter'    => ['type' => 'string'],
                'hidden'    => true,
                'tdCls'     => 'gm-mp-emanager-td_offset',
                'width'     => 150
            ],
            [
                'text'      => '#Author',
                'dataIndex' => 'versionAuthor',
                'cellTip'   => '{versionAuthor}',
                'hidden'    => true,
                'tdCls'     => 'gm-mp-emanager-td_offset',
                'width'     => 150
            ],
            [
                'text'      => '#Version',
                'dataIndex' => 'version',
                'cellTip'   => '{version}',
                'sortable'  => true,
                'tdCls'     => 'gm-mp-emanager-td_offset',
                'width'     => 90
            ],
            [
                'xtype' => 'g-gridcolumn-control',
                'width' => 90,
                'tdCls' => 'gm-mp-emanager-td_offset',
                'items' => [
                    [
                        'iconCls'   => 'g-icon-svg g-icon_size_16 g-icon-m_link g-icon-m_color_default g-icon-m_is-hover',
                        'dataIndex' => 'extensionUrl',
                        'tooltip'   => '#Go to extension',
                        'handler'   => 'loadWidgetFromCell'
                    ],
                    [
                        'iconCls'   => 'g-icon-svg g-icon_size_16 g-icon-m_wrench g-icon-m_color_default g-icon-m_is-hover',
                        'dataIndex' => 'settingsUrl',
                        'tooltip'   => '#Extension settings',
                        'handler'   => 'loadWidgetFromCell'
                    ],
                    [
                        'iconCls'   => 'g-icon-svg g-icon_size_16 g-icon-m_info-circle g-icon-m_color_default g-icon-m_is-hover',
                        'dataIndex' => 'infoUrl',
                        'tooltip'   => '#Extension info',
                        'handler'   => 'loadWidgetFromCell'
                    ]
                ]
            ],
            [
                'text'      => ExtGrid::columnIcon('g-icon-m_unlock', 'svg'),
                'tooltip'   => '#Extension enabled',
                'xtype'     => 'g-gridcolumn-switch',
                'collectData' =>['name', 'extensionId'],
                'tdCls'     => 'gm-mp-emanager-td_offset',
                'dataIndex' => 'enabled'
            ],
            [
                'xtype'    => 'templatecolumn',
                'text'     => '#Status',
                'sortable' => true,
                'width'    => 120,
                'align'    => 'center',
                'tdCls'     => 'gm-mp-emanager-td_offset',
                'tpl'      => HtmlGrid::tplSwitch(
                    [
                        [
                            HtmlGrid::tag(
                                'span', 
                                $this->creator->t('not installed'), 
                                ['class' => 'gm-mp-emanager__status gm-mp-emanager__status_not-installed']
                            ),
                            '0'
                        ],
                        [
                            HtmlGrid::tag(
                                'span', 
                                $this->creator->t('installed'), 
                                ['class' => 'gm-mp-emanager__status gm-mp-emanager__status_installed']
                            ),
                            '1'
                        ]
                    ],
                    'status'
                ),
                'dataIndex' => 'status'
            ],
        ];

        // панель инструментов (Gm.view.grid.Grid.tbar GmJS)
        $this->grid->tbar = [
            'padding' => 1,
            'items'   => ExtGrid::buttonGroups([
                'edit' => [
                    'items' => [
                        // инструмент "Установить" (Install)
                        ExtGrid::button([
                            'xtype'         => 'gm-mp-emanager-button-install',
                            'iconCls'       => 'g-icon-svg gm-mp-emanager__icon-install',
                            'text'          => '#Install',
                            'tooltip'       => '#Extension install',
                            'msgMustSelect' => '#You need to select a extension'
                        ]),
                        // инструмент "Удалить" (Uninstall)
                        ExtGrid::button([
                            'xtype'         => 'gm-mp-emanager-button-uninstall',
                            'iconCls'       => 'g-icon-svg gm-mp-emanager__icon-uninstall',
                            'text'          => '#Uninstall',
                            'tooltip'       => '#Completely delete an installed extension',
                            'msgConfirm'    => '#Are you sure you want to completely delete the installed extension?',
                            'msgMustSelect' => '#You need to select a extension',
                            'handler'       => 'onSendData',
                            'handlerArgs'   => ['route' => $this->creator->route('/extension/uninstall')]
                        ]),
                        '-',
                        // инструмент "Удалить" (Delete)
                        ExtGrid::button([
                            'xtype'         => 'gm-mp-emanager-button-delete',
                            'iconCls'       => 'g-icon-svg gm-mp-emanager__icon-delete',
                            'text'          => '#Delete',
                            'tooltip'       => '#Delete an uninstalled extension from the repository',
                            'msgConfirm'    => '#Are you sure you want to delete the uninstalled extension from the repository?',
                            'msgMustSelect' => '#You need to select a extension'
                        ]),
                        // инструмент "Демонтаж" (Unmount)
                        ExtGrid::button([
                            'xtype'         => 'gm-mp-emanager-button-unmount',
                            'iconCls'       => 'g-icon-svg gm-mp-emanager__icon-unmount',
                            'text'          => '#Unmount',
                            'tooltip'       => '#Delete an installed extension without removing it from the repository',
                            'msgConfirm'    => '#Are you sure you want to remove the installed extension without removing it from the repository?',
                            'msgMustSelect' => '#You need to select a extension',
                            'handler'       => 'onSendData',
                            'handlerArgs'   => ['route' => $this->creator->route('/extension/unmount')]
                        ]),
                        '-',
                        // инструмент "Скачать" (Download)
                        ExtGrid::button([
                            'xtype'         => 'gm-mp-emanager-button-download',
                            'iconCls'       => 'g-icon-svg gm-mp-emanager__icon-download',
                            'text'          => '#Download',
                            'tooltip'       => '#Downloads module extension package file',
                            'msgMustSelect' => '#You need to select a widget'
                        ]),
                        // инструмент "Загрузить" (Upload)
                        ExtGrid::button([
                            'iconCls'     => 'g-icon-svg gm-mp-emanager__icon-upload',
                            'text'        => '#Upload',
                            'tooltip'     => '#Uploads module extension package file',
                            'handler'     => 'loadWidget',
                            'handlerArgs' => ['route' => $this->creator->route('/upload')]
                        ]),
                        '-',
                        'edit',
                        'refresh',
                        // инструмент "Обновить" (Update)
                        ExtGrid::button([
                            'text'        => '#Update',
                            'tooltip'     => '#Update configurations of installed extensions',
                            'iconCls'     => 'g-icon-svg gm-mp-emanager__icon-update-config',
                            'handler'     => 'onSendData',
                            'handlerArgs' => ['route' => $this->creator->route('/extension/update')]
                        ])
                    ]
                ],
                'columns',
                 // группа инструментов "Поиск"
                 'search' => [
                    'items' => [
                        'help',
                        'search',
                        // инструмент "Фильтр"
                        'filter' => [
                            'form' => [
                                'cls'      => 'g-popupform-filter',
                                'width'    => 400,
                                'height'   => 'auto',
                                'action'   => $this->creator->route('/grid/filter', true),
                                'defaults' => ['labelWidth' => 100],
                                'items'    => [
                                    [
                                        'xtype'      => 'radio',
                                        'boxLabel'   => '#All',
                                        'name'       => 'type',
                                        'inputValue' => 'all',
                                    ],
                                    [
                                        'xtype'      => 'radio',
                                        'boxLabel'   => '#Installed',
                                        'name'       => 'type',
                                        'inputValue' => 'installed',
                                        'checked'    => true
                                    ],
                                    [
                                        'xtype'      => 'radio',
                                        'boxLabel'   => '#None installed',
                                        'name'       => 'type',
                                        'inputValue' => 'nonInstalled',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ], [
                'route' => $this->creator->route()
            ])
        ];

        // контекстное меню записи (Gm.view.grid.Grid.popupMenu GmJS)
        $this->grid->popupMenu = [
            'items' => [
                [
                    'text'    => '#Edit record',
                    'iconCls' => 'g-icon-svg g-icon-m_edit g-icon-m_color_default',
                    'handlerArgs' => [
                        'route'   => $this->creator->route('/form/view/{id}'),
                        'pattern' => 'grid.popupMenu.activeRecord'
                    ],
                    'handler' => 'loadWidget'
                ],
                '-',
                [
                    'text'    => '#Extension settings',
                    'iconCls' => 'g-icon-m_wrench g-icon-m_color_default',
                    'handlerArgs' => [
                        'route'   => '{settingsUrl}',
                        'pattern' => 'grid.popupMenu.activeRecord'
                    ],
                    'handler' => 'loadWidget'
                ],
                [
                    'text'    => '#Extension info',
                    'iconCls' => 'g-icon-m_info-circle g-icon-m_color_default',
                    'handlerArgs' => [
                        'route'   => '{infoUrl}',
                        'pattern' => 'grid.popupMenu.activeRecord'
                    ],
                    'handler' => 'loadWidget'
                ]
            ]
        ];

        // 2-й клик по строке сетки
        $this->grid->rowDblClickConfig = [
            'allow' => true,
            'route' => $this->creator->route('/form/view/{id}')
        ];

        // количество строк в сетке
        $this->grid->store->pageSize = 100;
        // локальная фильтрация и сортировка
        $this->grid->store->remoteFilter = false;
        $this->grid->store->remoteSort = false;
        // сортировка сетке
        $this->grid->sorters = [['property' => 'name', 'direction' => 'ASC']];
        // поле аудита записи
        $this->grid->logField = 'name';
        // плагины сетки
        $this->grid->plugins = 'gridfilters';
        // класс CSS применяемый к элементу body сетки
        $this->grid->bodyCls = 'g-grid_background';
        // убрать плагины пагинации сетки
        $this->grid->pagingtoolbar['plugins'] = [];
        // выбирать только одну запись
        $this->grid->selModel = ['mode' => 'SINGLE'];

        // панель навигации (Gm.view.navigator.Info GmJS)
        $this->navigator->info['tpl'] = HtmlNav::tags([
            HtmlNav::image('{icon}', ['width' => '128px'], false),
            HtmlNav::header('{name}'),
            ['div', '{description}', ['style' => 'text-align:center']],
            ['fieldset',
                [
                    HtmlNav::fieldLabel($this->creator->t('Extension id'), '{extensionId}'),
                    HtmlNav::fieldLabel($this->creator->t('Record id'), '{id}'),
                    HtmlNav::fieldLabel($this->creator->t('Path'), '{path}'),
                    HtmlNav::fieldLabel($this->creator->t('Route'), '{route}'),
                    HtmlNav::fieldLabel($this->creator->t('Status'), 
                    HtmlGrid::tplSwitch(
                        [
                            [$this->creator->t('not installed'), '0'],
                            [$this->creator->t('installed'), '1']
                        ],
                        'status'
                    )),
                    HtmlNav::tplIf('lock==0',
                        HtmlNav::fieldLabel($this->creator->t('Enabled'), HtmlNav::tplChecked('enabled==1')),
                        ''
                    )
                ]
            ],
            ['fieldset',
                [
                    HtmlNav::legend($this->creator->t('Version')),
                    HtmlNav::fieldLabel($this->creator->t('Version'), '{version}'),
                    HtmlNav::fieldLabel($this->creator->t('Date'), '{versionDate}'),
                    HtmlNav::fieldLabel($this->creator->t('Author'), '{versionAuthor}'),
                ]
            ],
            ['fieldset',
                [
                    HtmlNav::tplIf('status',
                        HtmlNav::widgetButton(
                            $this->creator->t('Edit record'),
                            ['route' => $this->creator->route('/form/view/{id}'), 'long' => true]
                        ),
                        ''
                    ),
                    HtmlNav::tplIf('settingsUrl',
                        HtmlNav::widgetButton(
                            $this->creator->t('Extension settings'),
                            ['route' => '{settingsUrl}', 'long' => true]
                        ),
                        ''
                    ),
                    HtmlNav::tplIf('infoUrl',
                        HtmlNav::widgetButton(
                            $this->creator->t('Extension info'),
                            ['route' => '{infoUrl}', 'long' => true]
                        ),
                        ''
                    )
                ]
            ]
        ]);

        $this
            ->setNamespaceJS('Gm.be.mp.emanager')
            ->addRequire('Gm.view.grid.column.Switch')
            ->addRequire('Gm.be.mp.emanager.Button')
            ->addCss('/grid.css');
    }
}
