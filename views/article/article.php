<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

?>
<div class="site-article">
    <div class="body-content">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <img src="<?= \yii\helpers\Url::base() ?>/assets/images/source-logos/<?= $model['logo']->imagename ?>" style="width: 40px" alt="">
                        <?= date_format(date_create($model['article']->created),"D, jS F y") ?><br>
                        Topic: <?= $model['article']->topic->topic_title; ?><br>
                        Keywords: <?php foreach ($model['keywords'] as $keyword): ?>
                        <span><?= $keyword['keyword']; ?></span>
                        <?php endforeach;?>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title"><?= $model['article']->title ?></h1>
                        <p class="card-text"><?= $model['article']->summary ?></p>
                        <a target="_blank" href="<?= $model['article']->external_link ?>" class="btn btn-primary">Read the full article at<br><?= parse_url($model['article']->external_link)['host'] ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

