<?php

namespace SearchLib;

class SearchLib
{
    // search array of all search terms and filters
    protected $search;
    // return format, accepted values are standard and JSON
    protected $return;
    // ISO 3166-1 alpha-2 country_codes for validation of the country_codes filter
    protected $country_codes = [
        'AF',
        'AX',
        'AL',
        'DZ',
        'AS',
        'AD',
        'AO',
        'AI',
        'AQ',
        'AG',
        'AR',
        'AM',
        'AW',
        'AU',
        'AT',
        'AZ',
        'BS',
        'BH',
        'BD',
        'BB',
        'BY',
        'BE',
        'BZ',
        'BJ',
        'BM',
        'BT',
        'BO',
        'BQ',
        'BA',
        'BW',
        'BV',
        'BR',
        'IO',
        'BN',
        'BG',
        'BF',
        'BI',
        'KH',
        'CM',
        'CA',
        'CV',
        'KY',
        'CF',
        'TD',
        'CL',
        'CN',
        'CX',
        'CC',
        'CO',
        'KM',
        'CG',
        'CD',
        'CK',
        'CR',
        'CI',
        'HR',
        'CU',
        'CW',
        'CY',
        'CZ',
        'DK',
        'DJ',
        'DM',
        'DO',
        'EC',
        'EG',
        'SV',
        'GQ',
        'ER',
        'EE',
        'ET',
        'FK',
        'FO',
        'FJ',
        'FI',
        'FR',
        'GF',
        'PF',
        'TF',
        'GA',
        'GM',
        'GE',
        'DE',
        'GH',
        'GI',
        'GR',
        'GL',
        'GD',
        'GP',
        'GU',
        'GT',
        'GG',
        'GN',
        'GW',
        'GY',
        'HT',
        'HM',
        'VA',
        'HN',
        'HK',
        'HU',
        'IS',
        'IN',
        'ID',
        'IR',
        'IQ',
        'IE',
        'IM',
        'IL',
        'IT',
        'JM',
        'JP',
        'JE',
        'JO',
        'KZ',
        'KE',
        'KI',
        'KP',
        'KR',
        'KW',
        'KG',
        'LA',
        'LV',
        'LB',
        'LS',
        'LR',
        'LY',
        'LI',
        'LT',
        'LU',
        'MO',
        'MK',
        'MG',
        'MW',
        'MY',
        'MV',
        'ML',
        'MT',
        'MH',
        'MQ',
        'MR',
        'MU',
        'YT',
        'MX',
        'FM',
        'MD',
        'MC',
        'MN',
        'ME',
        'MS',
        'MA',
        'MZ',
        'MM',
        'NA',
        'NR',
        'NP',
        'NL',
        'NC',
        'NZ',
        'NI',
        'NE',
        'NG',
        'NU',
        'NF',
        'MP',
        'NO',
        'OM',
        'PK',
        'PW',
        'PS',
        'PA',
        'PG',
        'PY',
        'PE',
        'PH',
        'PN',
        'PL',
        'PT',
        'PR',
        'QA',
        'RE',
        'RO',
        'RU',
        'RW',
        'BL',
        'SH',
        'KN',
        'LC',
        'MF',
        'PM',
        'VC',
        'WS',
        'SM',
        'ST',
        'SA',
        'SN',
        'RS',
        'SC',
        'SL',
        'SG',
        'SX',
        'SK',
        'SI',
        'SB',
        'SO',
        'ZA',
        'GS',
        'SS',
        'ES',
        'LK',
        'SD',
        'SR',
        'SJ',
        'SZ',
        'SE',
        'CH',
        'SY',
        'TW',
        'TJ',
        'TZ',
        'TH',
        'TL',
        'TG',
        'TK',
        'TO',
        'TT',
        'TN',
        'TR',
        'TM',
        'TC',
        'TV',
        'UG',
        'UA',
        'AE',
        'GB',
        'US',
        'UM',
        'UY',
        'UZ',
        'VU',
        'VE',
        'VN',
        'VG',
        'VI',
        'WF',
        'EH',
        'YE',
        'ZM',
        'ZW'
    ];

    public function __construct($search = null, $return = 'standard')
    {
        $this->search = $search;
        $this->return = $return;
    }

    public function sendSearch($url, $body)
    {
        $ch = curl_init($url);
        $jsonDataEncoded = json_encode($body);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        return curl_exec($ch);
    }

