<?php
/**
 * Author: Stefan Lee
 * 
 */

namespace shin85\jstream;

class jstream
{
    private $cid;
    private $tokenWriteAPI;
    private $tokenReadAPI;
    private $connectAPI;
    public $urlAuthAPI = "https://file-platform.stream.co.jp/WriteApiLocation.aspx";
    public $preUrlWrite = "https://file-platform.stream.co.jp/writeapi";
    public $preUrlRead = "http://api01-platform.stream.co.jp/apiservice";
    public $debug = false;

    public function __construct($cid, $tokenWriteAPI, $tokenReadAPI)
    {
        $this->cid = $cid;
        $this->tokenWriteAPI = $tokenWriteAPI;
        $this->tokenReadAPI = $tokenReadAPI;
        if ($this->connectAPI == null) {
            $this->connect();
        }
    }
    public function createLiveStream($name = "test", $description = "description", $quality = "low")
    {
        if ($this->connectAPI) {
            $data["name"] = $name;
            if (!in_array($quality, ["low", "standard", "high", "hd", "fhd", "low"])) {
                $quality = "low";
            }
            $data["quality_" . $quality] = 1;
            $url = $this->preUrlWrite . "/live/setProfile/" . $this->connectAPI;
            $result = [];
            $resultCreateLive = $this->postAPI($url, $data, true);
            if (isset($resultCreateLive["lpid"])) {
                $url = $this->preUrlWrite . "/live/getEncoder/" . $this->connectAPI;
                $resultGetDataLiveStream = $this->postAPI($url, ["lpid" => $resultCreateLive["lpid"]], true);
                $result["lpid"] = $resultCreateLive["lpid"];
                $result["url"] = $resultGetDataLiveStream["encoder_setting"]["server_mainurl"];
            }
            return $result;
        }
    }
    public function getListVideo()
    {
        if ($this->tokenReadAPI) {
            $url = $this->preUrlRead . "/getMediaByParam/";
            $params["token"] = $this->tokenReadAPI;
            $result = $this->getAPI($url, $params);
            preg_match("/searchResultEq\((.*)\);/i", $result, $matches);
            return json_decode($matches[1], true);
        } else {
            return false;
        }
    }
    private function connect()
    {
        if ($this->connectAPI == null) {
            $this->connectAPI = $this->postAPI($this->urlAuthAPI, [
                "cid" => $this->cid,
                "API" => $this->tokenWriteAPI
            ]);
        }
    }
    private function getAPI($url = "", $params = array(), $json = false)
    {
        if ($url) {
            $fullUrl = $url . "?" . http_build_query($params);
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $fullUrl,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            $resp = curl_exec($curl);
            curl_close($curl);
            if ($json) {
                return json_decode($resp, true);
            } else {
                return $resp;
            }
        } else {
            return false;
        }
    }
    private function postAPI($url = "", $data = array(), $json = false)
    {
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
            if ($json) {
                return json_decode($resp, true);
            } else {
                return $resp;
            }
        } else {
            return false;
        }
    }
}