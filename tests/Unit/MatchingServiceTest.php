<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\Property;
use App\Services\MatchingService;
use Tests\TestCase;

class MatchingServiceTest extends TestCase
{
    public function test_matching_weights_are_centralized_and_total_one_hundred(): void
    {
        $this->assertSame(100, array_sum(config('matching.weights')));
    }

    public function test_exact_match_is_deterministically_one_hundred_percent(): void
    {
        $service = new MatchingService;
        $property = $this->property();
        $client = $this->client();

        $first = $service->score($property, $client);
        $second = $service->score($property, $client);

        $this->assertSame(1.0, $first['score']);
        $this->assertSame(100, $first['percentage']);
        $this->assertSame($first, $second);
    }

    public function test_budget_boundary_is_full_and_over_budget_is_reduced_by_ratio(): void
    {
        $service = new MatchingService;
        $client = $this->client(['budget' => 3_500_000]);

        $this->assertSame(1.0, $service->score($this->property(['price' => 3_500_000]), $client)['breakdown']['price']);
        $this->assertSame(0.5, $service->score($this->property(['price' => 7_000_000]), $client)['breakdown']['price']);
    }

    public function test_location_transaction_and_bedroom_mismatch_are_scored_without_crashing(): void
    {
        $service = new MatchingService;
        $property = $this->property(['location' => 'อารีย์', 'transaction_type' => 'rent', 'bedrooms' => 1]);
        $client = $this->client(['locations' => 'สุขุมวิท', 'transaction_type' => 'buy', 'bedrooms' => 3]);

        $result = $service->score($property, $client);

        $this->assertSame(0.0, $result['breakdown']['location']);
        $this->assertSame(0.0, $result['breakdown']['transaction_type']);
        $this->assertSame(1 / 3, $result['breakdown']['bedrooms']);
    }

    public function test_missing_optional_requirements_are_neutral_not_mismatches(): void
    {
        $result = (new MatchingService)->score($this->property(), $this->client());

        $this->assertSame(1.0, $result['breakdown']['size']);
        $this->assertSame(1.0, $result['breakdown']['transit']);
        $this->assertSame(1.0, $result['breakdown']['other']);
    }

    public function test_property_size_matches_client_minimum_size(): void
    {
        $service = new MatchingService;
        $client = $this->client(['minimum_size' => 40]);

        $meetsSize = $service->score($this->property(['size' => 45]), $client);
        $this->assertSame(1.0, $meetsSize['breakdown']['size']);

        $underSize = $service->score($this->property(['size' => 20]), $client);
        $this->assertSame(0.5, $underSize['breakdown']['size']);
    }

    /** @param array<string, mixed> $overrides */
    private function property(array $overrides = []): Property
    {
        return new Property(array_merge([
            'id' => 1,
            'transaction_type' => 'sale',
            'price' => 3_500_000,
            'bedrooms' => 2,
            'location' => 'สุขุมวิท',
        ], $overrides));
    }

    /** @param array<string, mixed> $overrides */
    private function client(array $overrides = []): Client
    {
        return new Client(array_merge([
            'transaction_type' => 'buy',
            'budget' => 3_500_000,
            'locations' => 'สุขุมวิท, อโศก',
            'bedrooms' => null,
            'minimum_size' => null,
            'transit_preference' => null,
            'notes' => null,
        ], $overrides));
    }
}
