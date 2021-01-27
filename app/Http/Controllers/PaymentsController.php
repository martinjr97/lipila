<?php

namespace App\Http\Controllers;

use App\Jobs\LipilaCollectionsJob;
use App\Jobs\MtnCollectionsJob;
use Illuminate\Http\Request;
use App\Models\Payments;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class PaymentsController extends Controller
{
    public function index()
    {
        return view('lipilaPay');

    }
    public function collections(request $request)
    {


            $validator = Validator::make(
                $request->all(), [
                'MSISDN' => ['required','regex:/^(0|260)((95|96|97)|(76|77))[0-9]{7}$/u',],
                'AMOUNT' => 'required','numeric',
            ]);

            if ($validator->passes()) {

             try {
                 $number = $request['MSISDN'];
                 $number = $this->validateNumber($number);

                 $num = "$number";

                    if (strlen($num) == 12) {

                        $payment = new Payments();
                        $payment->msisdn = $request['MSISDN'];
                        $payment->amount = $request['AMOUNT'];
                        $payment->request_type = 'collection';
                        $payment->status_code = 201;
                        $payment->created_at = Carbon::now();
                        $payment->saveOrFail();

                         LipilaCollectionsJob::dispatch($payment);
                        return response()->json(['status_code' => "200",
                            'status_message' => "processing done",
                        ]);


                                       } else {
                        return response()->json(['status_code' => "285",
                            'status_message' => "Invalid Number submitted",
                        ]);
                    }
                }
            catch (Exception $ex) {

                    return response()->json(['status_code' => "289",
                        'status_message' => "Error Occured Please Try Again"]);
                }
            } else {
                return response()->json(['status_code' => "284",
                    'status_message' => "Invalid Payload",
                    'errors' => json_encode($validator->errors())]);
            }


    }
    public function validateNumber($number)
    {

        $rest = substr( $number, -10);
        $number = "26"."$rest";


        return $number;
    }
        public function lipilaCallback(Request $request)
    {
        dd($request);
        if ($request->json()->all()) {
            $request = $request->json()->all();
            $validator = Validator::make(
                $request, [

                'TXID' => 'required',
                'EXTID' => 'required',
                'AMOUNT' => 'required',
                'RESPONCECODE' => 'required',
                'DESCRIPTION' => 'required',
                'MNOID' => 'nullable',
                'STATUS' => 'nullable',
                'TXN_STATUS' => 'nullable',
            ]);
            if ($validator->passes()) {

                $getPayment = Payments::where('id',$request['EXTID'])->first();
                if(RESPONSECODE == 100){
                    $getPayment->updated_at = Carbon::now();
                    $getPayment->status_code = 200;
                    $getPayment->txid= $request['TXID'];
                    $getPayment->saveOrFail();
                }else{
                    $getPayment->status_code = 202;
                    $getPayment->updated_at = Carbon::now();
                    $getPayment->txid= $request['TXID'];
                    $getPayment->saveOrFail();
                }



            } else {
                return response()->json(['status_code' => "284",
                    'status_message' => "Invalid Payload",
                    'errors' => json_encode($validator->errors())]);
            }
        } else {
            return response()->json(['status_code' => "284",
                'status_message' => "Invalid Payload"]);
        }



    }
}
