<?php

namespace App\Http\Controllers;
use App\Plan;
use Illuminate\Http\Request;


class MainController extends Controller
{
   
    // public function show()
    // {
    //     return view('welcome');
    // }

    public function index()
    {
            $plans = Plan::all();
            return view('plans.index', ['plans' => $plans]);
    }

    public function show(Plan $plan, Request $request)
    {
        $paymentMethods = $request->user()->paymentMethods();

        $intent = $request->user()->createSetupIntent();
        return view('plans.show', ['plan' => $plan,'intent' => $intent]);
        //return view('plans.show', ['plan' => $plan]);
    }
    
    public function create(Request $request, Plan $plan)
    {
        $plan = Plan::findOrFail($request->get('plan'));

        $user = $request->user();
        $paymentMethod = $request->paymentMethod;
        
        $request->user()
            ->newSubscription('main', $plan->price_id)
            ->create($paymentMethod, [
                'email' => $user->email,
            ]);
        
        return redirect()->route('/home');
    }

    public function fetchinvoices(Request $request)
    {
        $invoices = $request->user()->upcomingInvoice();
        
        return view('plans.invoices', ['invoices' => $invoices]);
    }
}