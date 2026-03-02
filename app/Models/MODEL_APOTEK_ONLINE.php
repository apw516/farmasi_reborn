<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class MODEL_APOTEK_ONLINE extends Model
{
    public $baseUrl = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/';
    // public $baseUrl = 'https://apijkn.bpjs-kesehatan.go.id/vclaim-rest/';
    public static function signature()
    {

        $cons_id =  env('CONS_ID_APT');
        $secretKey = env('SECRET_KEY_APT');
        $userkey = env('USER_KEY_APT');

        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $cons_id . "&" . $tStamp, $secretKey, true);
        $encodedSignature = base64_encode($signature);
        $response = array(
            'user_key' => $userkey,
            'x-cons-id' => $cons_id,
            'x-timestamp' => $tStamp,
            'x-signature' => $encodedSignature,
            'decrypt_key' => $cons_id . $secretKey . $tStamp,
        );
        return $response;
    }
    public static function stringDecrypt($key, $string)
    {
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $output = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $output;
    }
    public function referensi_dpho()
    {
        $client = new Client();
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/referensi/dpho';
        $signature = $this->signature();
        try {
            $response = $client->request('GET', $url, [
                'headers' => $signature
            ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        } catch (ClientException) {
            return 'RTO';
        }
    }
    public function referensi_poli($poli)
    {
        $client = new Client();
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/referensi/poli/' . $poli;
        $signature = $this->signature();
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_faskes($jenisfaskes, $faskes)
    {
        $client = new Client();
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/referensi/ppk/' . $jenisfaskes . '/' . $faskes;
        $signature = $this->signature();
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function setting_apotek($kode_apotek)
    {
        $client = new Client();
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/referensi/settingppk/read/' . $kode_apotek;
        $signature = $this->signature();
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_spesialistik($kode_apotek)
    {
        $client = new Client();
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/referensi/spesialistik';
        $signature = $this->signature();
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function referensi_obat($kode_jenis_obat, $tglresep, $filter)
    {
        $client = new Client();
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/referensi/obat/' . $kode_jenis_obat . '/' . $tglresep . '/' . $filter;
        $signature = $this->signature();
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function save_non_racik($dataobat)
    {
        $client = new Client();
        $data = json_encode($dataobat);
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/obatnonracikan/v3/insert';
        $signature = $this->signature();
        try {
            $response = $client->request('POST', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20
            ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        } catch (ClientException) {
            return 'RTO';
        }
    }
    public function save_racikan($dataobat)
    {
        $client = new Client();
        $data = json_encode($dataobat);
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/obatracikan/v3/insert';
        $signature = $this->signature();
        try {
            $response = $client->request('POST', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20
            ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        } catch (ClientException) {
            return 'RTO';
        }
    }
    public function update_Stok_obat($dataobat)
    {
        $client = new Client();
        $data = json_encode($dataobat);
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/UpdateStokObat/updatestok';
        $signature = $this->signature();
        try {
            $response = $client->request('POST', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20
            ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        } catch (ClientException) {
            return 'RTO';
        }
    }
    public function hapus_pelayanan_obat($dataobat)
    {
        $client = new Client();
        $data = json_encode($dataobat);
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/pelayanan/obat/hapus/';
        $signature = $this->signature();
        // try {
            $response = $client->request('DELETE', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20
            ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        // } catch (ClientException) {
        //     return 'RTO';
        // }
    }
    public function daftar_pelayanan_obat($no_sep)
    {
        $client = new Client();
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/obat/daftar/' . $no_sep;

        $signature = $this->signature();
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function riwayat_obat($tglawal, $tglakhir, $nokartu)
    {
        $client = new Client();
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/riwayatobat/' . $tglawal . '/' . $tglakhir . '/' . $nokartu;
        $signature = $this->signature();
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function simpan_resep($dataobat)
    {
        $client = new Client();
        $data = json_encode($dataobat);
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/sjpresep/v3/insert';
        $signature = $this->signature();
        try {
            $response = $client->request('POST', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20
            ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        } catch (ClientException) {
            return 'RTO';
        }
    }
    public function hapus_resep($dataobat)
    {
        $client = new Client();
        $data = json_encode($dataobat);
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/hapusresep';
        $signature = $this->signature();
        try {
            $response = $client->request('DELETE', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20
            ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        } catch (ClientException) {
            return 'RTO';
        }
    }
    public function daftar_resep($dataobat)
    {
        $client = new Client();
        $data = json_encode($dataobat);
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/daftarresep';
        $signature = $this->signature();
        try {
            $response = $client->request('POST', $url, [
                'headers' => $signature,
                'body' => $data,
                'allow_redirects' => true,
                'timeout' => 20
            ]);
            $response = json_decode($response->getBody());
            if ($response->metaData->code == 200) {
                $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
                $response->response = json_decode($decrypt);
            }
            return $response;
        } catch (ClientException) {
            return 'RTO';
        }
    }
    public function carikunjungansep($nosep)
    {
        $client = new Client();
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/sep/' . $nosep;
        $signature = $this->signature();
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function caridataklaim($bulan, $tahun, $jenisobat, $status)
    {
        $client = new Client();
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/monitoring/klaim/' . $bulan . '/' . $tahun . '/' . $jenisobat . '/' . $status;
        $signature = $this->signature();
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
    public function rekap_peserta_prb($tahun, $bulan)
    {
        $client = new Client();
        $url = 'https://apijkn-dev.bpjs-kesehatan.go.id/apotek-rest-dev/Prb/rekappeserta/tahun/' . $tahun . '/bulan' . '/' . $bulan;
        $signature = $this->signature();
        $response = $client->request('GET', $url, [
            'headers' => $signature
        ]);
        $response = json_decode($response->getBody());
        if ($response->metaData->code == 200) {
            $decrypt = $this->stringDecrypt($signature['decrypt_key'], $response->response);
            $response->response = json_decode($decrypt);
        }
        return $response;
    }
}
