<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sourcelogo".
 *
 * @property int $id
 * @property string $host
 * @property string $imagename
 *
 * @property Sourcelogo $sourcelogo
 */
class Sourcelogo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sourcelogo';
    }

    public static function getImageByUrl($url) {
        $domain = Article::getDomain(parse_url($url)['host']);
        if (self::findOne(['host' => trim($domain)])) {
            return self::findOne(['host' => trim($domain)])->imagename;
        }
        else {
            $saveto = '../web/assets/images/source-logos/' . $domain . 'png';

            $ch = curl_init ('https://logo.clearbit.com/'.$domain);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $raw=curl_exec($ch);
            curl_close ($ch);

            if (trim($raw) !== '') {
                $fp = fopen($saveto,'w');
                fwrite($fp, $raw);
                fclose($fp);
            }
            else {
                return false;
            }

            if (file_exists($saveto)) {
                return $domain . 'png';
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['host', 'image'], 'required'],
            [['host'], 'string', 'max' => 50],
            [['host'], 'unique'],
            [['image'], 'string', 'max' => 80]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'host' => 'Host',
            'image' => 'Image',
        ];
    }

}
