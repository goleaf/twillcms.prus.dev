<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ImageGenerationService
{
    private $colors;

    private $gradients;

    public function __construct()
    {
        $this->colors = [
            'primary' => ['#667eea', '#764ba2', '#f093fb', '#43cea2'],
            'warm' => ['#ff9a9e', '#fecfef', '#ffecd2', '#fcb69f'],
            'cool' => ['#a8edea', '#fed6e3', '#84fab0', '#8fd3f4'],
            'neutral' => ['#d3cce3', '#e9e4f0', '#ffeaa7', '#fab1a0'],
            'vibrant' => ['#ff7675', '#fd79a8', '#fdcb6e', '#6c5ce7'],
        ];

        $this->gradients = [
            ['#667eea', '#764ba2'],
            ['#f093fb', '#f5576c'],
            ['#4facfe', '#00f2fe'],
            ['#43e97b', '#38f9d7'],
            ['#fa709a', '#fee140'],
            ['#a8edea', '#fed6e3'],
            ['#ffecd2', '#fcb69f'],
            ['#ff9a9e', '#fecfef'],
            ['#84fab0', '#8fd3f4'],
            ['#a6c0fe', '#f68084'],
        ];
    }

    /**
     * Generate enhanced post image with category-specific styling
     */
    public function generatePostImage($post, string $title, ?string $category = null): string
    {
        $width = 1200;
        $height = 630; // Optimal for social media sharing

        // Create base image with gradient
        // @phpstan-ignore-next-line
        $image = Image::canvas($width, $height);

        // Apply category-specific gradient
        $this->applyCategoryGradient($image, $width, $height, $category);

        // Add geometric patterns
        $this->addGeometricPatterns($image, $width, $height, $category);

        // Add title overlay with better typography
        $this->addTitleOverlay($image, $title, $width, $height);

        // Add category badge if provided
        if ($category) {
            $this->addCategoryBadge($image, $category, $width, $height);
        }

        // Add subtle texture overlay
        $this->addTextureOverlay($image, $width, $height);

        // Generate filename and save
        $filename = $this->generateFilename($post, 'post');
        $path = "public/images/posts/{$filename}";

        // Ensure directory exists
        Storage::makeDirectory('public/images/posts');

        // Save with optimization
        $image->encode('jpg', 85)->save(storage_path("app/{$path}"));

        return $filename;
    }

    /**
     * Generate enhanced category image with sophisticated design
     */
    public function generateCategoryImage($category, string $title): string
    {
        $width = 800;
        $height = 400;

        // @phpstan-ignore-next-line
        $image = Image::canvas($width, $height);

        // Apply category-specific styling
        $this->applyCategoryGradient($image, $width, $height, $title);
        $this->addCategorySpecificPattern($image, $width, $height, $title);
        $this->addCategoryIcon($image, $title, $width, $height);
        $this->addTitleOverlay($image, $title, $width, $height, 'category');

        // Generate filename and save
        $filename = $this->generateFilename($category, 'category');
        $path = "public/images/categories/{$filename}";

        Storage::makeDirectory('public/images/categories');
        $image->encode('jpg', 85)->save(storage_path("app/{$path}"));

        return $filename;
    }

    /**
     * Apply category-specific gradient background
     */
    private function applyCategoryGradient($image, int $width, int $height, ?string $category): void
    {
        $gradient = $this->getCategoryGradient($category);

        // Create gradient effect
        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / $height;
            $color = $this->interpolateColor($gradient[0], $gradient[1], $ratio);
            $image->rectangle(0, $y, $width, $y + 1, function ($draw) use ($color) {
                $draw->background($color);
            });
        }
    }

    /**
     * Get category-specific gradient colors
     */
    private function getCategoryGradient(?string $category): array
    {
        $categoryGradients = [
            'technology' => ['#667eea', '#764ba2'],
            'technologijos' => ['#667eea', '#764ba2'],
            'design' => ['#f093fb', '#f5576c'],
            'dizainas' => ['f093fb', '#f5576c'],
            'lifestyle' => ['#43e97b', '#38f9d7'],
            'gyvenimo būdas' => ['#43e97b', '#38f9d7'],
            'business' => ['#4facfe', '#00f2fe'],
            'verslas' => ['#4facfe', '#00f2fe'],
            'travel' => ['#fa709a', '#fee140'],
            'kelionės' => ['#fa709a', '#fee140'],
            'food & cooking' => ['#ff9a9e', '#fecfef'],
            'maistas ir gaminimas' => ['#ff9a9e', '#fecfef'],
            'health & fitness' => ['#a8edea', '#fed6e3'],
            'sveikata ir sportas' => ['#a8edea', '#fed6e3'],
            'photography' => ['#84fab0', '#8fd3f4'],
            'fotografija' => ['#84fab0', '#8fd3f4'],
        ];

        $key = strtolower($category ?? '');

        return $categoryGradients[$key] ?? $this->gradients[array_rand($this->gradients)];
    }

    /**
     * Add geometric patterns based on category
     */
    private function addGeometricPatterns($image, int $width, int $height, ?string $category): void
    {
        $category = strtolower($category ?? '');

        switch ($category) {
            case 'technology':
            case 'technologijos':
                $this->addTechPattern($image, $width, $height);
                break;
            case 'design':
            case 'dizainas':
                $this->addDesignPattern($image, $width, $height);
                break;
            case 'travel':
            case 'kelionės':
                $this->addTravelPattern($image, $width, $height);
                break;
            default:
                $this->addGenericPattern($image, $width, $height);
        }
    }

    /**
     * Add technology-specific patterns (circuits, nodes)
     */
    private function addTechPattern($image, int $width, int $height): void
    {
        // Add circuit-like patterns
        for ($i = 0; $i < 10; $i++) {
            $x = rand(50, $width - 50);
            $y = rand(50, $height - 50);
            $size = rand(20, 40);

            // Draw nodes
            $image->circle($size, $x, $y, function ($draw) {
                $draw->background('rgba(255, 255, 255, 0.1)');
                $draw->border(2, 'rgba(255, 255, 255, 0.3)');
            });

            // Draw connecting lines
            if ($i > 0) {
                $prevX = rand(50, $width - 50);
                $prevY = rand(50, $height - 50);
                $image->line($x, $y, $prevX, $prevY, function ($draw) {
                    $draw->color('rgba(255, 255, 255, 0.2)');
                    $draw->width(1);
                });
            }
        }
    }

    /**
     * Add design-specific patterns (geometric shapes)
     */
    private function addDesignPattern($image, int $width, int $height): void
    {
        // Add geometric shapes
        for ($i = 0; $i < 8; $i++) {
            $x = rand(0, $width);
            $y = rand(0, $height);
            $size = rand(50, 150);

            $shape = rand(1, 3);
            switch ($shape) {
                case 1: // Triangle
                    $points = [
                        $x, $y - $size / 2,
                        $x - $size / 2, $y + $size / 2,
                        $x + $size / 2, $y + $size / 2,
                    ];
                    $image->polygon($points, function ($draw) {
                        $draw->background('rgba(255, 255, 255, 0.05)');
                        $draw->border(1, 'rgba(255, 255, 255, 0.15)');
                    });
                    break;
                case 2: // Rectangle
                    $image->rectangle($x - $size / 2, $y - $size / 2, $x + $size / 2, $y + $size / 2, function ($draw) {
                        $draw->background('rgba(255, 255, 255, 0.05)');
                        $draw->border(1, 'rgba(255, 255, 255, 0.15)');
                    });
                    break;
                case 3: // Circle
                    $image->circle($size, $x, $y, function ($draw) {
                        $draw->background('rgba(255, 255, 255, 0.05)');
                        $draw->border(1, 'rgba(255, 255, 255, 0.15)');
                    });
                    break;
            }
        }
    }

    /**
     * Add travel-specific patterns (paths, destinations)
     */
    private function addTravelPattern($image, int $width, int $height): void
    {
        // Add path-like curves
        for ($i = 0; $i < 5; $i++) {
            $startX = rand(0, $width / 3);
            $startY = rand(0, $height);
            $endX = rand(2 * $width / 3, $width);
            $endY = rand(0, $height);

            // Create curved path
            $image->line($startX, $startY, $endX, $endY, function ($draw) {
                $draw->color('rgba(255, 255, 255, 0.1)');
                $draw->width(3);
            });

            // Add destination points
            $image->circle(8, $endX, $endY, function ($draw) {
                $draw->background('rgba(255, 255, 255, 0.3)');
            });
        }
    }

    /**
     * Add generic pattern for other categories
     */
    private function addGenericPattern($image, int $width, int $height): void
    {
        // Add scattered dots pattern
        for ($i = 0; $i < 30; $i++) {
            $x = rand(0, $width);
            $y = rand(0, $height);
            $size = rand(2, 8);

            $image->circle($size, $x, $y, function ($draw) {
                $draw->background('rgba(255, 255, 255, 0.1)');
            });
        }
    }

    /**
     * Add title overlay with enhanced typography
     */
    private function addTitleOverlay($image, string $title, int $width, int $height, string $type = 'post'): void
    {
        // Prepare title - limit length and add line breaks
        $title = $this->prepareTitle($title, $type === 'category' ? 30 : 60);

        // Add semi-transparent overlay for text readability
        $image->rectangle(0, $height * 0.6, $width, $height, function ($draw) {
            $draw->background('rgba(0, 0, 0, 0.4)');
        });

        // Add title text
        $fontSize = $type === 'category' ? 32 : 28;
        $image->text($title, $width / 2, $height * 0.75, function ($font) use ($fontSize) {
            $font->file(storage_path('fonts/arial.ttf')); // You may need to add fonts
            $font->size($fontSize);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });
    }

    /**
     * Add category badge
     */
    private function addCategoryBadge($image, string $category, int $width, int $height): void
    {
        // Add category badge in top-right corner
        $badgeWidth = 120;
        $badgeHeight = 40;
        $margin = 20;

        $image->rectangle(
            $width - $badgeWidth - $margin,
            $margin,
            $width - $margin,
            $margin + $badgeHeight,
            function ($draw) {
                $draw->background('rgba(255, 255, 255, 0.2)');
                $draw->border(1, 'rgba(255, 255, 255, 0.5)');
            }
        );

        $image->text(strtoupper($category), $width - $badgeWidth / 2 - $margin, $margin + $badgeHeight / 2, function ($font) {
            $font->size(12);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });
    }

    /**
     * Add category-specific icon
     */
    private function addCategoryIcon($image, string $category, int $width, int $height): void
    {
        $iconSize = 60;
        $x = $width - 100;
        $y = 50;

        // Simple icon representation using shapes
        $category = strtolower($category);

        switch ($category) {
            case 'technology':
            case 'technologijos':
                // Tech icon (monitor shape)
                $image->rectangle($x - $iconSize / 2, $y - $iconSize / 3, $x + $iconSize / 2, $y + $iconSize / 3, function ($draw) {
                    $draw->border(3, 'rgba(255, 255, 255, 0.6)');
                });
                break;
            case 'design':
            case 'dizainas':
                // Design icon (palette shape)
                $image->circle($iconSize / 2, $x, $y, function ($draw) {
                    $draw->border(3, 'rgba(255, 255, 255, 0.6)');
                });
                break;
        }
    }

    /**
     * Add texture overlay for depth
     */
    private function addTextureOverlay($image, int $width, int $height): void
    {
        // Add noise texture
        for ($i = 0; $i < 1000; $i++) {
            $x = rand(0, $width);
            $y = rand(0, $height);
            $opacity = rand(1, 5) / 100;

            $image->pixel($x, $y, "rgba(255, 255, 255, {$opacity})");
        }
    }

    /**
     * Interpolate between two hex colors
     */
    private function interpolateColor(string $color1, string $color2, float $ratio): string
    {
        $rgb1 = $this->hexToRgb($color1);
        $rgb2 = $this->hexToRgb($color2);

        $r = round($rgb1[0] + ($rgb2[0] - $rgb1[0]) * $ratio);
        $g = round($rgb1[1] + ($rgb2[1] - $rgb1[1]) * $ratio);
        $b = round($rgb1[2] + ($rgb2[2] - $rgb1[2]) * $ratio);

        return "rgb({$r}, {$g}, {$b})";
    }

    /**
     * Convert hex color to RGB array
     */
    private function hexToRgb(string $hex): array
    {
        $hex = str_replace('#', '', $hex);

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Prepare title for display (word wrapping, length limiting)
     */
    private function prepareTitle(string $title, int $maxLength = 60): string
    {
        if (strlen($title) > $maxLength) {
            $title = substr($title, 0, $maxLength - 3).'...';
        }

        // Simple word wrapping for longer titles
        if (strlen($title) > 30) {
            $words = explode(' ', $title);
            $lines = [];
            $currentLine = '';

            foreach ($words as $word) {
                if (strlen($currentLine.' '.$word) > 30) {
                    $lines[] = $currentLine;
                    $currentLine = $word;
                } else {
                    $currentLine .= ($currentLine ? ' ' : '').$word;
                }
            }
            $lines[] = $currentLine;

            return implode("\n", array_slice($lines, 0, 2)); // Max 2 lines
        }

        return $title;
    }

    /**
     * Generate unique filename for images
     */
    private function generateFilename($model, string $type): string
    {
        $id = $model->id ?? uniqid();
        $timestamp = time();

        return "{$type}_{$id}_{$timestamp}.jpg";
    }

    /**
     * Add category-specific pattern for category images
     */
    private function addCategorySpecificPattern($image, int $width, int $height, string $category): void
    {
        $this->addGeometricPatterns($image, $width, $height, $category);
    }
}
