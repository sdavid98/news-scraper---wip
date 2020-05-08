<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

?>
<div class="site-index">
    <div class="body-content">

        <div class="row" id="articleHolder">
            <?php foreach ($model as $m): ?>
                <div class="col-12">
                     <p>
                         <?= date_format(date_create($m['created']),"D, jS F y"); ?><br>
                         <?php if (isset($displayConfig) && isset($displayConfig['showTopic']) && $displayConfig['showTopic']) echo('<span>'.$m['topic_title'].'</span>'); ?>

                         <?php
                         foreach (explode(',', $m['keywords']) as $keyword) {
                                echo('<span>' . $keyword . '</span>');
                            }
                         ?>
                     </p>
                    <img style="vertical-align: top; margin-top: 4px;" class="md-icon" width="35" src="<?= \yii\helpers\Url::base() ?>/assets/images/source-logos/<?= $m['imagename'] ?>">
                    <a style="width: 80%; display: inline-block" href="<?= \yii\helpers\Url::base() ?>/articles/<?= $m['filename'] ?>">
                        <div>
                            <h2 style="margin-top: 0"><?= $m['title'] ?></h2>
                            <p><?= $m['first_row'] ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach;?>
        </div>

    </div>
</div>
