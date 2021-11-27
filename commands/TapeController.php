<?php

declare(strict_types=1);

namespace app\commands;

use app\models\Music;
use app\models\Track;
use DateTime;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;

/**
 * Creates your some tape.
 * The slug generated here is not complete and may need to be edited.
 * @noinspection PhpUnused
 */
class TapeController extends Controller
{
    public $defaultAction = 'favorites';

    public function init(): void
    {
        parent::init();

        Yii::setAlias('@tape', Yii::getAlias('@runtime/tape'));
        FileHelper::createDirectory(Yii::getAlias('@tape'));
    }

    /**
     * Creates favorite tracks.
     * @noinspection PhpUnused
     */
    public function actionFavorites(int $id, string $title): int
    {
        $tracks = Track::find()
            ->favoritesInLatestOrder()
            ->asArray()
            ->all();

        $items = [];
        foreach ($tracks as $i => $item) {
            $items[$i]['title'] = $item['title'];
            $items[$i]['provider'] = Music::PROVIDERS[$item['provider']];
            $items[$i]['provider_key'] = $item['provider_key'];
            $items[$i]['image'] = $item['image'];
            $items[$i]['slug'] = Inflector::slug($item['title']);
        }

        $now = new DateTime;

        $data['id'] = $id;
        $data['title'] = $title;
        $data['path'] = '/'.$now->format('Y').'/'.$now->format('m').'/'.Inflector::slug($title);
        $data['date'] = $now->format('M d, Y');
        $data['items'] = $items;

        $data = json_encode($data,
            JSON_PRETTY_PRINT |
            JSON_UNESCAPED_SLASHES |
            JSON_UNESCAPED_UNICODE
        );

        $data = preg_replace_callback('/^\s+/m', function($matches) {
            return str_repeat(' ', strlen($matches[0]) / 2);
        }, $data);

        $filename = Yii::getAlias('@tape/'.Inflector::slug($title).'.json');
        file_put_contents($filename, $data);

        $this->stdout('Created: '.$filename."\n");

        return ExitCode::OK;
    }
}
