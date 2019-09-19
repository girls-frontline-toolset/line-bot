<?php
include_once('lineEvent.php');
include_once('GoogleAnalytics.php');
$googleAn = new GoogleAnalytics();

$line = new lineEvent();
/**
 * @param $event
 * @return mixed
 */
function getID($event)
{
    switch ($event['source']['type']) {
        case "user":
            $id = $event['source']['userId'];
            break;
        case "group":
            $id = $event['source']['groupId'];
            break;
        case "room":
            $id = $event['source']['roomId'];
            break;
        default:
            exit();
    }
    return $id;
}

/**
 * @param $event
 * @return string
 */
function getLangSetting($event)
{
    $id = getID($event);

    include_once("DB.php");
    $DB = new DB();

    $rs = $DB->getLineBotLang($id);
    $lang = "tw";
    if ($rs !== null) {
        $lang = $rs[0]['lang'];
    }
    return $lang;
}

foreach ($line->getEvent() as $event) {

    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            if($event['source']['userId'] == "Uf2adf5c8534fbc4e5682c2990ada6f3b" && $message['text'] == "大佬你好"){
                $listArray = array(array("type" => "text", "text" => "大佬你好"));
                $line->replyMessage($event, $listArray);
                exit();
            }
            switch ($message['type']) {
                case 'text':
                    if(substr($message['text'],0,1) == ':'){
                        exit();
                    }

                    $message = explode(" ", $message['text']);
                    if ($message[0] == "#gl" || $message[0] == "#少前") {
                        if(!isset($message[1])){
                            $googleAn->sendEvent("LineApi", "line_help");
                            $line->printHelpAction($event);
                            exit();
                        }
                        switch ($message[1]) {
                            case 'g':
                                $url = htmlspecialchars_decode("https://www.ntw-20.com/api/inquiry/girl/$message[2]/$message[3]");
                                $dataGet = file_get_contents($url);
                                $dataJson = json_decode(file_get_contents($url));
                                if (!$dataGet) {
                                    $listArray = array(array("type" => "text", "text" => "人型製造時間\n#gl g 1 10\n#少前 g 1 10"));
                                    $line->replyMessage($event, $listArray);
                                    exit();
                                }
                                $dataJson = json_decode($dataGet);

                                if ($message[2] == null || $message[3] == null) {
                                    $googleAn->sendEvent("LineApi", "line_g_time_error");
                                    $line->printHelpAction($event);
                                    exit();
                                }

                                break;
                            case 'f':
                                $url = htmlspecialchars_decode("https://www.ntw-20.com/api/inquiry/fairy/$message[2]/$message[3]");
                                $dataGet = file_get_contents($url);

                                if (!$dataGet) {
                                    $listArray = array(array("type" => "text", "text" => "妖精製造時間\n#gl f 1 10\n#少前 f 5 30"));
                                    $line->replyMessage($event, $listArray);
                                    exit();
                                }
                                $dataJson = json_decode($dataGet);

                                if ($message[2] == null || $message[3] == null) {
                                    $googleAn->sendEvent("LineApi", "line_f_time_error");
                                    $line->printHelpAction($event);
                                    exit();
                                }

                                break;
                            case 'nl':
                                $googleAn->sendEvent("LineApi", "line_name_list");
                                $Glist = json_decode(file_get_contents("https://www.ntw-20.com/api/inquiry/allGirl"));
                                $nameList = "";

                                if ($message[2] !== null) {
                                    $type = strtoupper($message[2]);
                                    if ($type !== "SMG" && $type !== "HG" && $type !== "RF" && $type !== "AR" && $type !== "MG" && $type !== "SG") {
                                        $listArray = array(array("type" => "text", "text" => "只支援 SMG HG RF AR MG SG \n#gl nl SMG 5"));
                                        $line->replyMessage($event, $listArray);
                                    }

                                    if ($message[3] == null) {
                                        foreach ($Glist->data as $list) {
                                            if ($list->name == "") {
                                                continue;
                                            }
                                            if ($list->type == $type) {
                                                $nameList .= $list->name . "\n";
                                            }
                                        }

                                    } else {
                                        $start = $message[3];
                                        if ($start !== "5" && $start !== "4" && $start !== "3" && $start !== "2") {
                                            $listArray = array(array("type" => "text", "text" => "請輸入 1-5 \n#gl nl SMG 5"));
                                            $line->replyMessage($event, $listArray);
                                        } else {
                                            foreach ($Glist->data as $list) {
                                                if ($list->name == "") {
                                                    continue;
                                                }
                                                if ($list->type == $type && $list->star == $start) {
                                                    $nameList .= $list->name . "\n";
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    foreach ($Glist->data as $list) {
                                        if ($list->name == "") {
                                            continue;
                                        }
                                        $nameList .= $list->name . "\n";

                                    }
                                }
                                $line->sendText($event, $nameList);
                                break;
                            case 'd':
                                $url = htmlspecialchars_decode("https://www.ntw-20.com/api/inquiry/device/$message[2]");
                                $dataGet = file_get_contents($url);
                                if (!$dataGet) {
                                    $listArray = array(array("type" => "text", "text" => "裝備製造時間\n#gl d 52\n#少前 d 52"));
                                    $line->replyMessage($event, $listArray);
                                    exit();
                                }
                                $dataJson = json_decode($dataGet);

                                if ($message[2] == null) {
                                    $googleAn->sendEvent("LineApi", "line_d_time_error");
                                    $line->printHelpAction($event);
                                    exit();
                                }
                                break;
                            case 'i':

                                $link = 'https://www.ntw-20.com/api/inquiry/lineGetImage';
                                if ($message[2] !== null) {
                                    if ($message[2] == "iws" || $message[2] == "IWS") {
                                        $message[2] = "IWS 2000";
                                    }

                                    $postdata = http_build_query(array('name' => $message[2]));
                                    $opts = array('http' =>
                                        array(
                                            'method' => 'POST',
                                            'header' => 'Content-type: application/x-www-form-urlencoded',
                                            'content' => $postdata
                                        )
                                    );
                                    $context = stream_context_create($opts);
                                    $rs = json_decode(file_get_contents($link, false, $context));
                                    $googleAn->sendEvent("LineApi", "line_image_$message[2]");
                                } else {
                                    $googleAn->sendEvent("LineApi", "line_image_random");
                                    $rs = json_decode(file_get_contents($link, false));
                                }

                                if ($rs->status == "success") {
                                    if ($message[2] != null) {
                                        $listArray = array(array("type" => "text", "text" => $rs->url), array("type" => "template", "altText" => "更多$message[2]圖片:\nhttps://www.ntw-20.com/image/$message[2]", "template" => array("type" => "buttons", "title" => "更多圖片", "text" => "點擊按鈕以取得更多$message[2]的圖片", "actions" => array(array("type" => "uri", "label" => "點擊查看", "uri" => "https://www.ntw-20.com/image/$message[2]")))));
                                        $line->replyMessage($event, $listArray);
                                    } else {
                                        $line->sendText($event, $rs->url);
                                    }

                                } else {
                                    $Glist = json_decode(file_get_contents("https://www.ntw-20.com/api/inquiry/allGirl"));
                                    $nameList = "";

                                    foreach ($Glist->data as $list) {
                                        if ($list->name == "") {
                                            continue;
                                        }
                                        similar_text($message[2], $list->name, $perc);
                                        if ($perc == 100) {
                                            $line->sendText($event, "沒有這角色的圖片\n加入更多作品:\nhttps://www.ntw-20.com/image/add");
                                            exit();
                                        }
                                        if ($perc >= 60) {
                                            $nameList .= " " . $list->name;
                                        }
                                    }

                                    $text = "請檢查名字有否錯誤";
                                    if ($nameList != "") {
                                        $text .= "\n相似名字 " . $nameList;
                                    }
                                    //$line->sendText($event, $text);
                                    $listArray = array(array("type" => "text", "text" => $text), array("type" => "text", "text" => "名字清單: #gl nl SMG 5"), array("type" => "text", "text" => "網上直接找尋:\nhttps://www.ntw-20.com/image/all"));
                                    $line->replyMessage($event, $listArray);
                                }
                                exit();

                                break;

                            case 'set':
                                if (!isset($message[1]) || !isset($message[2]) || !isset($event['source']['type'])) {
                                    $googleAn->sendEvent("LineApi", "line_help");
                                    $line->printHelpAction($event);
                                    exit();
                                }

                                $id = getID($event);

                                include_once("DB.php");
                                if ($message[2] == 'lang') {
                                    switch ($message[3]) {
                                        case "en";
                                        case "ja";
                                        case "tw";
                                        case "cn";
                                            $DB = new DB();

                                            $rs = $DB->getLineBotLang($id);

                                            if ($rs == null) {
                                                $DB->insertLineBotLang($id, $message[3]);
                                            } else {
                                                $DB->updateLineBotLang($id, $message[3]);
                                            }
                                            $listArray = array(array("type" => "text", "text" => "完成"));
                                            $line->replyMessage($event, $listArray);
                                            exit();
                                            break;
                                        default:
                                            $listArray = array(array("type" => "text", "text" => "en, ja, cn, tw"));
                                            $line->replyMessage($event, $listArray);
                                            exit();
                                    }
                                }

                                break;
                            case 'get':
                                if(!isset($message[1]) || !isset($message[2]) || !isset($event['source']['type']) || $message[2] != 'lang'){
                                    $googleAn->sendEvent("LineApi", "line_help");
                                    $line->printHelpAction($event);
                                    exit();
                                }

                                $lang = getLangSetting($event);

                                $listArray = array(array("type" => "text", "text" => $lang));
                                $line->replyMessage($event, $listArray);
                                exit();

                                break;
                            case 'info':
                                $googleAn->sendEvent("LineApi", "line_info");
                                $line->printInfo($event);
                                exit();
                                break;
                            case 'help':
                                $googleAn->sendEvent("LineApi", "help");
                                $line->printHelp($event);
                                exit();
                            default:
                                $googleAn->sendEvent("LineApi", "line_help");
                                $line->printHelpAction($event);
                                exit();
                                break;
                        }

                        if (!isset($dataJson->status)) {
                            $googleAn->sendEvent("LineApi", "line_help");
                            $line->printHelpAction($event);
                            exit();
                        }

                        if ($dataJson->status == "empty") {
                            $googleAn->sendEvent("LineApi", "line_empty");
                            $line->sendText($event, "找不到有關數據!!");
                            exit();
                        }

                        if ($message[1] == "g") {
                            $lang = getLangSetting($event);
                            $langPath  = ($lang == 'tw')? '': $lang  . "/";

                            $link = 'https://www.ntw-20.com/common/girl/'.  $langPath .'girl_';
                            $imageList = array(array(), array());

                            foreach ($dataJson->data as $dataList) {

                                if ($dataList == "") {
                                    continue;
                                }

                                foreach ($dataList as $data) {
                                    array_push($imageList[$data->heavy], $data->no);
                                }

                            }

                            $listArray = array();

                            if($lang == "tw"){
                                $lang = "";
                            }else{
                                $lang .= "/";
                            }

                            if (count($imageList[0]) != 0) {
                                foreach ($imageList[0] as $no) {
                                    array_push($listArray,
                                        array("type" => "image", "originalContentUrl" => $link. $no . ".jpg",
                                            "previewImageUrl" => "https://img.ump40.com/gf/common/s/girl/" . $lang. "girl_$no.jpg")
                                    );
                                }
                            }

                            if (count($imageList[1]) != 0) {
                                array_push($listArray, array(
                                    'type' => 'text',
                                    'text' => "===大建==="
                                ));

                                foreach ($imageList[1] as $no) {
                                    array_push($listArray,
                                        array("type" => "image", "originalContentUrl" => $link . $no . ".jpg",
                                            "previewImageUrl" => "https://www.ntw-20.com/api/preview.php?id=$no&type=g")
                                    );
                                }
                            }

                            $googleAn->sendEvent("LineApi", "line_search_girl");
                            $line->replyMessage($event, $listArray);
                            exit();

                        } else if ($message[1] == "f") {
                            $link = 'https://www.ntw-20.com/common/fairy/fairy_';
                            $imageList = array();


                            foreach ($dataJson->data as $dataList) {
                                if ($dataList == "") {
                                    continue;
                                }

                                array_push($imageList,
                                    array("type" => "image", "originalContentUrl" => $link . $dataList[0]->no . ".jpg",
                                        "previewImageUrl" => "https://www.ntw-20.com/api/preview.php?id=".$dataList[0]->no."&type=f")
                                );

                            }

                            $googleAn->sendEvent("LineApi", "line_search_fairy");
                            $line->replyMessage($event, $imageList);
                            exit();
                        } else if ($message[1] == "d") {
                            $link = 'https://www.ntw-20.com/api/inquiry/deviceImg/';
                            $imageList = array();

                            foreach ($dataJson->data as $data) {
                                array_push($imageList,
                                    array("type" => "image", "originalContentUrl" => $link . "?at=" . rawurlencode($data->attribute) . "&img=$data->img&dName=" . rawurlencode($data->name) . "&star=$data->star&type=" . rawurlencode($data->type),
                                        "previewImageUrl" => $link . "?at=" . rawurlencode($data->attribute) . "&img=$data->img&dName=" . rawurlencode($data->name) . "&star=$data->star&type=" . rawurlencode($data->type))
                                );
                            }

                            $googleAn->sendEvent("LineApi", "line_search_device");
                            $line->replyMessage($event, $imageList);
                            exit();
                        }

                    } else {
                        $list = file_get_contents("text.json");
                        $roleList = json_decode(file_get_contents("text.json"));
                        foreach ($roleList->role as $role) {

                            if (is_array($role->role)) {
                                foreach ($role->role as $roleName) {
                                    if (!(stripos($event['message']['text'], $roleName) === FALSE)) {
                                        if (isset($role->imgUrl)) {
                                            $googleAn->sendEvent("LineApi", "line_ch_dialogue");

                                            $imgIndex = rand(0, count($role->imgUrl) - 1);
                                            preg_match('/-img:[\d]+/', $event['message']['text'], $matches, PREG_OFFSET_CAPTURE);

                                            if(count($matches) > 0){
                                                $imgIndex  =  intval(str_replace("-img:","",$matches[0][0])) - 1;
                                                if ($imgIndex >= count($role->imgUrl) || $imgIndex < 0 ){
                                                    $imgIndex = count($role->imgUrl) - 1 ;
                                                }
                                            }

                                            $line->sendImage($event, "https://www.ntw-20.com/api/line/img/" . $role->imgUrl[$imgIndex]);
                                        } else {
                                            $googleAn->sendEvent("LineApi", "line_ch_dialogue");
                                            $line->sendText($event, $role->text[rand(0, count($role->text) - 1)]);
                                        }
                                    }
                                }
                            } else {
                                if (!(stripos($event['message']['text'], $role->role) === FALSE)) {
                                    if (isset($role->imgUrl)) {
                                        $googleAn->sendEvent("LineApi", "line_ch_dialogue");
                                        $line->sendImage($event, "https://www.ntw-20.com/api/line/img/" . $role->imgUrl[rand(0, count($role->imgUrl) - 1)]);
                                    } else {
                                        $googleAn->sendEvent("LineApi", "line_ch_dialogue");
                                        $line->sendText($event, $role->text[rand(0, count($role->text) - 1)]);
                                    }
                                }
                            }
                        }

                        if (!(stripos($event['message']['text'], "真") === FALSE)) {
                            if(!(stripos($event['message']['text'], "真的") === FALSE)){
                                exit();
                            }

                            $str = mb_substr($event['message']['text'], strpos($event['message']['text'],"真"),2,"UTF-8");
                            $line->sendImage($event, "https://www.ntw-20.com/api/line/ImgCool.php?str=" . rawurlencode($str),"https://www.ntw-20.com/api/line/ImgCool.php?str=" . rawurlencode($str)."&t=s");
                        }

                    }
                    break;
                case "sticker":

                    if ($message['stickerId'] == "17411361" && $message['packageId'] == "1465208" || $message['stickerId'] == "90359500" && $message['packageId'] == "4857276") {
                        $listArray = array(array("type" => "image", "originalContentUrl" => "https://www.ntw-20.com/api/line/img/" . "a4a6cce.jpg",
                            "previewImageUrl" => "https://www.ntw-20.com/api/line/img/" . "a4a6cce.jpg"),array("type" => "text", "text" => "https://www.pixiv.net/member_illust.php?mode=medium&illust_id=64278016"));
                        $googleAn->sendEvent("LineApi", "line_sticker_img");
                        $line->replyMessage($event, $listArray);
                    }else if($message['stickerId'] == "196172237" && $message['packageId'] ="7958108"){
                        $listArray = array(array("type" => "image", "originalContentUrl" => "https://www.ntw-20.com/api/line/img/" . "FB_IMG_1568855252318.jpg",
                            "previewImageUrl" => "https://www.ntw-20.com/api/line/img/" . "FB_IMG_1568855252318.jpg"));
                        $googleAn->sendEvent("LineApi", "line_sticker_img");
                        $line->replyMessage($event, $listArray);
                    }
                    break;
                default:
                    break;
            }
            break;

        case "join":
            $line->printJoin($event);
            break;

        default:
            error_log("Unsupporeted event type: " . $event['type']);
            break;

    }
};

function get_http_response_code($url)
{
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}


