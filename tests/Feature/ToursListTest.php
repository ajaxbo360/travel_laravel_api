<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToursListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_tour_list_by_travel_slug_returns_correct_tours(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');


        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }
    public function test_tour_price_is_shown_correctly(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 123.23]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');


        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }
    public function test_tours_list_returns_paginated_data_correctly(): void
    {
        $travel = Travel::factory()->create();
        Tour::factory(16)->create(['travel_id' => $travel->id]);

        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        $response->assertStatus(200);

        $response->assertJsonCount(15, 'data');

        $response->assertJsonPath('meta.last_page', 2);
    }

    // write tours list test for filter and order by price

    public function test_tours_list_returns_filter_by_priceFrom_correctly(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 123.23]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours?priceFrom=10');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }
    public function test_tours_list_returns_filter_by_priceTo_correctly(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 123.23]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours?priceTo=200');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }


    public function test_tours_list_returns_filter_by_dateFrom_correctly(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id, 'starting_date' => now()]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours?dateFrom=2022-01-01');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }
    // public function test_tours_list_returns_filter_by_dateTo_correctly(): void
    // {
    //     $travel = Travel::factory()->create();
    //     $tour = Tour::factory()->create(['travel_id' => $travel->id, 'starting_date' => now()->addDays(10)]);
    //     $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours?dateTo=2022-01-20');

    //     $response->assertStatus(200);

    //     $response->assertJsonCount(0, 'data');
    //     $response->assertJsonFragment(['id' => $tour->id]);
    // }

    public function test_tours_list_returns_order_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 123.23]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours?sortBy=price&sortOrder=desc');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }

    // write test for error like wrong sortOrder

    public function test_tours_list_returns_error_if_wrong_sortOrder(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 123.23]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours?sortOrder=wrong');


        $response->assertStatus(302);

        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'sortOrder' => ['sortOrder must be "asc" or "desc"'],
            ],
        ]);
    }
}
