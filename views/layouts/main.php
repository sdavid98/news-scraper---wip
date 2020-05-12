<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="stylesheet" href="<?= \yii\helpers\Url::base() ?>/css/style.css">
    <script src="<?= \yii\helpers\Url::base() ?>/js/jquery.js"></script>
    <script src="<?= \yii\helpers\Url::base() ?>/js/popper.js"></script>
    <script src="<?= \yii\helpers\Url::base() ?>/js/bootstrap.js"></script>
    <?php if (isset($this->context->topicName)) echo("<script>window.topicName ='".$this->context->topicName."';</script>"); ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <div class="container mb-5">
        <div class="row">
            <div class="col">
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                    <a class="navbar-brand" href="#">Navbar</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item active">
                                <a class="nav-link" href="#">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Privacy policy</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Cookie policy</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Contact</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <div class="container mb-4">
        <div class="row">
            <div class="col">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link<?php if (isset($this->context->topicName) && $this->context->topicName === 'main') echo(' active'); ?>" href="<?= \yii\helpers\Url::base() ?>/main">
                            main</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::base() ?>/business">business</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::base() ?>/sports">sports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::base() ?>/health">health</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php if (isset($this->context->topicName) && $this->context->topicName === 'computers') echo(' active'); ?>" href="<?= \yii\helpers\Url::base() ?>/computers">
                            computers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::base() ?>/home">home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::base() ?>/science">science</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::base() ?>/society">society</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::base() ?>/games">games</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \yii\helpers\Url::base() ?>/others">others</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col">
                <form id="keySearch" method="GET" onsubmit="return validateForm()" action="<?= \yii\helpers\Url::base() . $this->context->topicName ?>">
                    <div class="form-group">
                        <label for="keyword">Article Link</label>
                        <input type="text" name="keyword" class="form-control" id="keyword" autocomplete="off">
                        <ul style="overflow: hidden;display: none; position: absolute;z-index: 99;width: 300px;" class="list-group" id="selectedKeyword">
                        </ul>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>

                <script>
                    function delay(callback, ms) {
                        var timer = 0;
                        return function() {
                            var context = this, args = arguments;
                            clearTimeout(timer);
                            timer = setTimeout(function () {
                                callback.apply(context, args);
                            }, ms || 0);
                        };
                    }
                    $('#keyword').keyup(delay(function (e) {
                        if (this.value.trim() !== '') {
                            getKeywords(this.value);
                        }
                        else {
                            $('#selectedKeyword').css('display', 'none');
                        }
                    }, 350));

                    function getKeywords(startString) {
                        var data = new FormData();
                        data.append('keyword', startString);
                        $.ajax({
                            url: "<?= \yii\helpers\Url::base() ?>/keyword-search",
                            type: 'POST',
                            data: data,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                if (data.length) {
                                    var select = $('#selectedKeyword');
                                    select.css('display', 'block');
                                    var options = '';
                                    for (var i = 0; i < data.length; i++) {
                                        options += ('<li class="list-group-item">' + data[i].keyword + '</li>');
                                    }
                                    select.html(options);
                                    select.attr('size', data.length);

                                }
                            }
                        });
                    }

                    function validateForm() {
                        if ($('#keyword').val().trim() === '') {
                            return false;
                        }
                    }


                    function writeOutArticles(articles) {
                        var content = '';
                        for (var i = 0; i < articles.length; i++) {
                            content += '<div class="col-12"><p>' + articles[i].created + '<br>' + articles[i].topic_title;
                            var keywords = articles[i].keywords.split(',');
                            for (var j = 0; j < keywords.length; j++) {
                                content += '<span>' + keywords[j] + '</span>';
                            }
                            content += '</p>';
                            content += '<img style="vertical-align: top; margin-top: 4px;" class="md-icon" width="35" src="/assets/images/source-logos/' + articles[i].imagename + '">';
                            content += `<a style="width: 80%; display: inline-block" href="/articles/${articles[i].filename}">
                                            <div>
                                                <h2 style="margin-top: 0">${articles[i].title}</h2>
                                                <p>${articles[i].first_row}</p>
                                            </div>
                                        </a>
                                    </div>`;
                        }
                        $('#articleHolder').html(content);
                    }
                </script>
            </div>
        </div>
    </div>
    <div class="container">
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
