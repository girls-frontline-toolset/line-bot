<?php
include_once('lineEvent.php');
include_once('GoogleAnalytics.php');
$googleAn = new GoogleAnalytics();

$line = new lineEvent();
foreach ($line->getEvent() as $event) {

    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            switch ($message['type']) {
                case 'text':
                    $message = explode(" ", $message['text']);
                    if ($message[0] == "#gl" || $message[0] == "#少前") {
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
                                    $line->printHelp($event);
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
                                    $line->printHelp($event);
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
                                    $line->printHelp($event);
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
                            case 'info':
                                $googleAn->sendEvent("LineApi", "line_info");
                                $line->printInfo($event);
                                exit();
                                break;

                            default:
                                $googleAn->sendEvent("LineApi", "line_help");
                                $line->printHelp($event);
                                exit();
                                break;
                        }

                        if (!isset($dataJson->status)) {
                            $googleAn->sendEvent("LineApi", "line_help");
                            $line->printHelp($event);
                            exit();
                        }

                        if ($dataJson->status == "empty") {
                            $googleAn->sendEvent("LineApi", "line_empty");
                            $line->sendText($event, "找不到有關數據!!");
                            exit();
                        }

                        if ($message[1] == "g") {
                            $link = 'https://ntw-20.com/common/girl/girl_';
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

                            if (count($imageList[0]) != 0) {
                                foreach ($imageList[0] as $no) {
                                    array_push($listArray,
                                        array("type" => "image", "originalContentUrl" => $link . $no . ".jpg",
                                            "previewImageUrl" => $link . $no . ".jpg")
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
                                            "previewImageUrl" => $link . $no . ".jpg")
                                    );
                                }
                            }

                            $googleAn->sendEvent("LineApi", "line_search_girl");
                            $line->replyMessage($event, $listArray);
                            exit();

                        } else if ($message[1] == "f") {
                            $link = 'https://ntw-20.com/common/fairy/fairy_';
                            $imageList = array();


                            foreach ($dataJson->data as $dataList) {
                                if ($dataList == "") {
                                    continue;
                                }

                                array_push($imageList,
                                    array("type" => "image", "originalContentUrl" => $link . $dataList[0]->no . ".jpg",
                                        "previewImageUrl" => $link . $dataList[0]->no . ".jpg")
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
                                            $line->sendImage($event, "https://www.ntw-20.com/api/line/img/" . $role->imgUrl[rand(0, count($role->imgUrl) - 1)]);
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
                    }
                    break;
                case "sticker":
                    //$line->sendText($event, json_encode($message));
                    //if($message['stickerId'] == "17411365" && $message['packageId'] == "1465208"){
                       // $line->replyMessage($event, array(array("type"=>"sticker","packageId"=>"1465208","stickerId"=>"17411365")));
                    //}
                    if ($message['stickerId'] == "17411361" && $message['packageId'] == "1465208") {
                        $listArray = array(array("type" => "image", "originalContentUrl" => "https://www.ntw-20.com/api/line/img/" . "a4a6cce.jpg",
                            "previewImageUrl" => "https://www.ntw-20.com/api/line/img/" . "a4a6cce.jpg"),array("type" => "text", "text" => "https://www.pixiv.net/member_illust.php?mode=medium&illust_id=64278016"));
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


