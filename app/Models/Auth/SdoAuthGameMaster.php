<?php

namespace Xnhd\Core\Auth;

use Xnhd\Core\Socket\CCodeEngine;
use Xnhd\Core\Socket\CCSHeadOption;
use Xnhd\Core\Socket\CCSHead;
use Xnhd\Core\Socket\Fiveonelib_SockStream;

require_once dirname(__DIR__).'/Protobuf/Sdo/pb_proto_authserver.php';

class SdoAuthGameMaster {
    private $socket_stream;
    private $sequence = 0;
    private $host;
    private $port;

    function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * 公共头部方法
     * @param unknown_type $zoneid
     * @param unknown_type $bodyLength
     * @param unknown_type $messageid
     */
    private function head($zoneid, $bodyLength, $messageid){
        $headOption = new CCSHeadOption();
        $headOption->optionArray = array(
            'zoneid'	=> array('int32', $zoneid)
        );
        $headOption->encode($option, $optionLength);

        $cshead = new CCSHead();
        $cshead->shPackageLength= $bodyLength + $cshead->size() + $optionLength;
        $cshead->nOptionalLen	= $optionLength;
        $cshead->lpbyOptional	= $option;
        $cshead->nHeaderLen	= $cshead->size() + $optionLength;
        $cshead->shMessageID	= $messageid;
        $cshead->nSequence	= $this->sequence++;

        return $cshead;
    }

    function pushAuthInfo($zoneid, $account,$ServerKey,$keyLen,$optiondata,$optionLen)
    {
        $this->socket_stream = new Fiveonelib_SockStream(AF_INET, SOL_TCP);
        $resultcon = $this->socket_stream->connect($this->host, $this->port, 1);// or die('can not connect gmServer');
        if($resultcon === false)
        {
            $result['ret']=-1;
            $result['desc']="can not connect auth server ";
            return $result;
        }

        $request = new \AUSPushAuthInfoReq();
        $request->setSzAccountID($account);
        $request->setSzPlayerSignature($ServerKey);
        $request->setSzOptional($optiondata);
        $request->setNPlayerSignatureLen($keyLen);
        $request->setNOptionalLen($optionLen);

        $result['ret']=0;
        $result['desc']="OK";
        $body = $request->serializeToString();
        $bodyLength = strlen($body);
        $message = new \MSGIDAUTHSERVER();
        $arrMessage = $message->getEnumValues();
        $cshead = $this->head($zoneid, $bodyLength, $arrMessage['MSGID_CAUS_REQ_PUSHAUTHINFO']);
        $cshead->encode($head, $headLength);

        //send data to auth server
        $send_result = $this->socket_stream->write($head.$body, $headLength+$bodyLength+1, 10);
        if(0 == $send_result)
        {
            $result['ret']=-1;
            $result['desc']="can not send data to server ";
            return $result;
        }
        //recv response from auth server
        $recieve_result = $this->socket_stream->read(4, 10);
        if ($recieve_result != NULL)
        {
            CCodeEngine::decode_int32($recieve_result, $packageLegth);
            $recieve_result = $this->socket_stream->read($packageLegth-4, 10);
            if ($recieve_result !== FALSE)
            {
                $result['desc']="finish over";
                $response = new \AUSPushAuthInfoResp();
                $response->parseFromString(substr($recieve_result, 26));
                $result['ret']= $response->getNErrorCode();
                return $result;
            }
            else
            {
                $result['desc']="wrong";
                $result['ret']= -3;
                return $result;
            }
        }

        $result['ret']=-2;
        $result['desc']="can not recv data from auth server ";
        return $result;
    }

    public function MakePlayerSignature( $szAccount, $arrSessionKey, $nTimeStamp, $nExpireTime, $nRandom, $szMD5Checksum, $ServerKey,  $nSessionKeyCount = 16)
    {
        $xtea = new Xtea_Encrypt();

        $nCryptMeans = $this->HeadFlag;
        $body =pack('c', $nCryptMeans);

        $tmp_string_length = strlen($szAccount);
        if ($tmp_string_length != 0)
        {
            $tmp_string_length += 1;
        }

        if ($tmp_string_length  > 128)
        {
            $tmp_string_length = 128;
        }
        $package = pack('n',$tmp_string_length);
        //echo '$package 1::'.  bin2hex($package) .'<br>';
        $package .= pack('a'.$tmp_string_length, $szAccount);
        //echo '$package 2::'.  bin2hex($package) .'<br>';
        $package .= pack('N', $nSessionKeyCount);
        //echo '$package 3::'.  bin2hex($package) .'<br>';


        $package .=  $arrSessionKey;
        //echo '$package 4::'.  bin2hex($package) .'<br>';
        $package .= pack('N', $nTimeStamp);
        $package .= pack('N', $nExpireTime);
        $package .= pack('N', $nRandom);
        //echo '$package 5::'.  bin2hex($package) .'<br>';

        $tmp_string_length = strlen($szMD5Checksum);
        //echo 'szMD5Checksum length::'.$tmp_string_length.'<br>';
        $package .= pack('N',$tmp_string_length);
        //$package .= pack('a'.$tmp_string_length, $szMD5Checksum);
        $package .= $szMD5Checksum;
        //echo 'PlayerSignature::'.bin2hex($package).'<br>';
        //echo '$package 6::'.  bin2hex($package) .'<br>';

        $structLen = strlen($package);
        $tmp_structLen = pack('n',$structLen);
        $package = $tmp_structLen.$package;
        //echo '$package 7::'.  bin2hex($package) .'<br>';
        $tmp_package = $xtea->Encrypt($package, $ServerKey);

        $body .= $tmp_package;

        $nSize = 2 + strlen($body);
        $nSizeLen = pack('n', $nSize);
        $tmp_body = $nSizeLen.$body;
        //echo 'PlayerSignature 222 ::'.bin2hex($tmp_body).'<br>';
        return $tmp_body;
    }


    public function MakeAuthOptinal( $authType, $authLeval, $registerTime, $registType, $LoginChannel, $LoginType, $iopenid, $itime,$userFlag)
    {
        $optional = pack('c', $authType);
        $optional .= pack('c', $authLeval);
        $optional .=pack('N', $registerTime);
        $optional .=pack('c', $registType);
        $optional .=pack('N', $LoginChannel);
        $optional .=pack('c', $LoginType);
        $optional .=pack('N', $userFlag);


        $tmp_string_length = strlen($iopenid);
        if ($tmp_string_length != 0)
        {
            $tmp_string_length += 1;
        }

        if ($tmp_string_length  > 128)
        {
            $tmp_string_length = 128;
        }

        $optional .= pack('n',$tmp_string_length);
        $optional .= pack('a'.$tmp_string_length, $iopenid);

        $optional .=pack('N', $itime);
        $optionalLen = strlen($optional);

        $package = pack('N', $optionalLen);
        $package .= $optional;

        return $package;
    }
}


