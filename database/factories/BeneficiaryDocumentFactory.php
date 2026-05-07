<?php

namespace Database\Factories;

use App\Models\BeneficiaryDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BeneficiaryDocument>
 */
class BeneficiaryDocumentFactory extends Factory
{
    protected $model = BeneficiaryDocument::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'file_path' => 'beneficiary-documents/'.$this->faker->uuid.'.pdf',
            'mime_type' => 'application/pdf',
            'size' => $this->faker->numberBetween(1024, 4096),
            'document_type' => $this->faker->randomElement(array_keys(BeneficiaryDocument::DOCUMENT_TYPE_OPTIONS)),
            'sensitivity_level' => 'internal',
            'verification_status' => BeneficiaryDocument::STATUS_UPLOADED,
            'issued_on' => now()->subMonth()->toDateString(),
            'expires_on' => now()->addYear()->toDateString(),
        ];
    }
}
