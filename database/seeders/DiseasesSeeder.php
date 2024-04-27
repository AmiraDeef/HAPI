<?php

namespace Database\Seeders;

use App\Models\Crop;
use App\Models\Disease;
use Illuminate\Database\Seeder;

class DiseasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diseases = [
            [
                'crop' => 'tomato',
                'name' => 'bacterial spot',
                'causes' => 'Bacterial infection by Xanthomonas perforans or Xanthomonas vesicatoria',
                'spread' => 'Splashing water, wind, rain, insects, contaminated tools',
                'prevention' => 'Disease-resistant varieties, crop rotation, clean garden',
                'treatment' => 'Remove infected parts, copper fungicide (limited effectiveness)',
                'symptoms' => 'Dark spots on leaves, stems, and fruits, lesions with a yellow halo'
            ],
            [
                'crop' => 'tomato',
                'name' => 'early blight',
                'causes' => 'Fungal infection by Alternaria solani',
                'spread' => 'Wind, rain, splashing water',
                'prevention' => 'Water at base, improve air circulation by pruning, crop rotation, disease-resistant varieties',
                'treatment' => 'Copper fungicide, remove infected leaves',
                'symptoms' => 'Brown spots with concentric rings on older leaves, leaves turn yellow and drop'
            ],
            [
                'crop' => 'tomato',
                'name' => 'late blight',
                'causes' => 'Fungal infection by Phytophthora infestans',
                'spread' => 'Cool, moist weather, wind, rain',
                'prevention' => 'Fungicide sprays, crop rotation, resistant varieties',
                'treatment' => 'Fungicide sprays, remove infected plant parts',
                'symptoms' => 'Dark, water-soaked spots on leaves, lesions may appear greasy or oily'
            ],
            [
                'crop' => 'tomato',
                'name' => 'septoria leaf spot',
                'causes' => 'Fungal infection by Septoria lycopersici',
                'spread' => 'Wind, rain, splashing water',
                'prevention' => 'Crop rotation, sanitation (remove plant debris), watering at base',
                'treatment' => 'Copper fungicide, remove infected leaves',
                'symptoms' => 'Small, dark spots with white centers on leaves, spots may enlarge and merge'
            ],
            [
                'crop' => 'tomato',
                'name' => 'yellow leaf curl virus',
                'causes' => 'Tomato yellow leaf curl virus (TYLCV) transmitted by whiteflies',
                'spread' => 'Whiteflies',
                'prevention' => 'Control whiteflies (insect traps, insecticidal soap), use insect netting',
                'treatment' => 'No effective treatment, remove infected plants',
                'symptoms' => 'Curling and yellowing of leaves, stunted growth, leaf discoloration'
            ],
            [
                'crop' => 'potato',
                'name' => 'early blight',
                'causes' => 'Fungal infection by Alternaria solani (the same fungus that causes Early Blight in tomatoes)',
                'spread' => 'Wind, rain, splashing water',
                'prevention' => 'Fungicide sprays, crop rotation, resistant varieties',
                'treatment' => 'Fungicide sprays, remove infected leaves',
                'symptoms' => 'Brown spots with concentric rings on leaves, lesions may encircle entire stems'
            ],
            [
                'crop' => 'potato',
                'name' => 'late blight',
                'causes' => 'Fungal infection by Phytophthora infestans (the same fungus that causes Late Blight in tomatoes)',
                'spread' => 'Cool, moist weather, wind, rain',
                'prevention' => 'Fungicide sprays, crop rotation, resistant varieties',
                'treatment' => 'Fungicide sprays, remove infected plant parts',
                'symptoms' => 'Dark, water-soaked lesions on leaves, lesions may spread rapidly to stems and tubers'
            ],
            [
                'crop' => 'corn',
                'name' => 'cercospora leaf spot',
                'causes' => 'Fungal infection by Cercospora zeae-maydis',
                'spread' => 'Wind, rain, splashing water',
                'prevention' => 'Crop rotation, resistant varieties, avoid overhead irrigation',
                'treatment' => 'Fungicide sprays, remove infected leaves (if caught early)',
                'symptoms' => 'Small, gray-brown spots on leaves, lesions may have a dark border'
            ],
            [
                'crop' => 'corn',
                'name' => 'common rust',
                'causes' => 'Fungal infection by several fungal species',
                'spread' => 'Wind, rain',
                'prevention' => 'Resistant varieties, remove volunteer corn plants (these can harbor rust spores)',
                'treatment' => 'Fungicide sprays (usually preventative, not very effective on established rust)',
                'symptoms' => 'Small, circular pustules on leaves, pustules may become powdery and orange'
            ],
            [
                'crop' => 'corn',
                'name' => 'northern leaf blight',
                'causes' => 'Fungal infection by Exserohilum turcicum',
                'spread' => 'Wind, rain',
                'prevention' => 'Resistant varieties, crop rotation',
                'treatment' => 'No effective treatment, remove infected debris',
                'symptoms' => 'Large, cigar-shaped lesions with tan centers and dark borders on leaves'
            ],
            [
                'crop' => 'apple',
                'name' => 'apple scab',
                'causes' => 'Fungal infection by Venturia inaequalis',
                'spread' => 'Rain splashing spores from fallen leaves',
                'prevention' => 'Dormant fungicide sprays, sanitation (remove fallen leaves)',
                'treatment' => 'Fungicide sprays',
                'symptoms' => 'Olive-green to black lesions on leaves, lesions may spread to fruit and twigs'
            ],
            [
                'crop' => 'apple',
                'name' => 'black rot',
                'causes' => 'Fungal infection by Botryosphaeria obtusa',
                'spread' => 'Spores spread by wind and rain',
                'prevention' => 'Prune for good air circulation, avoid injuring fruit, harvest apples at maturity',
                'treatment' => 'Fungicide sprays, remove infected fruits',
                'symptoms' => 'Brown, circular lesions on fruit, lesions may have black pycnidia'
            ],
            [
                'crop' => 'apple',
                'name' => 'cedar apple rust',
                'causes' => 'Fungal infection with two hosts: juniper and apple trees',
                'spread' => 'Wind carries spores from juniper trees',
                'prevention' => 'Remove cedar trees near apple orchards if feasible, fungicide sprays on apple trees',
                'treatment' => 'Fungicide sprays (preventative only)',
                'symptoms' => 'Yellow-orange spots on leaves, spots may develop black fruiting bodies'
            ],
            [
                'crop' => 'cotton',
                'name' => 'bacterial blight',
                'causes' => 'Bacterial infection by Xanthomonas axonopodis pv. malvacearum',
                'spread' => 'Rain, splashing water, insects',
                'prevention' => 'Certified disease-free seed, crop rotation, destroy infected debris',
                'treatment' => 'No effective treatment, remove infected plants',
                'symptoms' => 'Water-soaked lesions on leaves, lesions may become dark and sunken'
            ],
            [
                'crop' => 'cotton',
                'name' => 'curl virus',
                'causes' => 'Several viruses including Cotton Leaf Curl Virus (CLCV) transmitted by whiteflies',
                'spread' => 'Whiteflies',
                'prevention' => 'Control whiteflies, use insect netting',
                'treatment' => 'No effective treatment, remove infected plants',
                'symptoms' => 'Leaf curling, stunted growth, yellowing of leaves'
            ],
            [
                'crop' => 'cotton',
                'name' => 'fusarium wilt',
                'causes' => 'Fungal infection by Fusarium oxysporum',
                'spread' => 'Lives in soil for many years',
                'prevention' => 'Resistant varieties, crop rotation with non-susceptible crops',
                'treatment' => 'No effective treatment',
                'symptoms' => 'Yellowing and wilting of lower leaves, vascular tissue may turn brown'
            ],
            [
                'crop' => 'sugarcane',
                'name' => 'mosaic',
                'causes' => 'Virus spread by aphids',
                'spread' => 'Aphids',
                'prevention' => 'Control aphids (insecticidal soap), use disease-free seed cane',
                'treatment' => 'No effective treatment, remove infected plants',
                'symptoms' => 'Mottled or streaked appearance on leaves, reduced growth and yield'
            ],
            [
                'crop' => 'sugarcane',
                'name' => 'red rot',
                'causes' => 'Fungal infection by Trichoderma harzianum',
                'spread' => 'Spores spread by wind and rain',
                'prevention' => 'Avoid mechanical damage to sugarcane stalks, proper sanitation',
                'treatment' => 'No effective treatment, remove infected stalks',
                'symptoms' => 'Reddish-brown lesions on stalks, lesions may become sunken and gummy'
            ],
            [
                'crop' => 'sugarcane',
                'name' => 'rust',
                'causes' => 'Fungal infection by Puccinia kuehnii',
                'spread' => 'Spores spread by wind',
                'prevention' => 'Resistant varieties',
                'treatment' => 'Fungicide sprays (somewhat effective)',
                'symptoms' => 'Small, circular pustules on leaves, pustules may become reddish-brown'
            ],
            [
                'crop' => 'sugarcane',
                'name' => 'yellow leaf curl virus',
                'causes' => 'Virus spread by leafhoppers',
                'spread' => 'Leafhoppers',
                'prevention' => 'Control leafhoppers (insecticides), use resistant varieties',
                'treatment' => 'No effective treatment, remove infected plants',
                'symptoms' => 'Leaf curling, yellowing of leaves, stunted growth'
            ],
            [
                'crop' => 'wheat',
                'name' => 'brown rust',
                'causes' => 'Fungal infection by Puccinia triticina',
                'spread' => 'Wind carries spores long distances',
                'prevention' => 'Resistant varieties',
                'treatment' => 'No effective treatment',
                'symptoms' => 'Brown, powdery pustules on leaves, pustules may become necrotic'
            ],
            // Add other diseases here...
        ];


        foreach ($diseases as $disease) {
            $crop = Crop::where('name', $disease['crop'])->first();
            if (!$crop) {
                $this->command->error('Crop not found: ' . $disease['crop']);
                continue;
            }
            Disease::create([
                'crop_id' => $crop->id,
                'name' => $disease['name'],
                'causes' => $disease['causes'],
                'spread' => $disease['spread'],
                'prevention' => $disease['prevention'],
                'treatment' => $disease['treatment'],
                'symptoms' => $disease['symptoms']
            ]);
        }
    }
}
