<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\ComplaintNote;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintNoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ComplaintNote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'complaint_id' => Complaint::inRandomOrder()->firstOrCreate(Complaint::factory()->make()->toArray())->id,
            'notes' => $this->faker->paragraph,
        ];
    }
}
