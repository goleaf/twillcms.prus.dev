<?php

namespace App\Services;

use Faker\Factory as Faker;

class ContentGenerationService
{
    private $faker;

    private $fakerLT;

    public function __construct()
    {
        $this->faker = Faker::create('en_US');
        $this->fakerLT = Faker::create('lt_LT');
    }

    /**
     * Generate realistic English content based on category
     */
    public function generateEnglishContent(string $category, string $type = 'post'): array
    {
        switch (strtolower($category)) {
            case 'technology':
                return $this->generateTechContent();
            case 'design':
                return $this->generateDesignContent();
            case 'lifestyle':
                return $this->generateLifestyleContent();
            case 'business':
                return $this->generateBusinessContent();
            case 'travel':
                return $this->generateTravelContent();
            case 'food & cooking':
                return $this->generateFoodContent();
            case 'health & fitness':
                return $this->generateHealthContent();
            case 'photography':
                return $this->generatePhotographyContent();
            default:
                return $this->generateGenericContent();
        }
    }

    /**
     * Generate realistic Lithuanian content based on category
     */
    public function generateLithuanianContent(string $category, string $type = 'post'): array
    {
        switch (strtolower($category)) {
            case 'technology':
            case 'technologijos':
                return $this->generateTechContentLT();
            case 'design':
            case 'dizainas':
                return $this->generateDesignContentLT();
            case 'lifestyle':
            case 'gyvenimo būdas':
                return $this->generateLifestyleContentLT();
            case 'business':
            case 'verslas':
                return $this->generateBusinessContentLT();
            case 'travel':
            case 'kelionės':
                return $this->generateTravelContentLT();
            case 'food & cooking':
            case 'maistas ir gaminimas':
                return $this->generateFoodContentLT();
            case 'health & fitness':
            case 'sveikata ir sportas':
                return $this->generateHealthContentLT();
            case 'photography':
            case 'fotografija':
                return $this->generatePhotographyContentLT();
            default:
                return $this->generateGenericContentLT();
        }
    }

    /**
     * Generate rich HTML content with various elements
     */
    public function generateRichContent(array $paragraphs, string $language = 'en'): string
    {
        $content = [];
        $totalParagraphs = count($paragraphs);

        foreach ($paragraphs as $index => $paragraph) {
            $content[] = "<p>{$paragraph}</p>";

            // Add variety every few paragraphs
            if ($index > 0 && $index < $totalParagraphs - 1 && rand(1, 4) === 1) {
                $element = $this->generateRandomElement($language);
                if ($element) {
                    $content[] = $element;
                }
            }
        }

        return implode("\n\n", $content);
    }

    /**
     * Generate random HTML elements for content variety
     */
    private function generateRandomElement(string $language): ?string
    {
        $elements = ['heading', 'list', 'quote', 'code'];
        $element = $elements[array_rand($elements)];

        switch ($element) {
            case 'heading':
                return $this->generateHeading($language);
            case 'list':
                return $this->generateList($language);
            case 'quote':
                return $this->generateQuote($language);
            case 'code':
                return $this->generateCodeBlock();
            default:
                return null;
        }
    }

    private function generateHeading(string $language): string
    {
        $faker = $language === 'lt' ? $this->fakerLT : $this->faker;

        return '<h3>'.$faker->sentence(rand(3, 6)).'</h3>';
    }

    private function generateList(string $language): string
    {
        $faker = $language === 'lt' ? $this->fakerLT : $this->faker;
        $items = [];
        for ($i = 0; $i < rand(3, 6); $i++) {
            $items[] = '<li>'.$faker->sentence(rand(3, 8)).'</li>';
        }

        return '<ul>'.implode('', $items).'</ul>';
    }

    private function generateQuote(string $language): string
    {
        $faker = $language === 'lt' ? $this->fakerLT : $this->faker;

        return '<blockquote><p>"'.$faker->paragraph(rand(1, 3)).'"</p></blockquote>';
    }

