<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiPonController extends Controller
{
    public function getPon(Request $request)
    {
        $pon = $request->pon??1;
        $type = $request->type??'onualllist';

        $payload=[
            'pon' => $pon,
            'type' => $type
        ];

        $data = self::requestPon($payload);

        return response()->json([
            'data' => $data
        ]);
    }

    public static function requestPon($payload)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://103.163.227.218:2532/sw.cgi',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>"get={$payload['type']}&ponport={$payload['pon']}",
          CURLOPT_HTTPHEADER => array(
            'Referer: http://103.163.227.218:2532/m/onu_info.htm',
            'Content-Type: uni_mars_ap'
          ),
        ));

        $response = curl_exec($curl);

        return self::extractXmlResponse($response);
    }

    public static function extractXmlResponse($text)
    {
        $xml = simplexml_load_string($text);
        $data = [];

        foreach ($xml->item as $item) {
            if (isset($item['onu'])) {
                $values = explode(",", (string) $item['onu']);
                
                $data[] = [
                    "slot_pon" => $values[0] ?? '',
                    "nama" => $values[1] ?? '',
                    "mac_address" => $values[2] ?? '',
                    "status" => $values[3] ?? '',
                    "olt" => $values[4] ?? '',
                    "vlan" => $values[5] ?? '',
                    "bw_up" => $values[6] ?? '',
                    "bw_down" => $values[7] ?? '',
                    "mcast" => $values[8] ?? '',
                    "snooping" => $values[9] ?? '',
                    "unicast" => $values[10] ?? '',
                    "power" => $values[11] ?? '',
                    "rx_power" => $values[12] ?? '',
                    "tx_power" => $values[13] ?? '',
                    "voltage" => $values[14] ?? '',
                    "temp" => $values[15] ?? '',
                    "optical_power" => $values[16] ?? '',
                ];
            }
        }
        return $data;
    }
}
