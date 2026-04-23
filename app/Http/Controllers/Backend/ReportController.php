<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use DateTime;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function ReportView(){

        return view('admin.backend.report.report_view');

    }//end method
public function SearchByDate(Request $request){

    $date = new DateTime($request->date);
    $formateDate = $date->format('d F Y');
    $payment = Payment::where('order_date', $formateDate)->latest()->get();
    //dd($date);
    return view('admin.backend.report.report_by_date', compact('payment','formateDate'));   
}//end method
       
public function SearchByMonth(Request $request){

    $month = $request->month;
    $year = $request->year_name;
    $payment = Payment::where('order_month', $month)->where('order_year',$year)->latest()->get();
    //dd($date);
    return view('admin.backend.report.report_by_month', compact('payment','month','year'));   
}//end method

public function SearchByYear(Request $request){

   $year = $request->year;
    $payment = Payment::where('order_year',$year)->latest()->get();
    return view('admin.backend.report.report_by_year', compact('payment','year'));      
}//end method

}
