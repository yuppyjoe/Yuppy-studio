<?php

namespace App\Livewire\Forms;

use App\Models\BrandingProfile;
use App\Services\AuditLogger;
use App\Services\LogoStorage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

/**
 * Branding fields shared by the photographer studio page and the admin
 * Yuppy-branding page. Authorization is done by the owning component.
 */
class BrandingForm extends Form
{
    public string $brand_name = '';

    public string $tagline = '';

    public string $email = '';

    public string $phone = '';

    public string $website = '';

    /** @var array<string, string> */
    public array $social_links = [];

    /** @var TemporaryUploadedFile|null */
    public $logo = null;

    public function fillFrom(BrandingProfile $branding): void
    {
        $this->brand_name = (string) $branding->brand_name;
        $this->tagline = (string) $branding->tagline;
        $this->email = (string) $branding->email;
        $this->phone = (string) $branding->phone;
        $this->website = (string) $branding->website;
        $saved = $branding->social_links ?? [];
        $this->social_links = [];
        foreach (array_keys(BrandingProfile::SOCIAL_NETWORKS) as $network) {
            $this->social_links[$network] = (string) ($saved[$network] ?? '');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'brand_name' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+()\-\s.]{6,40}$/'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'social_links' => ['array'],
            'social_links.*' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', ...LogoStorage::rules()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        return [
            'brand_name' => 'brand name',
            'social_links.*' => 'social link',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid phone number (digits, spaces, +, - and brackets).',
            'logo.image' => 'The logo must be a PNG, JPG or WebP image. SVG files are not accepted.',
            'logo.mimes' => 'The logo must be a PNG, JPG or WebP image. SVG files are not accepted.',
            'logo.mimetypes' => 'The logo must be a PNG, JPG or WebP image. SVG files are not accepted.',
            'logo.max' => 'The logo may not be larger than '.(LogoStorage::MAX_KILOBYTES / 1024).' MB.',
        ];
    }

    public function save(BrandingProfile $branding, string $event): void
    {
        $this->validate();

        $branding->fill([
            'brand_name' => $this->brand_name,
            'tagline' => $this->tagline ?: null,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website ?: null,
            'social_links' => array_filter(
                array_intersect_key($this->social_links, BrandingProfile::SOCIAL_NETWORKS),
                fn ($value) => filled($value),
            ) ?: null,
        ]);

        $branding->save();

        $audit = app(AuditLogger::class);
        $audit->logChanges($event, $branding);

        if ($this->logo !== null) {
            $previous = $branding->logo_path;
            app(LogoStorage::class)->store($branding, $this->logo);
            $audit->log('branding_profile.logo_updated', $branding, ['logo_path' => $previous], ['logo_path' => $branding->logo_path]);
            $this->logo = null;
        }
    }
}
