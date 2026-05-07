<?php

namespace App\Models;

use Database\Factories\DonationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Donation extends Model
{
    /** @use HasFactory<DonationFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_REFUNDED = 'refunded';

    public const PAYMENT_STATUSES = [
        self::STATUS_PENDING => 'قيد الانتظار',
        self::STATUS_PAID => 'مدفوع',
        self::STATUS_FAILED => 'فشل',
        self::STATUS_REFUNDED => 'مسترجع',
    ];

    public const PAYMENT_METHODS = [
        'cash' => 'نقدي',
        'bank_transfer' => 'تحويل بنكي',
        'pos' => 'نقاط بيع',
        'manual' => 'إدخال يدوي',
    ];

    protected $fillable = [
        'project_id',
        'campaign_id',
        'donor_name',
        'donor_email',
        'donor_phone',
        'amount',
        'currency',
        'payment_status',
        'payment_method',
        'reference',
        'donated_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'donated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Donation $donation): void {
            $errors = [];
            $donation->currency ??= 'SAR';
            $donation->payment_status ??= self::STATUS_PENDING;

            if ((float) $donation->amount <= 0) {
                $errors['amount'][] = 'The donation amount must be greater than zero.';
            }

            if (! preg_match('/^[A-Z]{3}$/', (string) $donation->currency)) {
                $errors['currency'][] = 'The donation currency must be a three-letter ISO code.';
            }

            if (! array_key_exists((string) $donation->payment_status, self::PAYMENT_STATUSES)) {
                $errors['payment_status'][] = 'The selected payment status is invalid.';
            }

            if ($donation->payment_method && ! array_key_exists((string) $donation->payment_method, self::PAYMENT_METHODS)) {
                $errors['payment_method'][] = 'The selected payment method is invalid.';
            }

            if ($donation->campaign_id) {
                $campaign = Campaign::query()->find($donation->campaign_id);

                if ($campaign && ! $donation->project_id) {
                    $donation->project_id = $campaign->project_id;
                }

                if ($campaign && $donation->project_id && (int) $donation->project_id !== (int) $campaign->project_id) {
                    $errors['campaign_id'][] = 'The donation campaign must belong to the selected project.';
                }
            }

            if ($errors !== []) {
                throw ValidationException::withMessages($errors);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