    public function complyAdvantage($api_key)
    {
        $url = 'https://api.complyadvantage.com/searches?api_key='.$api_key;
        $optionals = [
            'client_ref' => 'string',
            'search_profile' => 'string',
            'fuzziness' => 'float',
            'offset' => 'integer',
            'limit' => 'integer',
            'share_url' => 'integer',
            'exact_match' => 'boolean',
            'tags' => 'array'
        ];
        $filters = [
            'types' => 'array',
            'remove_deceased' => 'integer',
            'birth_year' => 'integer',
            'country_codes' => 'array',
            'entity_type' => 'string'
        ];
        // ensure there is a valid search to be made.
        // either a search string for a company, or if searching for a person, a mimimum of a last name.
        if (array_key_exists('search', $this->search)) {
            $body = array();
            // first validate the search term to make sure it'll work.
            $valid_responce = $this->validateSearch('search', 'string');
            if ($valid_responce['success'] != true) {
                $return = ['code' => 400, 'message' => $valid_responce['reason'],'status': 'failure'];
                if ($this->return == 'JSON') {
                    $return = json_encode($return);
                }
                return $return;
            }
            // now set it into the body to be sent.
            $body['search_term'] = $this->search['search'];
        } elseif (array_key_exists('last_name', $this->search)) {
            // if we are using the last name route, the search term will be an array.
            $body['search_term'] = array();
            $valid_responce = $this->validateSearch('last_name', 'string');
            if ($valid_responce['success'] != true) {
                $return = ['code' => 400, 'message' => $valid_responce['reason'], 'status': 'failure'];
                if ($this->return == 'JSON') {
                    $return = json_encode($return);
                }
                return $return;
            }
            $body['search_term']['last_name'] = $this->search['last_name'];
            // check if there are middle and first names set for the search.
            if (array_key_exists('middle_names', $this->search)) {
                $valid_responce = $this->validateSearch('middle_names', 'string');
                if ($valid_responce['success'] == true) {
                    $body['search_term']['middle_names'] = $this->search['middle_names'];
                } else {
                    $return = ['code' => 400, 'message' => $valid_responce['reason'], 'status': 'failure'];
                    if ($this->return == 'JSON') {
                        $return = json_encode($return);
                    }
                    return $return;
                }
            }
            if (array_key_exists('first_name', $this->search)) {
                $valid_responce = $this->validateSearch('first_name', 'string');
                if ($valid_responce['success'] == true) {
                    $body['search_term']['first_name'] = $this->search['first_name'];
                } else {
                    $return = ['code' => 400, 'message' => $valid_responce['reason'], 'status': 'failure'];
                    if ($this->return == 'JSON') {
                        $return = json_encode($return);
                    }
                    return $return;
                }
            }
        } else {
            // if there isn't a search term set, we can't make a search.
            $return = ['code' => 400, 'message' => 'no search term set', 'status': 'failure'];
            if ($this->return == 'JSON') {
                $return = json_encode($return);
            }
            return $return;
        }
        // now we add in the optional fields
        foreach ($optionals as $optional => $type) {
            if (array_key_exists($optional, $this->search)) {
                $valid_responce = $this->validateSearch($optional, $type);
                if ($valid_responce['success'] == true) {
                    $body[$optional] = $this->search[$optional];
                }
            }
        }
        // and the filters
        foreach ($filters as $filter => $type) {
            if ($filter == 'types' && array_key_exists('search_profile', $body)) {
                continue;
            }
            if (array_key_exists($filter, $this->search)) {
                $valid_responce = $this->validateSearch($optional, $type);
                if ($valid_responce['success'] == true) {
                    $body['filters'][$filter] = $this->search[$filter];
                }
            }
        }
        // now perferm the search and get the result
        $result = json_decode($this->sendSearch($url, $body));
        if ($result->status != 'success') {
            //oh no, something went wrong.
            // we will return the result as that will contain all of the information about what went wrong.
            if ($this->return == 'JSON') {
                $result = json_encode($result);
            }
            return $result;
        } else {
            $data = $result->content->data;
            // format result to resemble laravel paginated return, just without the page links
            $formatted_result = array();
            $formatted_result['total'] = $data->total_hits;
            if ($this->search['limit']) {
                $formatted_result['per_page'] = $this->search['limit'];
            } else {
                $formatted_result['per_page'] = 100;
            }
            if ($this->search['offset']) {
                $formatted_result['from'] = $this->search['offset'] + 1;
            } else {
                $formatted_result['from'] = 1;
            }
            $formatted_result['to'] = $formatted_result['from'] + $formatted_result['per_page'] - 1;
            $formatted_result['data'] = $data->hits;
            if ($this->return == 'JSON') {
                $return = json_encode($formatted_result);
            }
            return $formatted_result;
        }
    }

