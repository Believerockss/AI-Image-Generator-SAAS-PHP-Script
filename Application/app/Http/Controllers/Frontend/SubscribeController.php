<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Str;

class SubscribeController extends Controller
{
    public function subscribe(Request $request, $id, $type)
    {
        $plan = Plan::findOrFail($id);
        $user = userAuthInfo();
        abort_if($user->isSubscribed() && $user->subscription->isCancelled(), 401);
        switch ($type) {
            case 'subscribe':
                abort_if($user->isSubscribed(), 401);
                $type = 1;
                break;
            case "renew":
                if ($user->isSubscribed()) {
                    $subscription = $user->subscription;
                    abort_if($subscription->isFree() || $subscription->plan->id != $plan->id, 401);
                    if ($subscription->isAboutToExpire() || $subscription->isExpired()) {
                        $type = 2;
                    } else {
                        abort(401);
                    }
                } else {
                    abort(401);
                }
                break;
            case "upgrade":
                abort_if(!$user->isSubscribed() || $user->subscription->plan_id == $plan->id, 401);
                $type = 3;
                break;
            case "downgrade":
                abort_if(!$user->isSubscribed() || $user->subscription->plan_id == $plan->id || $plan->price > $user->subscription->plan->price && $plan->interval > $user->subscription->plan->interval, 401);
                $type = 4;
                break;
            default:
                return abort(404);
                break;
        }
        $checkoutId = sha1(Str::random(40) . time());
        $tax = ($plan->price * countryTax($user->address->country ?? ipInfo()->location->country)) / 100;
        $total = ($plan->price + $tax);
        $detailsBeforeDiscount = ['price' => priceFormat($plan->price), 'tax' => priceFormat($tax), 'total' => priceFormat($total)];
        $transaction = Transaction::create([
            'checkout_id' => $checkoutId,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'details_before_discount' => $detailsBeforeDiscount,
            'price' => priceFormat($plan->price),
            'tax' => $tax,
            'total' => $total,
            'type' => $type,
        ]);
        if ($transaction) {
            return redirect()->route('checkout.index', $transaction->checkout_id);
        }
    }
}
