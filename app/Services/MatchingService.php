<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Property;
use Illuminate\Support\Collection;

class MatchingService
{
    /**
     * @return Collection<int, array{property: Property, score: float, percentage: int, breakdown: array<string, float>}
     */
    public function forClient(Client $client): Collection
    {
        return Property::query()
            ->where('user_id', $client->user_id)
            ->get()
            ->map(fn (Property $property): array => $this->result($property, $client))
            ->sort(function (array $first, array $second): int {
                return ($second['score'] <=> $first['score'])
                    ?: ($first['property']->id <=> $second['property']->id);
            })
            ->values();
    }

    /**
     * @return Collection<int, array{client: Client, score: float, percentage: int, breakdown: array<string, float>}
     */
    public function forProperty(Property $property): Collection
    {
        return Client::query()
            ->where('user_id', $property->user_id)
            ->get()
            ->map(fn (Client $client): array => [
                'client' => $client,
                ...$this->score($property, $client),
            ])
            ->sort(function (array $first, array $second): int {
                return ($second['score'] <=> $first['score'])
                    ?: ($first['client']->id <=> $second['client']->id);
            })
            ->values();
    }

    /**
     * @return array{score: float, percentage: int, breakdown: array<string, float>}
     */
    public function score(Property $property, Client $client): array
    {
        $breakdown = [
            'price' => $this->priceScore($property, $client),
            'location' => $this->locationScore($property->location, $client->locations),
            'transaction_type' => $property->transaction_type === $this->transactionTypeForProperty($client) ? 1.0 : 0.0,
            'bedrooms' => $this->bedroomScore($property, $client),
            'size' => $this->optionalNumericScore($property->getAttribute('size'), $client->minimum_size),
            'transit' => $this->optionalTextScore($property->getAttribute('transit_preference'), $client->transit_preference),
            'other' => $this->optionalTextScore($property->getAttribute('notes'), $client->notes),
        ];
        $weights = config('matching.weights', []);
        $weightTotal = max(1, array_sum($weights));
        $weightedScore = 0.0;

        foreach ($weights as $key => $weight) {
            $weightedScore += ($breakdown[$key] ?? 1.0) * (float) $weight;
        }

        $score = round($weightedScore / $weightTotal, 6);

        return [
            'score' => $score,
            'percentage' => (int) round($score * 100),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * @return array{score: float, percentage: int, breakdown: array<string, float>}
     */
    private function result(Property $property, Client $client): array
    {
        return [
            'property' => $property,
            ...$this->score($property, $client),
        ];
    }

    private function priceScore(Property $property, Client $client): float
    {
        $price = (float) $property->price;
        $budget = (float) $client->budget;

        if ($price <= $budget) {
            return 1.0;
        }

        if ($price <= 0 || $budget <= 0) {
            return 0.0;
        }

        return match (config('matching.price.over_budget_score')) {
            'budget_to_price_ratio' => max(0.0, min(1.0, $budget / $price)),
            default => 0.0,
        };
    }

    private function transactionTypeForProperty(Client $client): string
    {
        return $client->transaction_type === 'buy' ? 'sale' : 'rent';
    }

    private function bedroomScore(Property $property, Client $client): float
    {
        if ($client->bedrooms === null || (int) $client->bedrooms <= 0) {
            return 1.0;
        }

        $requested = (int) $client->bedrooms;
        $available = (int) $property->bedrooms;

        if ($available >= $requested) {
            return 1.0;
        }

        return max(0.0, min(1.0, $available / $requested));
    }

    private function locationScore(?string $propertyLocation, ?string $clientLocations): float
    {
        $propertyTokens = $this->tokens($propertyLocation);
        $clientTokens = $this->tokens($clientLocations);

        if ($propertyTokens === [] || $clientTokens === []) {
            return 1.0;
        }

        foreach ($clientTokens as $clientToken) {
            foreach ($propertyTokens as $propertyToken) {
                if (str_contains($propertyToken, $clientToken) || str_contains($clientToken, $propertyToken)) {
                    return 1.0;
                }
            }
        }

        return 0.0;
    }

    private function optionalNumericScore(mixed $propertyValue, mixed $clientMinimum): float
    {
        if ($propertyValue === null || $clientMinimum === null || (float) $clientMinimum <= 0) {
            return 1.0;
        }

        $propertyValue = (float) $propertyValue;
        $clientMinimum = (float) $clientMinimum;

        return $propertyValue >= $clientMinimum
            ? 1.0
            : max(0.0, min(1.0, $propertyValue / $clientMinimum));
    }

    private function optionalTextScore(mixed $propertyValue, mixed $clientValue): float
    {
        if (! filled($propertyValue) || ! filled($clientValue)) {
            return 1.0;
        }

        return $this->locationScore((string) $propertyValue, (string) $clientValue);
    }

    /** @return list<string> */
    private function tokens(?string $value): array
    {
        return collect(preg_split('/[,|\/\n]+/u', mb_strtolower((string) $value)) ?: [])
            ->map(fn (string $token): string => trim($token))
            ->filter()
            ->values()
            ->all();
    }
}
