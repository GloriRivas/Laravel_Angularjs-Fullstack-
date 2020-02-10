<?php

use App\Libraries\InnerConvertApi;
use App\Models2\ClassSchedule;
use App\Models2\NotificationMobHistory;
use App\Models2\Payment;
use App\Models2\Transportation;
use App\Models2\User;
use Carbon\Carbon;
use ConvertApi\ConvertApi;
use Hashids\Hashids;
use Illuminate\Support\Facades\DB;

Route::get('test', function(){
	function __gen($interval) {
		while(true) {
	    if( time() % $interval == 0 ) { // get time in seconds and check if it is multiple of 5
	      echo rand(10000, 99999);
	      break; // break the loop
	    } else {
	      sleep(1);
	    }
		}
	}

	srand(mt_rand(1, 5));
	echo mt_rand(10000, 99999);

	// __gen(5);
});

Route::get('make-invoices1', function(){
	// if(get_server_info()['server_type'] == 'sxd') {
	// 	Payment::where('paymentTitle', 'Transport Fee Feb_Mar_2020')->delete();
	// 	$old_payments = Payment::where('paymentTitle', 'Transport Fee Aug_Sept_2019')->get()->toArray();
	// 	foreach ($old_payments as $key => $old_payment) {
	// 		$payment = new Payment;
	// 		$payment->paymentTitle = 'Transport Fee Feb_Mar_2020';
	// 		$payment->paymentDescription = 'Transport Fee Feb_Mar_2020';
	// 		$payment->paymentStudent = $old_payment['paymentStudent'];
	// 		$payment->paymentRows = $old_payment['paymentRows'];
	// 		$payment->paymentAmount = $old_payment['paymentAmount'];
	// 		$payment->paymentDiscount = $old_payment['paymentDiscount'];
	// 		$payment->paymentDiscounted = $old_payment['paymentDiscounted'];
	// 		$payment->paidAmount = 0;
	// 		$payment->paymentStatus = 0;
	// 		// Dec - Jan 1/12/2019 & 21/12/2019 -> 1575158400 : 1576886400
	// 		// feb - march 1/02/2020 & 21/02/2020 -> 1580515200 : 1582243200
	// 		$payment->paymentDate = 1580515200;
	// 		$payment->dueDate = 1582243200;
	// 		$payment->dueNotified = 0;
	// 		$payment->paymentUniqid = $old_payment['paymentUniqid'];
	// 		$payment->paymentSuccessDetails = null;
	// 		$payment->paidMethod = null;
	// 		$payment->paidTime = null;
	// 		$payment->discount_id = $old_payment['discount_id'];
	// 		$payment->fine_amount = $old_payment['fine_amount'];
	// 		$payment->save();
	// 	}
	// }
});

Route::get('test_convert_api', function(){
	// $convertApi = new InnerConvertApi;
	// $key = 'NeTnxqobjQcjhLi8'; // 1500
	// $key = 'OnMGZkmAp9mrFMZP'; // 1500
	// $key = 'yLREzuGZH6u12eht'; // 1500
	// $key = 'S7muBsCIOPJ1q371'; // 1500
	// $key = 'Ap287pJ4IYaRS1S4'; // 1500
	// $key = 'Q3TzqlKNChxvsYAc'; // 1500
	// $key = 'zyrDeJsphYzo8jLR'; // 1500
	// $key = 'EuyiLzUjpQGWXh52'; // 1500
	$key = 'B0ESLjFAFLPh229G'; // 1500
	$convertApi = new ConvertApi;
	$convertApi->setApiSecret($key);
	dd($convertApi->getUser());
	// $result = $convertApi->convert('jpg', [
 //        'File' => storage_path('app/Latest_SXD_Vison_2020.pdf'),
 //        'ScaleImage' => 'true',
 //        'JpgQuality' => '20',
 //        'ImageWidth' => '800',
 //        'Timeout' => '300',
 //    ], 'pdf'
	// );
	// $result->saveFiles(storage_path('app/pdf_images'));

	// dd(uploads_config());
});

Route::get('usage', function(){
	$free = shell_exec('free');
	$free = (string)trim($free);
	$free_arr = explode("\n", $free);
	$mem = explode(" ", $free_arr[1]);
	$mem = array_filter($mem);
	$mem = array_merge($mem);
	$memory_usage = $mem[2]/$mem[1]*100;
	$load = sys_getloadavg();

	return view('usage')->with([
		'memory_usage' => $memory_usage,
		'cpu_usage' => $load[0]
	]);
});

Route::get('remove-cache', function(){
	\Cache::flush();
	return redirect('/portal#/');
});

// Route::get('remove-all-firebasetokens', function(){
// 	DB::table('users')->update(['firebase_token' => '']);
// });