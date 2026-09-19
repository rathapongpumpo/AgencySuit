<?php

namespace App\Services;

use Illuminate\Http\Request;

class PostHogAnalytics
{
    /** @var list<string> */
    public const EVENTS = [
        'signup_completed',
        'login_completed',
        'first_property_created',
        'first_client_created',
        'match_viewed',
        'followup_created',
        'appointment_created',
        'deal_created',
        'deal_closed',
        'free_limit_reached',
        'upgrade_viewed',
        'checkout_started',
        'purchase_completed',
        'feedback_submitted',
    ];

    public function track(Request $request, string $event): void
    {
        if (! config('posthog.enabled')) {
            return;
        }

        if (! in_array($event, self::EVENTS, true)) {
            throw new \InvalidArgumentException("Unsupported PostHog event [{$event}].");
        }

        $events = (array) $request->attributes->get('posthog_events', []);

        if (in_array($event, $events, true)) {
            return;
        }

        $events[] = $event;
        $request->attributes->set('posthog_events', $events);
        $request->session()->flash('posthog_events', $events);
    }

    /** @return list<string> */
    public function events(Request $request): array
    {
        $events = $request->attributes->get('posthog_events');
        if ($events === null) {
            $events = $request->session()->get('posthog_events', []);
        }

        return array_values(array_filter(
            (array) $events,
            fn (mixed $event): bool => is_string($event) && in_array($event, self::EVENTS, true),
        ));
    }

    /** @return list<string> */
    public function pullEvents(Request $request): array
    {
        $events = $this->events($request);
        $request->attributes->set('posthog_events', []);
        $request->session()->forget('posthog_events');

        return $events;
    }

    public function checkoutStarted(Request $request): void
    {
        $this->track($request, 'checkout_started');
    }

    public function verifiedPurchaseCompleted(Request $request): void
    {
        $this->track($request, 'purchase_completed');
    }
}
