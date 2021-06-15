<?php

namespace App\Helper;

/**
 * Class DataHelper
 * @package App\Helper
 */
class DataHelper
{

    public function getLanguages()
    {
        return [
            "French" => "fr",
            "English" => "en",
        ];
    }

    /**
     * @return string[]
     */
    public function getPaymentChoice()
    {
        return [
            "Carte bancaire nationale (2.60 % de frais bancaires)" => "2.60",
            "Carte bancaire internationale (3.60 % de frais bancaires)" => "3.60",
        ];
    }

    /**
     * @return string[]
     */
    public function getCompanyDocumentTypes()
    {
        return [
            "Attestation RIB" => "Attestation RIB",
            "Autorisation SGG" => "Autorisation SGG",
            "Présentation association" => "Présentation association",
            "Autres" => "Autres"
        ];
    }

    public function getDurations()
    {
        return [
            "1" . " " . "month" => 1,
            "2" . " " . "month" => 2,
            "3" . " " . "month" => 3,
            "4" . " " . "month" => 4,
            "5" . " " . "month" => 5,
            "6" . " " . "month" => 6
        ];
    }

}