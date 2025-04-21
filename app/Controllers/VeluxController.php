<?php

namespace App\Controllers;

use App\Services\VeluxService;

class VeluxController
{
    private $velux;

    public function __construct()
    {
        $this->velux = new VeluxService();
    }

    public function rates()
    {
        echo json_encode($this->velux->getRates());
    }

    public function transactions()
    {
        echo json_encode($this->velux->getTransactions());
    }

    public function sellCrypto()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode($this->velux->createSellCrypto($data));
    }

    public function sellGiftCard()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode($this->velux->createGiftCard($data));
    }

    public function giftCardTransactions()
    {
        echo json_encode($this->velux->getGiftCardTransactions());
    }
}
