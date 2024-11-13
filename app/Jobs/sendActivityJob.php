<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class sendActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $id_group_wa;
    protected $nama;
    protected $cabang;
    protected $activity;
    protected $foto;
    protected $lv;
    public function __construct($id_group_wa, $nama, $cabang, $activity, $foto, $lv)
    {
        $this->id_group_wa = $id_group_wa;
        $this->nama = $nama;
        $this->cabang = $cabang;
        $this->activity = $activity;
        $this->foto = $foto;
        $this->lv = $lv;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $group_wa = ['120363181708613638@g.us', '120363048652516047@g.us', '120363023468297226@g.us'];
        $url = "https://sfa.pacific-tasikmalaya.com/storage/uploads/smactivity/";
        // $url = "https://sfa.pedasalami.com/storage/uploads/smactivity/";
        if ($this->lv == "manager marketing") {
            foreach ($group_wa as $d) {
                $pesan = [
                    'api_key' => 'B2TSubtfeWwb3eDHdIyoa0qRXJVgq8',
                    'sender' => '6289670444321',
                    'number' => '6282220804021',
                    'media_type' => 'image',
                    'caption' => '*' . $this->nama . ': (' . $this->cabang . ')* ' . $this->activity,
                    'url' => $url . $this->foto
                ];

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://wa.pedasalami.com/send-media',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($pesan),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                sleep(5);
                flush();
            }
        } else {
            $pesan = [
                'api_key' => 'B2TSubtfeWwb3eDHdIyoa0qRXJVgq8',
                'sender' => '6289670444321',
                'number' => $this->id_group_wa,
                'media_type' => 'image',
                'caption' => '*' . $this->nama . ': (' . $this->cabang . ')* ' . $this->activity,
                'url' => $url . $this->foto
            ];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://wa.pedasalami.com/send-media',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($pesan),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
        }
    }
}
