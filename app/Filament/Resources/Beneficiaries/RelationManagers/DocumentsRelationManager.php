<?php

namespace App\Filament\Resources\Beneficiaries\RelationManagers;

use App\Models\BeneficiaryDocument;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'الوثائق';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('العنوان')->searchable()->sortable(),
                TextColumn::make('document_type')->label('نوع الوثيقة')->badge()->formatStateUsing(fn (?string $state): string => BeneficiaryDocument::DOCUMENT_TYPE_OPTIONS[$state] ?? (string) $state),
                TextColumn::make('verification_status')
                    ->label('التحقق')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => BeneficiaryDocument::verificationStatusLabelFor($state))
                    ->color(fn (?string $state): string => BeneficiaryDocument::verificationStatusColorFor($state)),
                TextColumn::make('expires_on')->label('ينتهي في')->date()->sortable(),
                IconColumn::make('used_in_decision_at')->label('مستخدم في قرار')->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('إضافة وثيقة')
                    ->mutateDataUsing(function (array $data): array {
                        $data['beneficiary_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    })
                    ->schema([
                        TextInput::make('title')->label('العنوان')->required()->maxLength(255),
                        TextInput::make('file_path')->label('مسار الملف')->required()->maxLength(255),
                        TextInput::make('mime_type')->label('نوع الملف')->maxLength(255),
                        TextInput::make('size')->label('الحجم')->numeric()->minValue(0),
                        Select::make('document_type')->label('نوع الوثيقة')->required()->options(BeneficiaryDocument::DOCUMENT_TYPE_OPTIONS)->searchable(),
                        Select::make('sensitivity_level')->label('مستوى السرية')->required()->default('internal')->options(BeneficiaryDocument::SENSITIVITY_LEVEL_OPTIONS),
                        Select::make('verification_status')->label('حالة التحقق')->required()->default(BeneficiaryDocument::STATUS_UPLOADED)->options(BeneficiaryDocument::VERIFICATION_STATUS_OPTIONS),
                        DatePicker::make('issued_on')->label('تاريخ الإصدار'),
                        DatePicker::make('expires_on')->label('تاريخ الانتهاء'),
                        Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
