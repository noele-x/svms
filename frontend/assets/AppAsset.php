<?php

namespace frontend\assets;

use hail812\adminlte3\assets\AdminLteAsset;
use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
        AdminLteAsset::class,
        'yii\web\JqueryAsset',
    ];
}
