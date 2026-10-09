<?php

namespace App\Http\Controllers;

use App\Models\PaymentGatewayConfig;
use App\Models\Subscription;
use App\Services\EnrolmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private const PAYSTACK_API = 'https://api.paystack.co';

    public function __construct(private EnrolmentService $enrolment)
    {
    }

    /**
     * Initiates a Paystack payment and redirects to the authorization URL.
     */
    public function redirectToPaystack(Request $request)
    {
        $user = Auth::user();
        $subscription = $this->payableSubscription($user->id);

        if (! $subscription) {
            return redirect('/dashboard')->with('info', 'There is nothing left to pay on your account.');
        }

        $secretKey = $this->paystackSecret();
        if (! $secretKey) {
            return back()->with('error', 'Online payment is not available at the moment. Please contact the school.');
        }

        try {
            $response = Http::withToken($secretKey)->acceptJson()->post(self::PAYSTACK_API . '/transaction/initialize', [
                'email' => $user->email,
                'amount' => $subscription->amountInKobo(),
                'currency' => 'NGN',
                'reference' => 'BRG-' . $subscription->id . '-' . Str::upper(Str::random(10)),
                'callback_url' => route('payment.callback'),
                'metadata' => [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                ],
            ]);

            $url = $response->json('data.authorization_url');

            if (! $response->successful() || ! $url) {
                throw new \RuntimeException('Paystack initialize failed: ' . $response->body());
            }

            return redirect()->away($url);
        } catch (\Throwable $e) {
            Log::error('Paystack initialization error: ' . $e->getMessage());

            return back()->with('error', 'We could not start the payment. Please try again.');
        }
    }

    /**
     * Paystack redirects the payer here after paying.
     */
    public function handleGatewayCallback(Request $request)
    {
        $reference = (string) $request->query('reference');

        if ($reference === '') {
            return redirect()->route('application')->with('error', 'No payment reference was provided.');
        }

        $result = $this->verifyAndComplete($reference, Auth::id());

        if ($result === true) {
            return redirect('/dashboard')->with('success', 'Payment successful! Welcome to BigRig — your courses and instructors are now available.');
        }

        return redirect()->route('application')->with('error', $result);
    }

    /**
     * Server-to-server notification from Paystack, so payments are recorded even
     * if the payer never returns to the site.
     */
    public function webhook(Request $request)
    {
        $secretKey = $this->paystackSecret();
        $signature = (string) $request->header('x-paystack-signature');

        if (! $secretKey || ! hash_equals(hash_hmac('sha512', $request->getContent(), $secretKey), $signature)) {
            return response('Invalid signature', 401);
        }

        if ($request->input('event') === 'charge.success') {
            $this->verifyAndComplete((string) $request->input('data.reference'));
        }

        return response('OK');
    }

    /**
     * Verify a transaction with Paystack and enrol the student when it checks out.
     *
     * @return true|string true on success, otherwise a message for the payer
     */
    private function verifyAndComplete(string $reference, ?int $expectedUserId = null): bool|string
    {
        $secretKey = $this->paystackSecret();
        if (! $secretKey) {
            return 'Payment verification is not available at the moment. Please contact the school.';
        }

        try {
            $data = Http::withToken($secretKey)->acceptJson()
                ->get(self::PAYSTACK_API . '/transaction/verify/' . rawurlencode($reference))
                ->json('data');
        } catch (\Throwable $e) {
            Log::error("Paystack verification error for {$reference}: " . $e->getMessage());

            return 'We could not confirm your payment yet. If you were charged, it will be confirmed shortly — otherwise please try again.';
        }

        if (($data['status'] ?? null) !== 'success') {
            return 'The payment was not completed. Please try again.';
        }

        $subscription = Subscription::find($data['metadata']['subscription_id'] ?? null);

        $problem = match (true) {
            ! $subscription => 'subscription not found',
            $expectedUserId !== null && $subscription->user_id !== $expectedUserId => 'subscription belongs to another user',
            (int) ($data['metadata']['user_id'] ?? 0) !== $subscription->user_id => 'metadata user mismatch',
            strtoupper($data['currency'] ?? '') !== 'NGN' => 'unexpected currency',
            (int) ($data['amount'] ?? 0) < $subscription->amountInKobo() => 'amount paid is less than amount due',
            Subscription::where('payment_reference', $reference)->where('id', '!=', $subscription->id)->exists() => 'reference already used',
            default => null,
        };

        if ($problem) {
            Log::warning("Rejected Paystack payment {$reference}: {$problem}.", ['subscription_id' => $subscription?->id]);

            return 'We could not match this payment to your application. Please contact the school with your payment reference: ' . $reference;
        }

        if (! $subscription->isPaid()) {
            $this->enrolment->completePayment($subscription, $reference, 'Paystack');
        }

        return true;
    }

    private function payableSubscription(int $userId): ?Subscription
    {
        return Subscription::where('user_id', $userId)
            ->whereIn('payment_status', ['pending', 'failed'])
            ->latest()
            ->first();
    }

    private function paystackSecret(): ?string
    {
        $config = PaymentGatewayConfig::where('name', 'Paystack')->first();

        try {
            return $config?->secret_key ? Crypt::decryptString($config->secret_key) : null;
        } catch (\Throwable $e) {
            Log::error('Paystack secret key could not be decrypted: ' . $e->getMessage());

            return null;
        }
    }
}
