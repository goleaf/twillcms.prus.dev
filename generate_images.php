<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$imageDir = storage_path('app/public/images/posts');
if (! is_dir($imageDir)) {
    mkdir($imageDir, 0755, true);
}

$posts = \App\Models\Post::all();
echo 'Generating images for '.count($posts)." posts...\n";

foreach ($posts as $post) {
    $imagePath = $imageDir.'/post-'.$post->id.'.png';

    if (! file_exists($imagePath)) {
        $width = 800;
        $height = 400;
        $image = imagecreate($width, $height);

        $colors = [
            ['r' => 59, 'g' => 130, 'b' => 246],
            ['r' => 16, 'g' => 185, 'b' => 129],
            ['r' => 245, 'g' => 101, 'b' => 101],
            ['r' => 168, 'g' => 85, 'b' => 247],
            ['r' => 251, 'g' => 191, 'b' => 36],
        ];

        $color = $colors[array_rand($colors)];
        $backgroundColor = imagecolorallocate($image, $color['r'], $color['g'], $color['b']);
        $textColor = imagecolorallocate($image, 255, 255, 255);

        imagefill($image, 0, 0, $backgroundColor);

        $title = substr($post->title, 0, 50);
        if (strlen($post->title) > 50) {
            $title .= '...';
        }

        imagestring($image, 5, 50, 180, $title, $textColor);
        imagestring($image, 3, 50, 220, 'Post #'.$post->id, $textColor);

        imagepng($image, $imagePath);
        imagedestroy($image);

        echo "Generated: post-{$post->id}.png\n";
    }
}

echo "Done!\n";
