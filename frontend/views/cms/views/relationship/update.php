<?php

use yii\helpers\Html;

$this->title = 'Update Relationship: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Relationships', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="relationship-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>