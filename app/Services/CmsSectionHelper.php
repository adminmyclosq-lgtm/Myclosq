<?php

namespace App\Services;

class CmsSectionHelper
{
    public static function parse($section): array
    {
        $content = is_string($section?->content) ? json_decode($section->content, true) : ($section?->content ?? []);
        if (!is_array($content)) {
            $content = [];
        }
        $settings = $content['settings'] ?? [];

        // 1. Spacing (Padding)
        $topSpacing = $settings['top_spacing'] ?? '80px';
        $bottomSpacing = $settings['bottom_spacing'] ?? '80px';
        $leftSpacing = $settings['left_spacing'] ?? '0px';
        $rightSpacing = $settings['right_spacing'] ?? '0px';

        // 2. Background Color Resolution (custom hex, swatches, presets, legacy tone)
        $rawBg = !empty($settings['bg_custom_color'])
            ? $settings['bg_custom_color']
            : (!empty($settings['bg_color']) ? $settings['bg_color'] : ($settings['bg_tone'] ?? ''));

        $effectiveBgColor = null;
        if (!empty($rawBg)) {
            if (str_starts_with($rawBg, '#') || str_starts_with($rawBg, 'rgb')) {
                $effectiveBgColor = $rawBg;
            } else {
                $effectiveBgColor = match ($rawBg) {
                    'bg-white', 'white' => '#FFFFFF',
                    'bg-[var(--cream)]', 'cream' => '#F5F1E8',
                    'bg-[var(--sand)]', 'sand' => '#E5DDCD',
                    'bg-[var(--ink)] text-white', 'bg-[var(--ink)]', 'ink' => '#254536',
                    'bg-stone-50', 'stone-50' => '#F9FAFB',
                    'bg-stone-900 text-white', 'bg-stone-900', 'stone-900' => '#1C1917',
                    default => (str_starts_with($rawBg, '#') ? $rawBg : null),
                };
            }
        }

        // 3. Text Color Resolution (custom hex, swatches, presets, legacy title_font_color)
        $rawText = !empty($settings['text_custom_color'])
            ? $settings['text_custom_color']
            : (!empty($settings['text_color']) ? $settings['text_color'] : ($settings['title_font_color'] ?? ''));

        $effectiveTextColor = null;
        if (!empty($rawText)) {
            if (str_starts_with($rawText, '#') || str_starts_with($rawText, 'rgb')) {
                $effectiveTextColor = $rawText;
            } else {
                $effectiveTextColor = match ($rawText) {
                    'text-stone-900' => '#1C1917',
                    'text-stone-700' => '#44403C',
                    'text-stone-500' => '#78716C',
                    'text-white' => '#FFFFFF',
                    'text-[#204b34]' => '#204b34',
                    'text-[#254536]' => '#254536',
                    'text-emerald-700' => '#047857',
                    default => (str_starts_with($rawText, '#') ? $rawText : null),
                };
            }
        }

        // If dark background was chosen and text color wasn't set, default text to white
        if (empty($effectiveTextColor) && !empty($effectiveBgColor)) {
            $darkBgs = ['#254536', '#1C1917', '#204b34', '#18352c', '#1e402b'];
            if (in_array(strtolower($effectiveBgColor), $darkBgs)) {
                $effectiveTextColor = '#FFFFFF';
            }
        }

        // 4. Typography: Font Family, Size, Weight
        $fontFamilyClass = $settings['font_family'] ?? ($settings['title_font_family'] ?? '');
        $fontFamilyCss = match ($fontFamilyClass) {
            'font-serif' => "'Cormorant Garamond', Georgia, serif",
            'font-sans' => "Inter, system-ui, -apple-system, sans-serif",
            'font-mono' => "ui-monospace, SFMono-Regular, Menlo, monospace",
            default => '',
        };

        $fontSizeClass = $settings['font_size'] ?? ($settings['title_font_size'] ?? '');
        $fontSizeCss = match ($fontSizeClass) {
            'text-2xl' => 'font-size: 1.5rem !important; line-height: 2rem !important;',
            'text-3xl' => 'font-size: 1.875rem !important; line-height: 2.25rem !important;',
            'text-4xl' => 'font-size: 2.25rem !important; line-height: 2.5rem !important;',
            'text-5xl' => 'font-size: 3rem !important; line-height: 1.15 !important;',
            'text-6xl' => 'font-size: 3.75rem !important; line-height: 1.1 !important;',
            default => '',
        };
        // Handle legacy arbitrary font size like "text-[20pt]"
        if (empty($fontSizeCss) && !empty($fontSizeClass) && preg_match('/text-\[(.*?)\]/', $fontSizeClass, $m)) {
            $fontSizeCss = "font-size: {$m[1]} !important; line-height: 1.2 !important;";
        }

        $fontWeightClass = $settings['font_weight'] ?? '';
        $fontWeightCss = match ($fontWeightClass) {
            'font-normal' => 'font-weight: 400 !important;',
            'font-medium' => 'font-weight: 500 !important;',
            'font-semibold' => 'font-weight: 600 !important;',
            'font-bold' => 'font-weight: 700 !important;',
            'font-extrabold' => 'font-weight: 800 !important;',
            default => '',
        };

        // 5. Button Global Styling
        $buttonFont = $settings['button_font'] ?? '';
        $buttonBgColor = $settings['button_bg_color'] ?? ($settings['button_primary_color'] ?? '');
        $buttonTextColor = $settings['button_text_color'] ?? ($settings['button_primary_text_color'] ?? '');
        $buttonBorderRadius = $settings['button_border_radius'] ?? '12px';

        // 6. Layout & Width
        $imagePosition = $section?->image_position ?? 'right';
        if (empty($imagePosition)) {
            $imagePosition = 'right';
        }

        $containerWidth = match ($settings['container_width'] ?? 'standard') {
            'wide' => 'max-w-[1400px]',
            'full' => 'max-w-none px-4 sm:px-8',
            default => 'max-w-[1200px]',
        };

        $alignment = match ($settings['alignment'] ?? 'left') {
            'center' => 'text-center',
            'right' => 'text-right',
            default => 'text-left',
        };

        // 7. Responsive Visibility
        $visibility = '';
        if (!empty($settings['hidden_desktop']) && $settings['hidden_desktop'] != '0' && $settings['hidden_desktop'] !== false) {
            $visibility .= ' lg:hidden';
        }
        if (!empty($settings['hidden_tablet']) && $settings['hidden_tablet'] != '0' && $settings['hidden_tablet'] !== false) {
            $visibility .= ' md:max-lg:hidden';
        }
        if (!empty($settings['hidden_mobile']) && $settings['hidden_mobile'] != '0' && $settings['hidden_mobile'] !== false) {
            $visibility .= ' max-md:hidden';
        }

        $anchorId = !empty($settings['anchor_id']) ? $settings['anchor_id'] : 'section-' . ($section?->id ?? 'preview');

        // 8. Build Composite Section Inline Styles
        $styles = [];
        if (!empty($topSpacing)) {
            $styles[] = "--sec-pt: {$topSpacing}; padding-top: var(--sec-pt);";
        }
        if (!empty($bottomSpacing)) {
            $styles[] = "--sec-pb: {$bottomSpacing}; padding-bottom: var(--sec-pb);";
        }
        if (!empty($leftSpacing) && $leftSpacing !== '0px') {
            $styles[] = "--sec-pl: {$leftSpacing}; padding-left: var(--sec-pl);";
        }
        if (!empty($rightSpacing) && $rightSpacing !== '0px') {
            $styles[] = "--sec-pr: {$rightSpacing}; padding-right: var(--sec-pr);";
        }
        if (!empty($effectiveBgColor)) {
            $styles[] = "background-color: {$effectiveBgColor} !important;";
        }
        if (!empty($effectiveTextColor)) {
            $styles[] = "color: {$effectiveTextColor} !important;";
            $styles[] = "--sec-text-color: {$effectiveTextColor};";
        }

        // Build Heading and Body Specific Styles
        $headingStyles = [];
        if (!empty($effectiveTextColor)) {
            $headingStyles[] = "color: {$effectiveTextColor} !important;";
        }
        if (!empty($fontSizeCss)) {
            $headingStyles[] = $fontSizeCss;
        }
        if (!empty($fontWeightCss)) {
            $headingStyles[] = $fontWeightCss;
        }
        if (!empty($fontFamilyCss)) {
            $headingStyles[] = "font-family: {$fontFamilyCss};";
        }

        $bodyStyles = [];
        if (!empty($effectiveTextColor)) {
            $bodyStyles[] = "color: {$effectiveTextColor} !important; opacity: 0.88;";
        }
        if (!empty($fontFamilyCss)) {
            $bodyStyles[] = "font-family: {$fontFamilyCss};";
        }

        return [
            'content' => $content,
            'settings' => $settings,
            'topSpacing' => $topSpacing,
            'bottomSpacing' => $bottomSpacing,
            'leftSpacing' => $leftSpacing,
            'rightSpacing' => $rightSpacing,
            'style' => implode(' ', $styles),
            'headingStyle' => implode(' ', $headingStyles),
            'bodyStyle' => implode(' ', $bodyStyles),
            'bgColor' => $effectiveBgColor,
            'textColor' => $effectiveTextColor,
            'headingColor' => $effectiveTextColor,
            'fontFamily' => $fontFamilyClass,
            'fontSize' => $fontSizeClass,
            'fontWeight' => $fontWeightClass,
            'buttonFont' => $buttonFont,
            'buttonBgColor' => $buttonBgColor,
            'buttonTextColor' => $buttonTextColor,
            'buttonBorderRadius' => $buttonBorderRadius,
            'imagePosition' => $imagePosition,
            'containerWidth' => $containerWidth,
            'alignment' => $alignment,
            'visibility' => trim($visibility),
            'anchorId' => $anchorId,
            'isActive' => $section?->is_active ?? true,
        ];
    }
}
