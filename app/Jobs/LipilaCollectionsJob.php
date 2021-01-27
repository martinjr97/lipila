<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Payments;

class LipilaCollectionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $payment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Payments $payment )
    {
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = $this->createPayload($this->payment);
        $url = "http://41.175.8.69:8181/payments/lipila";
        $ch = curl_init();
        $headers = array();
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $result = curl_exec($ch);
        var_dump($result);
        $result = json_decode($result, TRUE);
        curl_close($ch);


    }
    public function createPayload($payment){
        $payload = [];
        $AUTHENTICATION =[
            'IDENTIFIER' => "Reuben2194",
            'KEY' => "lipilaPaymentGateway"
        ];

        $TRANSACTION = [
            'AMOUNT' =>$payment->amount,
            'MSISDN' => $payment->msisdn,
            'REQUESTID' => $payment->id,
            'REQUESTTYPE' => "$payment->request_type",
        ];
        $payload['AUTHENTICATION'] = $AUTHENTICATION;
        $payload['TRANSACTION'] = $TRANSACTION;
        return $payload;
    }
}
