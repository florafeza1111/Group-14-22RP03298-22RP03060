<?php
require 'vendor/autoload.php';


use AfricasTalking\SDK\AfricasTalking;

class sms {
    protected $phone;
    protected $AT;

    function __construct($phone) {
        // Initialize Africa's Talking gateway with correct credentials
        $this->phone = $phone;
        $this->AT = new AfricasTalking('sandbox', 'atsk_c3ace72a20c4e624c4d55b8925dc39c354a684156a431f01ec38bfb612a5926236c85aca');
    }

    public function sendSMS($message, $recipients,$from) {
        $sms = $this->AT->sms();

        try {
            // Don't include 'from' in sandbox
            $result = $sms->send([
                'to'      => $recipients,
                'message' => $message
            ]);
            return $result;
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}

?>