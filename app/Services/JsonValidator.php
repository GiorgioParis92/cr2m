<?php

namespace App\Services;

class JsonValidator
{
    /**
     * Decodes JSON and extracts invalid groups.
     *
     * @param string $json
     * @return array
     */
    public function getInvalidGroups(string $json): array
    {
        $decodedData = json_decode($json, true);

        if (!$decodedData || empty($decodedData['output_0']['data']['comparison_results']['grouping'])) {
            return [];
        }

        $grouping = $decodedData['output_0']['data']['comparison_results']['grouping'];
        $invalidGroups = [];

        foreach ($grouping as $groupId => $groupDetails) {
            if (isset($groupDetails['valid']) && !$groupDetails['valid']) {
                $invalidGroups[] = [
                    'id' => $groupId,
                    'display_name' => $groupDetails['display_name'],
                    'error' => $groupDetails['error'] ?? 'Erreur inconnue',
                    'groups' => $groupDetails['groups']
                ];
            }
        }

        return $invalidGroups;
    }
}
