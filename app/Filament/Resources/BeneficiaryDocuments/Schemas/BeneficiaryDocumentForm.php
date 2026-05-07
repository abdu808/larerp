<?php

namespace App\Filament\Resources\BeneficiaryDocuments\Schemas;

use App\Models\AssistanceRequest;
use App\Models\Beneficiary;
use App\Models\BeneficiaryDocument;
use App\Models\SocialCase;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BeneficiaryDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('العنوان')->required()->maxLength(255),
                TextInput::make('file_path')->label('مسار الملف')->required()->maxLength(255),
                TextInput::make('mime_type')->label('نوع الملف')->maxLength(255),
                TextInput::make('size')->label('الحجم')->numeric()->minValue(0),
                Select::make('document_type')->label('نوع الوثيقة')->required()->options(BeneficiaryDocument::DOCUMENT_TYPE_OPTIONS)->searchable(),
                Select::make('sensitivity_level')->label('مستوى السرية')->required()->default('internal')->options(BeneficiaryDocument::SENSITIVITY_LEVEL_OPTIONS),
                Select::make('verification_status')->label('حالة التحقق')->required()->default(BeneficiaryDocument::STATUS_UPLOADED)->options(BeneficiaryDocument::VERIFICATION_STATUS_OPTIONS),
                Select::make('beneficiary_id')
                    ->label('المستفيد')
                    ->relationship('beneficiary', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Beneficiary $record): string => $record->full_name)
                    ->searchable(['first_name', 'father_name', 'grandfather_name', 'family_name', 'national_id'])
                    ->preload(),
                Select::make('social_case_id')
                    ->label('الحالة الاجتماعية')
                    ->relationship('socialCase', 'case_number')
                    ->getOptionLabelFromRecordUsing(fn (SocialCase $record): string => "{$record->case_number} - {$record->summary}")
                    ->searchable(['case_number', 'summary'])
                    ->preload(),
                Select::make('assistance_request_id')
                    ->label('طلب الخدمة')
                    ->relationship('assistanceRequest', 'request_number')
                    ->getOptionLabelFromRecordUsing(fn (AssistanceRequest $record): string => "{$record->request_number} - {$record->description}")
                    ->searchable(['request_number', 'description'])
                    ->preload(),
                Select::make('uploaded_by_id')->label('رفع بواسطة')->relationship('uploadedBy', 'name')->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name)->searchable(['name', 'email'])->preload(),
                Select::make('verified_by_id')->label('تحقق بواسطة')->relationship('verifiedBy', 'name')->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name)->searchable(['name', 'email'])->preload(),
                DatePicker::make('issued_on')->label('تاريخ الإصدار'),
                DatePicker::make('expires_on')->label('تاريخ الانتهاء'),
                DateTimePicker::make('verified_at')->label('تاريخ التحقق'),
                DateTimePicker::make('used_in_decision_at')->label('استخدمت في قرار'),
                Textarea::make('rejection_reason')->label('سبب الرفض')->columnSpanFull(),
                Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
            ]);
    }
}
