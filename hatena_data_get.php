<?php

require "./config.php";

class HatenaDataGet {

    private $username = USERNAME;
    private $api_key = APIKEY;
    private $header = array();
    public $get_url = '';

    /// コンストラクタ
    public function __construct() {
        $this->setHeader();
        $this->setFirstUrl();
    }

    /// curlのヘッダーを作成
    private function setHeader() {

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

    /// 最初に取得するURLをセット
    private function setFirstUrl() {
        $this->get_url = "https://blog.hatena.ne.jp/" . USERNAME . "/" . BLOGID . "/atom/entry";
    }

    /// APIに接続してデータを取得
    private function getBlogData() {
        
        $curl = curl_init($this->get_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        if ($response === false) {
            echo 'Curl error: ' . curl_error($curl);
            curl_close($curl);
            return null;
        }
        curl_close($curl);

        return $response;
    }

    /// 取得したデータを分解して配列に格納
    private function setXmlToArray($xml_data) {
        $temp_arr = array();
        $preg_pattern = '/<hatena:formatted-content type=\"text\/html\" xmlns:hatena=\"http:\/\/www\.hatena\.ne\.jp\/info\/xmlns#\">(.*?)<\/hatena:formatted-content>/s';
        $xml = new SimpleXMLElement($xml_data);
        preg_match_all($preg_pattern, $xml_data, $preg_temp_arr);
        $key = 0;
        foreach($xml->entry as $loop) {
            //print_r($loop);
            $temp_arr[$key]['blog_id'] =  (string)$loop->id;
            $temp_arr[$key]['author'] = (string)$loop->author->name;
            $temp_arr[$key]['atom_url'] = (string)$this->getLinkHref($loop->link, 'edit');
            $temp_arr[$key]['url'] = (string)$this->getLinkHref($loop->link, 'alternate');
            $temp_arr[$key]['title'] = (string)$loop->title;
            $temp_arr[$key]['content'] = (string)$loop->content;
            $temp_arr[$key]['format_content'] = $preg_temp_arr[1][$key];
            $temp_arr[$key]['published'] = (string)$loop->published;
            $temp_arr[$key]['updated'] = (string)$loop->updated;
            $temp_arr[$key]['summary'] = (string)$loop->summary;
            $categories = [];
            foreach($loop->category as $n=>$tag){
                $categories[] = (string)$tag['term'];
            }
            $temp_arr[$key]['category'] = implode(',',$categories);
            $key++;
        }

        return $temp_arr;
    }

    /// 次のページのURLを取得して返す
    private function getNextPageUrl($xml_data) {
        $xml = new SimpleXMLElement($xml_data);
        $next = (string)$this->getLinkHref($xml->link, 'next');
        $this->get_url = $next;
    }

    /// データを取得して次のページのURLをセットする
    public function getData() {
        $xml_data = $this->getBlogData();
        if($xml_data == null) {
            return null;
        }
        $this->getNextPageUrl($xml_data);
        return $this->setXmlToArray($xml_data);
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
