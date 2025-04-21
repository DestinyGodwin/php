<?php

namespace App\Models;

class VirtualAccount
{
    private $accountLabel;
    private $bvn;
    private $nin;
    private $phoneNumber;
    private $email;
    
    // Response properties
    private $accountName;
    private $account;
    private $bankName;

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function validate()
    {
        $errors = [];
        
        // Required field validation
        if (empty($this->accountLabel)) {
            $errors[] = "Account label is required";
        }
        
        // At least one identification method should be provided
        if (empty($this->bvn) && empty($this->nin) && empty($this->phoneNumber) && empty($this->email)) {
            $errors[] = "At least one identification method (BVN, NIN, Phone Number, or Email) is required";
        }
        
        return $errors;
    }

    public function toArray()
    {
        return [
            'accountLabel' => $this->accountLabel,
            'bvn' => $this->bvn,
            'nin' => $this->nin,
            'phoneNumber' => $this->phoneNumber,
            'email' => $this->email
        ];
    }
    
    public function setResponseData($data)
    {
        $this->accountName = $data['accountName'] ?? null;
        $this->account = $data['account'] ?? null;
        $this->bankName = $data['bankName'] ?? null;
    }
    
    public function getResponseData()
    {
        return [
            'accountLabel' => $this->accountLabel,
            'accountName' => $this->accountName,
            'account' => $this->account,
            'bankName' => $this->bankName
        ];
    }
}