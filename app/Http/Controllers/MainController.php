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
            'sk_test_51I3yIbKRRxWfz3artfS9X1SBghpFBDEERKAtiz9MNOlhtFpEjg2ZKyNXaEwG5XbmJUGutCPcQR7gD55qu5vJ2NMC00DxTV5Wgq'
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
    
    public function banktransfer(Request $request)
    {
        $stripe = new \Stripe\StripeClient(
            'sk_test_51I3yIbKRRxWfz3artfS9X1SBghpFBDEERKAtiz9MNOlhtFpEjg2ZKyNXaEwG5XbmJUGutCPcQR7gD55qu5vJ2NMC00DxTV5Wgq'
          );

          $stripe->transfers->create([
            'amount' => 1000,
            'currency' => 'usd',
            'destination' => 'acct_1I423d4GUzxy3xFZ',
          ]);
    }

    public function createcharge(Request $request)
    {
        $stripe = new \Stripe\StripeClient(
            'sk_test_51I3yIbKRRxWfz3artfS9X1SBghpFBDEERKAtiz9MNOlhtFpEjg2ZKyNXaEwG5XbmJUGutCPcQR7gD55qu5vJ2NMC00DxTV5Wgq'
          );

          $stripe->charges->create([
            'amount' => 100,
            'currency' => 'usd',
            'source' => 'acct_1I5r8t3uSqxqemli',
            'description' => 'My New Payment',
          ]);
    }

    public function createaccount(Request $request)
    {
        $stripe = new \Stripe\StripeClient(
            'sk_test_51I3yIbKRRxWfz3artfS9X1SBghpFBDEERKAtiz9MNOlhtFpEjg2ZKyNXaEwG5XbmJUGutCPcQR7gD55qu5vJ2NMC00DxTV5Wgq'
          );

        $response = $stripe->accounts->create([
        'type' => 'custom',
        'country' => 'US',
        'email' => 'swatibacancy5@bacancy.com',
        'business_type' => 'individual',
        'capabilities' => [
            'card_payments' => ['requested' => true],
            'transfers' => ['requested' => true],
        ],
        'business_profile' => [
            "mcc" => "5045",
            "support_url" => "http://laravel.com",
            "url" => "http://laravel.com",
        ],
        'individual' => [
          'address' => 
              [
                'line1' => '103 N Main St',
                'postal_code' => '21713',
                'state' => 'MA',
                'country' => 'US',
                'city' =>'Boonsboro'
              ],
          'dob' => 
          [
            'day' => 25,
            'month' => 8,
            'year' => 1995
          ],
          'email' => 'swatibacancy5@bacancy.com',
          'first_name' => 'bacancy final',
          'last_name' => 'Tech',
          'phone' => '2015550123',
          'ssn_last_4' => '0000'
        ],
        'external_account' => [
          'object' => 'bank_account',
          'country' => 'US',
          'currency' => 'USD',
          'account_holder_name' => 'Viraj1',
          'account_holder_type' => 'individual',
          'routing_number' => '110000000',
          'account_number' => '000123456789',
          ],
          'tos_acceptance' => ['date' => time(), 'ip' => $_SERVER['REMOTE_ADDR']],
      ]);
        
        echo '<pre>';
        print_r($response);
        
    }

    public function createexternalaccount(Request $request)
    {
        $stripe = new \Stripe\StripeClient(
            'sk_test_51I3yIbKRRxWfz3artfS9X1SBghpFBDEERKAtiz9MNOlhtFpEjg2ZKyNXaEwG5XbmJUGutCPcQR7gD55qu5vJ2NMC00DxTV5Wgq'
          );

          $response = $stripe->accounts->createExternalAccount(
            'acct_1I4R5s4EAThK6Xqx',
            [
              'external_account' => [
                'object' => 'bank_account',
                'country' => 'US',
                'currency' => 'USD',
                'account_holder_name' => 'Viraj',
                'account_holder_type' => 'individual',
                'routing_number' => '110000000',
                'account_number' => '000123456789',
                ],
            ]
          );
        
        echo '<pre>';
        print_r($response);
        
    }
}