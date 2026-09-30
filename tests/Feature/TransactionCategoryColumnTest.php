<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionCategoryColumnTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_transaction_table_renders_kategori_header_and_data(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $this->assertNotNull($bendaharaUser);

        // Check index page contains Kategori header
        $response = $this->actingAs($bendaharaUser)->get(route('bendahara.transactions.index'));
        $response->assertStatus(200);
        $response->assertSee('Kategori');

        // Check AJAX DataTables endpoint returns category_badge
        $ajaxResponse = $this->actingAs($bendaharaUser)->getJson(route('bendahara.transactions.index'), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure([
            'data' => [
                '*' => ['category_badge']
            ]
        ]);
    }
}
