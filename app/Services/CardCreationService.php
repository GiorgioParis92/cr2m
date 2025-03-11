<?php

namespace App\Services;

use App\Models\Card;
use App\Models\User;
use SebastianBergmann\CodeCoverage\StaticAnalysis\FileAnalyser;

class CardCreationService
{
    protected $cardCreationRules = [
        [
            'key' => 'validate_mar_2',
            'check_not_null' => true,

            'title' => 'RDV MAR 2 effectué',
            'clients' => [],
            'users' => [1],
            'types_client' => [0],
            'types_user' => [],
            'user_type' => 1,

        ],
        [
            'key' => 'cp',
            'check_not_null' => true,
            // 'value' => '111',

            'title' => 'Code postal mis à jour',
            'user_type' => 1,
        ],
        [
            'key' => 'specific_field_3',
            'value' => 'trigger_value_3',
            'title' => 'Title for Card 3',
            'custom_user_logic' => true,
        ],
    ];

    public function checkAndCreateCard($key, $value, $dossier, $authUserId)
    {
    
       
        foreach ($this->cardCreationRules as $rule) {
            if ($rule['key'] === $key) {
            
                if (isset($rule['check_not_null']) && $rule['check_not_null']) {
                    if ($value === null || $value === '') {
                        continue;
                    }
                } elseif (isset($rule['value']) && $rule['value'] !== $value) {
              
                    continue;
                }

                $title = $rule['title'];
                $assignedUsers = $this->getAssignedUsers($rule,$dossier);

                if ($title && !empty($assignedUsers)) {
                    $card = Card::create([
                        'title' => $title,
                        'dossier_id' => $dossier->id,
                        'user_id' => $authUserId,
                        'status' => 1,
                    ]);

                    $card->users()->attach($assignedUsers);
                    return $card; // Optionally return the created card
                }

                break;
            }
        }

        return false;
    }

    private function getAssignedUsers($rule,$dossier)
    {
        if (isset($rule['assigned_users'])) {
            return $rule['assigned_users'];
        } elseif (isset($rule['user_type'])) {
            return User::where('type_id', $rule['user_type'])->pluck('id')->toArray();
        } elseif (!empty($rule['custom_user_logic'])) {
            return $this->customUserLogic();
        }

        return [];
    }

    private function customUserLogic()
    {
        return User::where('type_id', 4)->pluck('id')->toArray();
    }
}
