<?php

namespace Database\Seeders;

use App\Models\QuestionnaireTemplate;
use Illuminate\Database\Seeder;

class QuestionnaireTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'specialty' => 'Médecine générale',
                'title' => 'Questionnaire de médecine générale',
                'description' => 'Questionnaire standard pour consultation de médecine générale',
                'questions' => [
                    [
                        'id' => 'smoking',
                        'question' => 'Fumez-vous ?',
                        'type' => 'radio',
                        'options' => ['Oui', 'Non', 'Anciennement'],
                    ],
                    [
                        'id' => 'alcohol',
                        'question' => 'Consommez-vous de l\'alcool ?',
                        'type' => 'radio',
                        'options' => ['Jamais', 'Occasionnellement', 'Régulièrement'],
                    ],
                    [
                        'id' => 'exercise',
                        'question' => 'Pratiquez-vous une activité physique régulière ?',
                        'type' => 'radio',
                        'options' => ['Oui', 'Non'],
                    ],
                ],
                'is_active' => true,
                'order' => 1,
            ],
            [
                'specialty' => 'Cardiologie',
                'title' => 'Questionnaire de cardiologie',
                'description' => 'Questionnaire pré-consultation pour cardiologie',
                'questions' => [
                    [
                        'id' => 'chest_pain',
                        'question' => 'Ressentez-vous des douleurs thoraciques ?',
                        'type' => 'radio',
                        'options' => ['Oui', 'Non'],
                    ],
                    [
                        'id' => 'shortness_breath',
                        'question' => 'Avez-vous des difficultés respiratoires ?',
                        'type' => 'radio',
                        'options' => ['Oui', 'Non', 'Parfois'],
                    ],
                    [
                        'id' => 'palpitations',
                        'question' => 'Ressentez-vous des palpitations ?',
                        'type' => 'radio',
                        'options' => ['Oui', 'Non', 'Parfois'],
                    ],
                    [
                        'id' => 'family_heart_disease',
                        'question' => 'Y a-t-il des antécédents de maladie cardiaque dans votre famille ?',
                        'type' => 'radio',
                        'options' => ['Oui', 'Non', 'Je ne sais pas'],
                    ],
                ],
                'is_active' => true,
                'order' => 2,
            ],
            [
                'specialty' => 'Dermatologie',
                'title' => 'Questionnaire de dermatologie',
                'description' => 'Questionnaire pré-consultation pour dermatologie',
                'questions' => [
                    [
                        'id' => 'skin_type',
                        'question' => 'Quel est votre type de peau ?',
                        'type' => 'radio',
                        'options' => ['Sèche', 'Grasse', 'Mixte', 'Normale', 'Sensible'],
                    ],
                    [
                        'id' => 'sun_exposure',
                        'question' => 'Êtes-vous souvent exposé au soleil ?',
                        'type' => 'radio',
                        'options' => ['Oui', 'Non', 'Occasionnellement'],
                    ],
                    [
                        'id' => 'allergies',
                        'question' => 'Avez-vous des allergies cutanées connues ?',
                        'type' => 'text',
                    ],
                    [
                        'id' => 'previous_treatments',
                        'question' => 'Avez-vous déjà suivi des traitements dermatologiques ?',
                        'type' => 'text',
                    ],
                ],
                'is_active' => true,
                'order' => 3,
            ],
            [
                'specialty' => 'Pédiatrie',
                'title' => 'Questionnaire de pédiatrie',
                'description' => 'Questionnaire pré-consultation pour pédiatrie',
                'questions' => [
                    [
                        'id' => 'child_age',
                        'question' => 'Âge de l\'enfant',
                        'type' => 'number',
                    ],
                    [
                        'id' => 'fever',
                        'question' => 'L\'enfant a-t-il de la fièvre ?',
                        'type' => 'radio',
                        'options' => ['Oui', 'Non'],
                    ],
                    [
                        'id' => 'vaccinations',
                        'question' => 'Les vaccinations sont-elles à jour ?',
                        'type' => 'radio',
                        'options' => ['Oui', 'Non', 'Je ne sais pas'],
                    ],
                    [
                        'id' => 'allergies',
                        'question' => 'Allergies connues de l\'enfant',
                        'type' => 'text',
                    ],
                ],
                'is_active' => true,
                'order' => 4,
            ],
            [
                'specialty' => 'Psychiatrie',
                'title' => 'Questionnaire de psychiatrie',
                'description' => 'Questionnaire pré-consultation pour psychiatrie',
                'questions' => [
                    [
                        'id' => 'mood',
                        'question' => 'Comment décririez-vous votre humeur récente ?',
                        'type' => 'radio',
                        'options' => ['Bonne', 'Variable', 'Basse', 'Très basse'],
                    ],
                    [
                        'id' => 'sleep',
                        'question' => 'Comment dormez-vous ?',
                        'type' => 'radio',
                        'options' => ['Bien', 'Difficulté à s\'endormir', 'Réveils nocturnes', 'Très mal'],
                    ],
                    [
                        'id' => 'anxiety',
                        'question' => 'Ressentez-vous de l\'anxiété ?',
                        'type' => 'radio',
                        'options' => ['Non', 'Parfois', 'Souvent', 'Constamment'],
                    ],
                    [
                        'id' => 'previous_treatment',
                        'question' => 'Avez-vous déjà consulté un psychiatre ou psychologue ?',
                        'type' => 'radio',
                        'options' => ['Oui', 'Non'],
                    ],
                ],
                'is_active' => true,
                'order' => 5,
            ],
        ];

        foreach ($templates as $template) {
            QuestionnaireTemplate::create($template);
        }
    }
}
