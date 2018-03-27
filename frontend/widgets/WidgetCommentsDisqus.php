<?php
namespace frontend\widgets;
use Yii;
use yii\base\Widget;

class WidgetCommentsDisqus extends Widget{
    public $disqus_shortname = 'outstyle-org';
    public $disqus_url = null;
    public function run()
    {
        if($this -> disqus_shortname)
        {
            $Request = Yii::$app -> getRequest();

            $disqus_url = $Request -> hostInfo;
            $parse_ar = parse_url($disqus_url);

            if($this -> disqus_url === null)
            {
                $disqus_url .= $Request -> url;
            }
            else
            {
                $disqus_url .= $this -> disqus_url;
            }

            return $this -> render('widgetCommentsDisqus',[
                'disqus_shortname' => $this -> disqus_shortname,
                'disqus_url' => $disqus_url,
                'scheme' => $parse_ar['scheme']
            ]);
        }
        return '';
    }

}