<?php
namespace shin85\jstream;

class jstream
{
    private $cid;
    private $writeAPI;
    private $readAPI;
    private $connectAPI;
    public function __construct($cid,$writeAPI,$readAPI){
        $this->cid = $cid;
        $this->writeAPI = $writeAPI;
        $this->readAPI = $readAPI;
        if ($this->connectAPI == null){
            $this->connectAPI = $this->connect();
        }
    }
    private function connect(){
        if ($this->connectAPI == null){
            
        } else {
            return $this->connectAPI;
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