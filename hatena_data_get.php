<?php

require "./config.php";

class HatenaDataGet {

    private $username = USERNAME;
    private $api_key = APIKEY;
    private $header = array();

    /// コンストラクタ
    public function __construct() {
        $this->setHeader();
    }

    /// curlのヘッダーを作成
    public function setHeader() {

        $nonce = sha1(time() . rand() . getmypid(), true);
        
        $date = new DateTime();
        $blognow = $date->format("Y-m-d\TH:i:s");

        $digest =  base64_encode(sha1($nonce . $blognow . "Z" . $this->api_key, true));
        $credentials =
            sprintf(
                "UsernameToken Username=\"%s\",PasswordDigest=\"%s\",Nonce=\"%s\",Created=\"%s\"",
                $this->username,
                $digest,
                base64_encode($nonce),
                $blognow . "Z"
            );

        $this->header  = array(
            "X-WSSE: $credentials",
            "Accept: application/x.atom+xml, application/xml, text/xml, */*"
        );
    }

    /// APIに接続してデータを取得
    public function getBlogData($connect_url) {
        
        $curl = curl_init($connect_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        if ($response === false) {
            echo 'Curl error: ' . curl_error($curl);
            exit();
        }
        curl_close($curl);

        return $response;
    }

    /// 取得したデータを分解して配列に格納
    public function setXmlToArray($xml_data) {

    }

    /// 次のページのURLを取得して返す
    public function getNextPageUrl($xml_data) {
        $xml = new SimpleXMLElement($xml_data);
        $next = (string)$this->getLinkHref($xml->link, 'next');
        return $next;
    }


    /// xmlのhrefを取得
    private function getLinkHref($links, $rel){
        foreach($links as $n=>$link){
            if($link['rel']==$rel){
                return $link['href'];
            }
        }
        return null;
    }
}

$obj = new HatenaDataGet();
$result = $obj->getBlogData("https://blog.hatena.ne.jp/" . USERNAME . "/" . BLOGID . "/atom/entry");
$next_url = $obj->getNextPageUrl($result);
$result2 = $obj->getBlogData($next_url);
print_r($result2);