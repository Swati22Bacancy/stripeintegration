<?php

namespace App\Http\Controllers;
use App\Plan;
use Illuminate\Http\Request;
use Stripe\Service\PlanService;
use App\User;
use App\Subscriptions;

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
        
            return redirect()->route('plans.fetchusersubscriptions');
    }

    public function fetchinvoices(Request $request)
    {

        $stripeCustomer = Subscriptions::where('user_id', '=', $request->user()->id)->first();
        if($stripeCustomer)
        {
            $invoices = $request->user()->upcomingInvoice();
        }
        else
        {
            $invoices =[];
        }
       
        
        return view('plans.invoices', ['invoices' => $invoices]);
    }

    public function fetchusersubscriptions(Request $request)
    {
        $stripeCustomer = Subscriptions::where('user_id', '=', $request->user()->id)->first();
        if($stripeCustomer)
        {
            $subscriptions = $request->user()->asStripeCustomer()->subscriptions;
        }
        else
        {
            $subscriptions =[];
        }
        return view('plans.fetchusersubscriptions', ['subscriptions' => $subscriptions]);
        
    }

    public function fetchallplans(Request $request)
    {
        $stripe = new \Stripe\StripeClient(
            'sk_test_51HDQhHJBwKM9SgpoOjcBm7X8hxvSpkKsNBRKfcALuj8BYUzBQOF90thCizw0UoEjWTSsw9Y2D2QswsapkRoXh9ox006QorO2HT'
          );
          $plans =  $stripe->plans->all(['limit' => 3]);

          foreach($plans as $key => $plan)
          {
            $proname = $stripe->products->retrieve(
                $plan->product,
                []
              );

              $plan->proname = $proname->name;
              $plan->prodescription = $proname->description;
          }
        
        return view('plans.fetchallplans', ['plans' => $plans]);
        //$plans = PlanService::all();
    }

    public function cancelsubscription(Request $request,$subscriptions_id)
    {
        $stripe = new \Stripe\StripeClient(
            'sk_test_51HDQhHJBwKM9SgpoOjcBm7X8hxvSpkKsNBRKfcALuj8BYUzBQOF90thCizw0UoEjWTSsw9Y2D2QswsapkRoXh9ox006QorO2HT'
          );
          $stripe->subscriptions->cancel(
            $subscriptions_id,
            []
          );

          return redirect()->route('plans.fetchusersubscriptions');
        exit;
        $request->user()->subscription('main','sub_Hua30td6HopvoT')->cancel();
    }
}