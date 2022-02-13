<?php
namespace shin85\jstream;

class jstream
{
    private $cid;
    private $writeAPI;
    private $readAPI;
    private $connectAPI;
    public $urlAuthAPI = "https://file-platform.stream.co.jp/WriteApiLocation.aspx";
    public $preUrlWrite = "https://file-platform.stream.co.jp/writeapi";
    public $preUrRead = "http://api01-platform.stream.co.jp/apiservice";
    public $debug = false;

    public function __construct($cid,$writeAPI,$readAPI){
        $this->cid = $cid;
        $this->writeAPI = $writeAPI;
        $this->readAPI = $readAPI;
        if ($this->connectAPI == null){
            $this->connectAPI = $this->connect();
        }
        if ($this->debug === true){
            var_dump($this->connectAPI);
        }
    }
    public function createLiveStream($name= "test", $description= "description", $quality= "low"){
        $data["name"] = $name;
        if (!in_array($quality,["low", "standard", "high", "hd", "fhd", "low"])){
            $quality = "low";
        }
        $data["quality_".$quality] = 1;
        $url = $this->preUrlWrite. "/live/setProfile/".$this->connectAPI;
        $result = [];
        $resultCreateLive = $this->postAPI($url, $data);
        if (isset($resultCreateLive["lpid"])){
            $url = $this->preUrlWrite. "/live/setProfile/".$this->connectAPI;
            $resultGetDataLiveStream = $this->postAPI($url, ["lpid" => $resultCreateLive["lpid"]]);
            $result["lpid"] = $resultCreateLive["lpid"];
            $result["url"] = $resultGetDataLiveStream["encoder_setting"]["server_mainurl"];
        }
        return $result;
    }
    public function getListVideo(){
        if ($this->readAPI){
            $url = $this->urlReadAPI."";
            $result = $this->getAPI();
            if ($this->debug === true) {
                var_dump($url);
            }
        }
    }
    private function connect(){
        if ($this->connectAPI == null){
            $this->connectAPI = $this->postAPI($this->urlAuthAPI,[
                "cid" => $this->cid,
                "API" => $this->writeAPI
            ]);
        }
    }
    private function getAPI($url = "", $params = array()){
        if ($url){
            $fullUrl = $url."?".http_build_query($params);
            $curl = curl_init();
            curl_setopt_array($curl ,[
                CURLOPT_RETURNTRANSFER => 0,
                CURLOPT_URL => $fullUrl,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            $resp = curl_exec($curl);
            curl_close($curl);
            return $resp;
        } else {
            return false;
        }

    }
    private function postAPI($url = "", $data =array()){
        if ($url) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_POST => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => http_build_query($data)
            ));
            $resp = curl_exec($curl);
            curl_close($curl);
            return $resp;
        } else {
            return false;
        }
    }
}