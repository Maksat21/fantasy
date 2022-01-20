<?php
use yii\helpers\Url;
use common\widgets\Sidebar;
?>
<aside class="main-sidebar">
    <a href="<?= Url::to(['/'])?>" class="mini-logo visible-xs">
        <span class="logo-lg">Admin</span>
    </a>
    <section class="sidebar">
        <?= Sidebar::widget(
            [
                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
                'linkTemplate' => '<a href="{url}">{icon} {label}</a>',
                'activeCssClass' => 'active',
                'items' => [
                    [
                        'label' => 'Меню',
                        'options' => [
                            'class' => 'header'
                        ],
                    ],
                    [
                        'label' => 'Главная',
                        'url' => ['/site/index'],
                        'icon' => 'fa-link',
                        'void' => true,
                    ],
                    [
                        'label' => 'Терминалы',
                        'url' => ['/terminal/index'],
                        'icon' => 'fa-link',
                        'void' => true,
                    ],
                    [
                        'label' => 'Карты',
                        'url' => ['/card/index'],
                        'icon' => 'fa-link',
                        'void' => true,
                    ],
                    [
                        'label' => 'Пополнения',
                        'url' => ['/replenishment/index'],
                        'icon' => 'fa-link',
                        'void' => true,
                    ],
                    [
                        'label' => 'Транзакции',
                        'url' => ['/transaction/index'],
                        'icon' => 'fa-link',
                        'void' => true,
                    ],
                    [
                        'label' => 'Аттракционы',
                        'url' => ['/attraction/index'],
                        'icon' => 'fa-link',
                        'void' => true,
                    ],
                    /*[
                        'label' => Yii::t('app', 'Matching'),
                        'icon' => 'fa-compress aria-hidden="true"',
                        'url' => '',
                        'items' => [
                            [
                                'label' => Yii::t('app', 'Medicament'),
                                'url' => ['/matching/index'],
                                'icon' => 'none',
                            ],
                        ]
                    ],*/
                ]
            ]
        );
        ?>
    </section>
    <div class="aside-footer-menu">
        <ul class="aside-footer-menu__list">
            <li class="aside-footer-menu__item">
                <a class="aside-footer-menu__link" href="<?= Url::to(['/site/about'])?>">
                    <i class="fa fa-copyright"></i>
                </a>
            </li>
            <li class="aside-footer-menu__item">
                <a class="aside-footer-menu__link" href="<?= Url::to(['/site/logout'])?>" data-method="post">
                    <i class="fa fa-power-off"></i>
                </a>
            </li>
        </ul>
    </div>
</aside>