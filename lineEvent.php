<?php

class lineEvent
{
    private $channelAccessToken;
    private $channelSecret;
    private $client;

    public function __construct(){

        require_once('LINEBotTiny.php');
        require_once('info.php');
        $info = new info();

        $this->channelAccessToken = $info->channelAccessToken;
        $this->channelSecret = $info->channelSecret;
        $this->client = new LINEBotTiny($this->channelAccessToken, $this->channelSecret);
    }

    public function getEvent(){
        return $this->client->parseEvents();

    }

    public function printHelp($event)
    {
        $returnText = "----教學----\n";
        $helpList = json_decode(file_get_contents("text.json"));
        $helpList = $helpList->help;

        foreach ($helpList as $list){
            $returnText .= $list->title . "\n";
            foreach ($list->code as $code){
                $returnText .= $code . "\n";
            }
            $returnText .= "例子: ";
            foreach ($list->example as $example){
                $returnText .= $example. "\n";
            }

            $returnText .= "\n";
        }
        $returnText .=  "----網頁---\nhttps://www.ntw-20.com/bot/line\n----Github---\nhttps://github.com/girls-frontline-toolset/line-bot";

        $this->sendText($event,  $returnText);
        exit();
    }

    public function printHelpAction($event){
        $listArray = array(array("type" => "template", "altText" => "教學: \n#gl help", "template" => array("type" => "buttons", "title" => "教學", "text" => "點擊按鈕以取得教學", "actions" => array(array("type" => "uri", "label" => "點擊查看", "uri" => "line://app/1540225872-9KzwKlzx"),array("type"=>"postback","label"=>"純文字版","data"=>"#gl help","text"=>"#gl help")))));
        $this->replyMessage($event, $listArray);
        exit();
    }

    public function printInfo($event){
        $returnText = json_decode(file_get_contents("text.json"));
        $returnText = $returnText->info;
        $this->sendText($event, $returnText);
        exit();
    }

    public function printJoin($event){
        $returnText = json_decode(file_get_contents("text.json"));
        $returnText = $returnText->join;


        $this->sendText($event,  $returnText);
        exit();
    }

    public function sendText($event,  $returnText ){
        $this->client->replyMessage(array(
            'replyToken' => $event['replyToken'],
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => $returnText
                )
            )
        ));
    }

    public function sendImage($event, $imageUrl ,$previewImageUrl = null){

        if($previewImageUrl == null){
            $previewImageUrl  = $imageUrl;
        }

        $this->client->replyMessage(array(
            'replyToken' => $event['replyToken'],
            'messages' => array(
                array(
                    'type' => 'image',
                    "originalContentUrl" => $imageUrl,
                    "previewImageUrl" => $previewImageUrl
                )
            )
        ));
    }

    public function replyMessage($event,array $array){
        $this->client->replyMessage(array(
            'replyToken' => $event['replyToken'],
            'messages' => $array
        ));
    }

    public function replyCarousel($event,$carouselList){
        $list = [
            "type" => "flex",
            "altText" => $carouselList->altText,
            "contents" => ["type" => "carousel", "contents" => []]
        ];


        foreach ($carouselList->CarouselBeanList as $bean){

            $color = "#a0c11b";

            if($bean->isHeavy){
                $color = "#ed7b4a";
            }

            $beanArray = [ "type" => "bubble",
                "direction" => "ltr",
                "header" => [
                    "type" => "box",
                    "layout" => "vertical",
                    "flex" => 0,
                    "contents" => [
                        [
                            "type" => "image",
                            "url" => $bean->imageUrl,
                            "size" => "full",
                            "aspectRatio" => "9:16",
                            "aspectMode" => "cover",
                            "backgroundColor" => $color,
                            "action" => [
                                "type" => "uri",
                                "uri" => $bean->url
                            ]
                        ]
                    ]
                ],
                "styles" => [
                    "header" => [
                        "backgroundColor" => $color
                    ]
                ]
            ];

            array_push($list["contents"]["contents"],$beanArray);
        }


        $this->replyMessage($event,array($list));
        exit();

    }

}