    private function generateCodeBlock(): string
    {
        $codes = [
            "function example() {\n    return 'Hello, World!';\n}",
            "const data = {\n    name: 'Example',\n    value: 42\n};",
            "SELECT * FROM posts\nWHERE published = 1\nORDER BY created_at DESC;",
            "npm install package-name\nnpm run build\nnpm start",
        ];

        return '<pre><code>'.$codes[array_rand($codes)].'</code></pre>';
    }

    // Technology content generators
    private function generateTechContent(): array
    {
        $titles = [
            'The Future of Artificial Intelligence in Web Development',
            'Building Scalable Applications with Modern JavaScript',
            'Understanding Cloud Computing: A Complete Guide',
            'The Rise of Quantum Computing and Its Applications',
            'Mobile-First Development: Best Practices for 2024',
            'Cybersecurity Trends Every Developer Should Know',
            'The Evolution of Machine Learning Algorithms',
            'Blockchain Technology Beyond Cryptocurrency',
        ];

        $title = $titles[array_rand($titles)];

        return [
            'title' => $title,
            'description' => $this->faker->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Technology continues to evolve at an unprecedented pace, reshaping how we interact with the digital world.',
                $this->faker->paragraph(4),
                'The implementation of new frameworks and methodologies has revolutionized software development.',
                $this->faker->paragraph(5),
                'As we look towards the future, emerging technologies promise to unlock new possibilities.',
                $this->faker->paragraph(3),
                'Understanding these trends is crucial for staying competitive in the tech industry.',
                $this->faker->paragraph(4),
            ]),
        ];
    }

    private function generateTechContentLT(): array
    {
        $titles = [
            'Dirbtinio intelekto ateitis internetinių svetainių kūrime',
            'Kaip kurti masštabuojamas aplikacijas su moderniu JavaScript',
            'Debesų kompiuterija: pilnas vadovas pradedantiesiems',
            'Kvantinio kompiuterio vystymasis ir taikymas',
            'Mobilusis dizainas: geriausi sprendimai 2024 metams',
            'Kibernetinio saugumo tendencijos, kurias turi žinoti kūrėjai',
            'Mašininio mokymosi algoritmų evoliucija',
            'Blockchain technologija už kriptovaliutų ribų',
        ];

        $title = $titles[array_rand($titles)];

        return [
            'title' => $title,
            'description' => $this->fakerLT->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Technologijos toliau vystosi neprecedentiniu greičiu, keisdamos mūsų sąveiką su skaitmeniniu pasauliu.',
                $this->fakerLT->paragraph(4),
                'Naujų karkasų ir metodologijų įgyvendinimas revoliucionizavo programinės įrangos kūrimą.',
                $this->fakerLT->paragraph(5),
                'Žvelgdami į ateitį, kylančios technologijos žada atrakinti naujas galimybes.',
                $this->fakerLT->paragraph(3),
                'Šių tendencijų supratimas yra labai svarbus išliekant konkurencingam technologijų srityje.',
                $this->fakerLT->paragraph(4),
            ], 'lt'),
        ];
    }

    // Design content generators
    private function generateDesignContent(): array
    {
        $titles = [
            'Minimalist Design Principles for Modern Websites',
            'Color Theory in Digital Design: A Comprehensive Guide',
            'Typography Trends That Will Define 2024',
            'User Experience Design: From Concept to Implementation',
            'The Psychology of Visual Hierarchy in Web Design',
            'Creating Accessible Designs for All Users',
            'Motion Graphics: Bringing Static Designs to Life',
            'Brand Identity Design in the Digital Age',
        ];

        return [
            'title' => $titles[array_rand($titles)],
            'description' => $this->faker->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Design is not just about aesthetics; it\'s about creating meaningful experiences for users.',
                $this->faker->paragraph(4),
                'The principles of good design remain constant while tools and trends evolve.',
                $this->faker->paragraph(5),
                'Understanding user psychology is crucial for creating effective design solutions.',
                $this->faker->paragraph(3),
                'Great design balances form and function to achieve optimal user satisfaction.',
                $this->faker->paragraph(4),
            ]),
        ];
    }

    private function generateDesignContentLT(): array
    {
        return [
            'title' => 'Minimalistinio dizaino principai šiuolaikiniams tinklalapiams',
            'description' => $this->fakerLT->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Dizainas - tai ne tik estetika, bet ir prasmingų vartotojo patirčių kūrimas.',
                $this->fakerLT->paragraph(4),
                'Gero dizaino principai išlieka pastovūs, o įrankiai ir tendencijos keičiasi.',
                $this->fakerLT->paragraph(5),
                'Vartotojo psichologijos supratimas yra labai svarbus kuriant efektyvius dizaino sprendimus.',
                $this->fakerLT->paragraph(3),
                'Puikus dizainas subalansuoja formą ir funkciją optimaliam vartotojo pasitenkinimui.',
                $this->fakerLT->paragraph(4),
            ], 'lt'),
        ];
    }

    // Lifestyle content generators
    private function generateLifestyleContent(): array
    {
        $titles = [
            'Finding Balance: Work-Life Integration in the Modern World',
            'Sustainable Living: Small Changes, Big Impact',
            'The Art of Mindful Living in a Digital Age',
            'Creating a Productive Home Office Space',
            'Minimalism: Less Stuff, More Life',
        ];

        return [
            'title' => $titles[array_rand($titles)],
            'description' => $this->faker->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Modern life presents unique challenges that require thoughtful approaches to well-being.',
                $this->faker->paragraph(4),
                'Creating sustainable habits is more important than pursuing perfection.',
                $this->faker->paragraph(5),
                'Small, consistent changes often lead to the most significant transformations.',
                $this->faker->paragraph(3),
            ]),
        ];
    }

    private function generateLifestyleContentLT(): array
    {
        return [
            'title' => 'Pusiausvyros paieška: darbo ir gyvenimo integravimas šiuolaikiniame pasaulyje',
            'description' => $this->fakerLT->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Šiuolaikinis gyvenimas kelia unikalius iššūkius, reikalaujančius apgalvotų gerovės sprendimų.',
                $this->fakerLT->paragraph(4),
                'Tvarių įpročių kūrimas yra svarbiau nei tobulybės siekimas.',
                $this->fakerLT->paragraph(5),
            ], 'lt'),
        ];
    }

    // Generic content fallbacks
    private function generateGenericContent(): array
    {
        return [
            'title' => $this->faker->sentence(rand(4, 8)),
            'description' => $this->faker->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                $this->faker->paragraph(4),
                $this->faker->paragraph(5),
                $this->faker->paragraph(3),
                $this->faker->paragraph(4),
            ]),
        ];
    }

    private function generateGenericContentLT(): array
    {
        return [
            'title' => $this->fakerLT->sentence(rand(4, 8)),
            'description' => $this->fakerLT->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                $this->fakerLT->paragraph(4),
                $this->fakerLT->paragraph(5),
                $this->fakerLT->paragraph(3),
                $this->fakerLT->paragraph(4),
            ], 'lt'),
        ];
    }

    // Business content generators
    private function generateBusinessContent(): array
    {
        $titles = [
            'Digital Transformation Strategies for Small Businesses',
            'The Future of Remote Work: Trends and Predictions',
            'Building a Strong Brand in the Digital Marketplace',
            'Effective Leadership in Times of Change',
            'Customer Experience: The New Competitive Advantage',
            'Innovation Management: Turning Ideas into Reality',
            'Sustainable Business Practices for Long-term Success',
            'The Role of Data Analytics in Business Decision Making',
        ];

        return [
            'title' => $titles[array_rand($titles)],
            'description' => $this->faker->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Successful businesses adapt to changing market conditions while maintaining core values.',
                $this->faker->paragraph(4),
                'Innovation and customer focus remain the cornerstones of sustainable growth.',
                $this->faker->paragraph(5),
                'Data-driven decision making enables companies to stay ahead of the competition.',
                $this->faker->paragraph(3),
                'Building strong relationships with stakeholders is essential for long-term success.',
                $this->faker->paragraph(4),
            ]),
        ];
    }

    private function generateBusinessContentLT(): array
    {
        return [
            'title' => 'Skaitmeninės transformacijos strategijos mažoms įmonėms',
            'description' => $this->fakerLT->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Sėkmingos įmonės prisitaiko prie kintančių rinkos sąlygų, išlaikydamos pagrindines vertybes.',
                $this->fakerLT->paragraph(4),
                'Inovacijos ir orientacija į klientą išlieka tvaraus augimo kertiniu akmeniu.',
                $this->fakerLT->paragraph(5),
                'Duomenimis pagrįstas sprendimų priėmimas leidžia įmonėms aplenkti konkurentus.',
                $this->fakerLT->paragraph(3),
                'Stiprių santykių su suinteresuotais subjektais kūrimas yra būtinas ilgalaikiam sėkmei.',
                $this->fakerLT->paragraph(4),
            ], 'lt'),
        ];
    }

    // Travel content generators
    private function generateTravelContent(): array
    {
        $titles = [
            'Hidden Gems: Undiscovered Destinations Worth Visiting',
            'Sustainable Travel: Exploring the World Responsibly',
            'Solo Travel Tips: Confidence and Safety on the Road',
            'Cultural Immersion: Going Beyond Tourist Attractions',
            'Budget Travel Hacks for the Savvy Explorer',
            'Photography on the Road: Capturing Travel Memories',
            'Digital Nomad Lifestyle: Working While Traveling',
            'Adventure Travel: Pushing Your Comfort Zone',
        ];

        return [
            'title' => $titles[array_rand($titles)],
            'description' => $this->faker->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Travel opens our minds to new perspectives and creates lifelong memories.',
                $this->faker->paragraph(4),
                'The best journeys often happen when we step outside our comfort zones.',
                $this->faker->paragraph(5),
                'Cultural exchange enriches both travelers and the communities they visit.',
                $this->faker->paragraph(3),
                'Responsible travel ensures that future generations can enjoy the same destinations.',
                $this->faker->paragraph(4),
            ]),
        ];
    }

    private function generateTravelContentLT(): array
    {
        return [
            'title' => 'Paslėpti perlas: neatrasti tikslai, kuriuos verta aplankyti',
            'description' => $this->fakerLT->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Kelionės atveria mūsų protus naujoms perspektyvoms ir sukuria visam gyvenimui išliekančius prisiminimus.',
                $this->fakerLT->paragraph(4),
                'Geriausi kelionės dažnai įvyksta, kai išeiname iš komforto zonos.',
                $this->fakerLT->paragraph(5),
                'Kultūrų mainai praturtina tiek keliautojus, tiek bendruomenes, kurias jie aplanko.',
                $this->fakerLT->paragraph(3),
                'Atsakinga kelionė užtikrina, kad ateities kartos galės mėgautis tais pačiais tikslais.',
                $this->fakerLT->paragraph(4),
            ], 'lt'),
        ];
    }

    // Food content generators
    private function generateFoodContent(): array
    {
        $titles = [
            'Farm-to-Table: The Benefits of Local Ingredients',
            'Mastering Basic Cooking Techniques for Beginners',
            'Plant-Based Nutrition: Delicious and Healthy Meals',
            'International Flavors: Exploring Global Cuisines',
            'Meal Prep Strategies for Busy Professionals',
            'The Art of Food Presentation: Making Meals Instagram-Worthy',
            'Fermentation at Home: Ancient Techniques for Modern Kitchens',
            'Seasonal Cooking: Making the Most of Fresh Ingredients',
        ];

        return [
            'title' => $titles[array_rand($titles)],
            'description' => $this->faker->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Good food brings people together and nourishes both body and soul.',
                $this->faker->paragraph(4),
                'Learning to cook opens up a world of flavors and creativity.',
                $this->faker->paragraph(5),
                'Fresh, seasonal ingredients make all the difference in taste and nutrition.',
                $this->faker->paragraph(3),
                'Cooking at home gives you control over what goes into your meals.',
                $this->faker->paragraph(4),
            ]),
        ];
    }

    private function generateFoodContentLT(): array
    {
        return [
            'title' => 'Nuo ūkio iki stalo: vietinių ingredientų nauda',
            'description' => $this->fakerLT->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Geras maistas suartina žmones ir maitina tiek kūną, tiek sielą.',
                $this->fakerLT->paragraph(4),
                'Mokymasis gaminti atveria skonių ir kūrybiškumo pasaulį.',
                $this->fakerLT->paragraph(5),
                'Šviežūs, sezoniški ingredientai daro visą skirtumą skoniui ir mitybai.',
                $this->fakerLT->paragraph(3),
                'Gaminimas namuose suteikia kontrolę, kas patenka į jūsų patiekalus.',
                $this->fakerLT->paragraph(4),
            ], 'lt'),
        ];
    }

    // Health content generators
    private function generateHealthContent(): array
    {
        $titles = [
            'Building Sustainable Fitness Habits for Long-term Health',
            'Mental Health Awareness: Breaking the Stigma',
            'Nutrition Science: Understanding What Your Body Needs',
            'Sleep Optimization: The Foundation of Good Health',
            'Stress Management Techniques for Modern Life',
            'Preventive Healthcare: Taking Charge of Your Well-being',
            'The Mind-Body Connection: Holistic Approaches to Health',
            'Fitness After 40: Adapting Your Routine as You Age',
        ];

        return [
            'title' => $titles[array_rand($titles)],
            'description' => $this->faker->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'True health encompasses physical, mental, and emotional well-being.',
                $this->faker->paragraph(4),
                'Small, consistent changes in lifestyle can lead to significant health improvements.',
                $this->faker->paragraph(5),
                'Prevention is always better than cure when it comes to health.',
                $this->faker->paragraph(3),
                'Listen to your body and prioritize what makes you feel your best.',
                $this->faker->paragraph(4),
            ]),
        ];
    }

    private function generateHealthContentLT(): array
    {
        return [
            'title' => 'Tvarių fitneso įpročių kūrimas ilgalaikei sveikatai',
            'description' => $this->fakerLT->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Tikra sveikata apima fizinę, psichinę ir emocinę gerovę.',
                $this->fakerLT->paragraph(4),
                'Maži, nuoseklūs gyvenimo būdo pokyčiai gali lemti reikšmingus sveikatos pagerėjimus.',
                $this->fakerLT->paragraph(5),
                'Prevencija visada geresnė nei gydymas, kai kalbama apie sveikatą.',
                $this->fakerLT->paragraph(3),
                'Klausykitės savo kūno ir teikite pirmenybę tam, kas jums padeda jaustis geriausiai.',
                $this->fakerLT->paragraph(4),
            ], 'lt'),
        ];
    }

    // Photography content generators
    private function generatePhotographyContent(): array
    {
        $titles = [
            'Composition Techniques That Transform Ordinary Photos',
            'Understanding Light: The Key to Better Photography',
            'Street Photography: Capturing Life in Motion',
            'Portrait Photography: Bringing Out the Best in People',
            'Landscape Photography: Patience and Planning Pay Off',
            'Mobile Photography: Creating Art with Your Smartphone',
            'Post-Processing Workflow: From RAW to Final Image',
            'Building a Photography Portfolio That Stands Out',
        ];

        return [
            'title' => $titles[array_rand($titles)],
            'description' => $this->faker->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Photography is about seeing the extraordinary in the ordinary.',
                $this->faker->paragraph(4),
                'Light is the most important element in creating compelling photographs.',
                $this->faker->paragraph(5),
                'Composition rules are guidelines, but creativity knows no bounds.',
                $this->faker->paragraph(3),
                'The best camera is the one you have with you when inspiration strikes.',
                $this->faker->paragraph(4),
            ]),
        ];
    }

    private function generatePhotographyContentLT(): array
    {
        return [
            'title' => 'Kompozicijos metodai, keičiantys paprastus foto į ypatingus',
            'description' => $this->fakerLT->paragraph(rand(2, 4)),
            'content' => $this->generateRichContent([
                'Fotografija yra apie nepaprastų dalykų matymą paprastuose.',
                $this->fakerLT->paragraph(4),
                'Šviesa yra svarbiausias elementas kuriant patrauklius fotografijas.',
                $this->fakerLT->paragraph(5),
                'Kompozicijos taisyklės yra gairės, bet kūrybiškumas ribų neturi.',
                $this->fakerLT->paragraph(3),
                'Geriausias fotoaparatas yra tas, kurį turite su savimi, kai ateina įkvėpimas.',
                $this->fakerLT->paragraph(4),
            ], 'lt'),
        ];
    }
}
