<?php

namespace app\behaviors;

use yii\filters\ContentNegotiator;
use yii\web\Response;

/**
 * 内容协商
 */
class ContentNegotiatorBehavior extends ContentNegotiator
{
    public $formats = [
        'application/json' => Response::FORMAT_JSON,
        'application/xml' => Response::FORMAT_XML,
        'text/html' => Response::FORMAT_HTML,
    ];
}
