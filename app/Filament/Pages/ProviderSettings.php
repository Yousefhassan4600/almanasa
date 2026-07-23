<?php

namespace App\Filament\Pages;

use App\Enums\AccountType;
use App\Filament\Support\CurrentAccount;
use App\Models\City;
use App\Models\Country;
use App\Models\GradeSubject;
use App\Models\PaymentMethod;
use App\Models\Provider;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\Rule;

class ProviderSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'provider-settings';

    protected string $view = 'filament.pages.provider-settings';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    private ?Provider $providerRecord = null;

    public static function getNavigationLabel(): string
    {
        return __('admin.labels.Settings');
    }

    public static function canAccess(): bool
    {
        $account = CurrentAccount::account();

        if (! $account?->provider_id || (int) $account->owner_user_id !== (int) auth()->id()) {
            return false;
        }

        return in_array($account->type, [
            AccountType::Academy,
            AccountType::StandaloneTeacher,
        ], true);
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $this->fillForm();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->model($this->provider())
            ->operation('edit')
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.labels.Basic Information'))
                    ->schema([
                        FileUpload::make('logo')
                            ->label(__('admin.labels.Logo'))
                            ->image()
                            ->directory('providers/logos'),
                        TextInput::make('name')
                            ->label(__('admin.labels.Name'))
                            ->required(),
                        TextInput::make('slug')
                            ->label(__('admin.labels.Slug'))
                            ->rules([Rule::unique('providers', 'slug')->ignore($this->provider()->id)])
                            ->required(),
                        Select::make('country_id')
                            ->label(__('admin.labels.Country'))
                            ->options(fn (): array => Country::query()->pluck('name', 'id')->all())
                            ->preload()
                            ->searchable(),
                        Select::make('city_id')
                            ->label(__('admin.labels.City'))
                            ->options(fn (): array => City::query()->pluck('name', 'id')->all())
                            ->preload()
                            ->searchable(),
                        Toggle::make('use_custom_domain')
                            ->label(__('admin.labels.Use Custom Domain'))
                            ->live(),
                        TextInput::make('subdomain')
                            ->label(__('admin.labels.Subdomain'))
                            ->rules([Rule::unique('providers', 'subdomain')->ignore($this->provider()->id)])
                            ->visible(fn ($get): bool => ! $get('use_custom_domain'))
                            ->required(fn ($get): bool => ! $get('use_custom_domain'))
                            ->dehydrated(fn ($get): bool => ! $get('use_custom_domain')),
                        TextInput::make('custom_domain')
                            ->label(__('admin.labels.Custom Domain'))
                            ->rules([Rule::unique('providers', 'custom_domain')->ignore($this->provider()->id)])
                            ->visible(fn ($get): bool => (bool) $get('use_custom_domain'))
                            ->required(fn ($get): bool => (bool) $get('use_custom_domain'))
                            ->dehydrated(fn ($get): bool => (bool) $get('use_custom_domain')),
                    ]),
                Section::make(__('admin.labels.Additional Information'))
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label(__('admin.labels.Cover Image'))
                            ->image()
                            ->directory('providers/cover_images'),
                        Textarea::make('bio.ar')
                            ->label(__('admin.labels.Bio (Arabic)'))
                            ->columnSpanFull(),
                        Textarea::make('bio.en')
                            ->label(__('admin.labels.Bio (English)'))
                            ->columnSpanFull(),
                        Textarea::make('address')
                            ->label(__('admin.labels.Address'))
                            ->columnSpanFull(),
                        TextInput::make('latitude')
                            ->label(__('admin.labels.Latitude'))
                            ->numeric(),
                        TextInput::make('longitude')
                            ->label(__('admin.labels.Longitude'))
                            ->numeric(),
                        Toggle::make('pause_website')
                            ->label(__('admin.labels.Pause Website'))
                            ->default(false),
                    ]),
                Section::make(__('admin.labels.Contact Information'))
                    ->schema([
                        TextInput::make('contact_phone')
                            ->label(__('admin.labels.Contact Phone'))
                            ->tel(),
                        TextInput::make('contact_whatsapp')
                            ->label(__('admin.labels.Contact Whatsapp'))
                            ->tel(),
                        TextInput::make('contact_email')
                            ->label(__('admin.labels.Contact Email'))
                            ->email(),
                        TextInput::make('youtube_link')
                            ->label(__('admin.labels.Youtube Link'))
                            ->url(),
                        TextInput::make('facebook_link')
                            ->label(__('admin.labels.Facebook Link'))
                            ->url(),
                        TextInput::make('instagram_link')
                            ->label(__('admin.labels.Instagram Link'))
                            ->url(),
                        TextInput::make('linkedin_link')
                            ->label(__('admin.labels.Linkedin Link'))
                            ->url(),
                        TextInput::make('x_link')
                            ->label(__('admin.labels.X Link'))
                            ->url(),
                        TextInput::make('snapchat_link')
                            ->label(__('admin.labels.Snapchat Link'))
                            ->url(),
                    ])
                    ->columns(1),
                Section::make(__('admin.labels.Settings'))
                    ->schema([
                        ColorPicker::make('primary_color')
                            ->label(__('admin.labels.Primary Color')),
                        ColorPicker::make('secondary_color')
                            ->label(__('admin.labels.Secondary Color')),
                        TextInput::make('completion_watch_percentage')
                            ->label(__('admin.labels.Completion Watch Percentage'))
                            ->numeric()
                            ->default(70)
                            ->suffix('%')
                            ->minValue(1)
                            ->maxValue(100),
                        RichEditor::make('terms_conditions')
                            ->label(__('admin.labels.Terms Conditions'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->columns(2),
                Section::make(__('admin.labels.Subjects'))
                    ->schema([
                        Select::make('grade_subject_ids')
                            ->label(__('admin.labels.Grade Subjects'))
                            ->multiple()
                            ->options(fn (): array => $this->gradeSubjectOptions())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('admin.resources.PaymentMethod.plural'))
                    ->schema([
                        Repeater::make('providerPaymentMethods')
                            ->label(__('admin.resources.PaymentMethod.plural'))
                            ->relationship()
                            ->schema([
                                Select::make('payment_method_id')
                                    ->label(__('admin.labels.Payment Method'))
                                    ->options(fn (): array => PaymentMethod::query()
                                        ->where('is_active', true)
                                        ->orderBy('sort_order')
                                        ->get()
                                        ->mapWithKeys(fn (PaymentMethod $paymentMethod): array => [
                                            $paymentMethod->id => $paymentMethod->name,
                                        ])
                                        ->all())
                                    ->preload()
                                    ->searchable()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('account_number')
                                    ->label(__('admin.labels.Account Number')),
                                TextInput::make('account_holder')
                                    ->label(__('admin.labels.Account Holder')),
                                TextInput::make('phone_number')
                                    ->label(__('admin.labels.Phone Number'))
                                    ->tel(),
                                TextInput::make('phone_holder')
                                    ->label(__('admin.labels.Phone Holder')),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel(__('admin.Create'))
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => filled($state['payment_method_id'] ?? null)
                                ? PaymentMethod::query()->find($state['payment_method_id'])?->name
                                : __('admin.labels.Payment Method'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('admin.labels.Banners'))
                    ->schema([
                        Repeater::make('banners')
                            ->label(__('admin.labels.Banners'))
                            ->relationship()
                            ->schema([
                                FileUpload::make('cover')
                                    ->label(__('admin.labels.Cover'))
                                    ->image()
                                    ->directory('banners')
                                    ->columnSpanFull()
                                    ->required(),
                                TextInput::make('title.ar')
                                    ->label(__('admin.labels.Title (Arabic)'))
                                    ->required(),
                                TextInput::make('title.en')
                                    ->label(__('admin.labels.Title (English)'))
                                    ->required(),
                                TextInput::make('subtitle.ar')
                                    ->label(__('admin.labels.Subtitle (Arabic)')),
                                TextInput::make('subtitle.en')
                                    ->label(__('admin.labels.Subtitle (English)')),
                                TextInput::make('url')
                                    ->label(__('admin.labels.URL'))
                                    ->url()
                                    ->columnSpanFull(),
                                Toggle::make('is_active')
                                    ->label(__('admin.labels.Is Active'))
                                    ->default(true),
                            ])
                            ->orderColumn('sort_order')
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel(__('admin.Create'))
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'][app()->getLocale()]
                                ?? $state['title'][config('app.fallback_locale')]
                                ?? __('admin.labels.Banners'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label(__('admin.actions.Save Changes'))
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ])
                            ->alignment(Alignment::Start)
                            ->key('form-actions'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $gradeSubjectIds = $data['grade_subject_ids'] ?? [];

        unset($data['grade_subject_ids'], $data['providerPaymentMethods'], $data['banners']);

        $provider = $this->provider();
        $data['type'] = $provider->type;
        $data['owner_user_id'] = $provider->owner_user_id;
        $data['subject_id'] = null;

        $provider->update($data);
        $this->form->model($provider)->saveRelationships();
        $provider->syncGradeSubjects($gradeSubjectIds);

        Notification::make()
            ->title(__('admin.messages.provider_settings_saved'))
            ->success()
            ->send();
    }

    protected function fillForm(): void
    {
        $provider = $this->provider();
        $data = $provider->attributesToArray();
        $data['grade_subject_ids'] = $provider->accountSubjects()
            ->where('is_active', true)
            ->pluck('grade_subject_id')
            ->all();

        $this->form->fill($data);
    }

    protected function provider(): Provider
    {
        if ($this->providerRecord) {
            return $this->providerRecord;
        }

        $providerId = CurrentAccount::providerId();

        abort_unless($providerId, 403);

        return $this->providerRecord = Provider::query()->findOrFail($providerId);
    }

    /**
     * @return array<int, string>
     */
    protected function gradeSubjectOptions(): array
    {
        return GradeSubject::query()
            ->with(['grade.educationStage', 'track', 'subject'])
            ->get()
            ->mapWithKeys(fn (GradeSubject $gradeSubject): array => [
                $gradeSubject->id => $gradeSubject->full_name,
            ])
            ->all();
    }
}
