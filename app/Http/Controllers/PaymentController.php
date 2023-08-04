<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function index()
    {
      return view('welcome');
    }

    public function customerDetails(Request $request) 
    {
      try {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $customer = \Stripe\Customer::create([
          "name" => $request->input('accountholder-name'),
          "email" => $request->input('email')
        ]);
        
        $setup_intent = \Stripe\SetupIntent::create([
          'payment_method_types' => ['sepa_debit'],
          'customer' => $customer->id,
        ]);

        return response()->json(['clientSecret' => $setup_intent->client_secret]);
      } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
      }
    }
}