    public function newSearch($search)
    {
        $this->search = $search;
    }

    public function changeReturn($return)
    {
        $this->return = $return;
    }

    private function validateSearch($field, $type)
    {
        $return = array();
        $return['success'] = true;
        $return['reason'] = null;
        switch ($type) {
            case 'string':
                if (!is_string($this->search[$field])) {
                    $return['success'] = false;
                    $return['reason'] = $field.' is not a string';
                } elseif (strlen($this->search[$field]) > 255 || strlen($this->search[$field] < 1)) {
                    $return['success'] = false;
                    $return['reason'] = $field.' term is not within the allowed number of characters range';
                }
                break;
            case 'integer':
                if (!is_int($this->search[$field])) {
                    if (is_numeric($this->search[$field])) {
                        $this->search[$field] = intval($this->search[$field]);
                    } else {
                        $return['success'] = false;
                        $return['reason'] = $field.' is not an integer';
                    }
                }
                break;
            case 'boolean':
                if (!is_bool($this->search[$field])) {
                    // convert it into boolean.
                    // this will match most things, like "yes" or "on" with true
                    // and anything it can't match like "string" or NULL will be set to false
                    $this->search[$field] = $this->isTrue($this->search[$field])
                }
                break;
            case 'float':
                if (!is_float($this->search[$field])) {
                    if (is_numeric($this->search[$field])) {
                        $this->search[$field] = floatval($this->search[$field]);
                    } else {
                        $return['success'] = false;
                        $return['reason'] = $field.' is not an integer';
                    }
                }
                break;
            case 'array':
                if (!is_array($this->search[$field])) {
                    $return['success'] = false;
                    $return['reason'] = $field.' is not an array';
                }
                break;
            default:
                // code...
                break;
        }
        // now we do some bespoke validation on fields that have to match specific values.
        switch ($field) {
            case 'fuzziness':
                if ($this->search[$field] > 1.0) {
                    $return['success'] = false;
                    $return['reason'] = $field.' exceeds maximum value';
                } elseif ($this->search[$field] < 0.0) {
                    $return['success'] = false;
                    $return['reason'] = $field.' is below the minimum value';
                }
                break;
            case 'limit':
                if ($this->search[$field] > 100) {
                    $return['success'] = false;
                    $return['reason'] = $field.' exceeds maximum value';
                }
                break;
            case 'share_url':
            case 'remove_deceased':
                if ($this->search[$field] > 1) {
                    $return['success'] = false;
                    $return['reason'] = $field.' exceeds maximum value';
                } elseif ($this->search[$field] < 0) {
                    $return['success'] = false;
                    $return['reason'] = $field.' is below the minimum value';
                }
                break;
            case 'types':
                $accepted_types = [
                    'sanction',
                    'warning',
                    'fitness-probity',
                    'pep',
                    'pep-class-1',
                    'pep-class-2',
                    'pep-class-3',
                    'pep-class-4',
                    'adverse-media',
                    'adverse-media-financial-crime',
                    'adverse-media-violent-crime',
                    'adverse-media-sexual-crime',
                    'adverse-media-terrorism',
                    'adverse-media-fraud',
                    'adverse-media-narcotics',
                    'adverse-media-general'
                ];
                foreach ($this->search[$field] as $key => $value) {
                    if (!in_array($value, $accepted_types)) {
                        unset($this->search[$field][$key]);
                    }
                }
                break;
            case 'birth_year':
                if (strtotime($this->search[$field])) {
                    $start_year = strtotime(date('Y') - 120);
                    $end_year = strtotime(date('Y'));
                    $received_year = strtotime($this->search[$field]);
                    if (!(($received_year >= $start_year) && ($received_year <= $end_year))) {
                        $return['success'] = false;
                        $return['reason'] = $field.' is not a valid birth year';
                    }
                } else {
                    $return['success'] = false;
                    $return['reason'] = $field.' is not a valid birth year';
                }

                break;
            case 'country_codes':
                foreach ($this->search[$field] as $key => $value) {
                    if (!in_array($value, $this->country_codes)) {
                        unset($this->search[$field][$key]);
                    }
                }
                break;
            case 'entity_type':
                $accepted_types = ['person', 'company', 'organisation', 'vessel', 'aircraft'];
                foreach ($this->search[$field] as $key => $value) {
                    if (!in_array($value, $accepted_types)) {
                        unset($this->search[$field][$key]);
                    }
                }
                break;
            default:
                // code...
                break;
        }
    }

    private function isTrue($val)
    {
        return (is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN) : (bool) $val);
    }
}
