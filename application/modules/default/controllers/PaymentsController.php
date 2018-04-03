<?php

Zend_Loader::loadClass('Credits');
Zend_Loader::loadClass('Custom_Controller_Action');

Zend_Loader::loadFile('PayPal/services/PayPalAPIInterfaceService/PayPalAPIInterfaceServiceService.php', null, true);
Zend_Loader::loadFile('PayPal/PPLoggingManager.php', null, true);

class PaymentsController extends Custom_Controller_Action {
        
    private $objCredits;
    
	public function init() {
		parent::init();
        
        $this->objCredits = new Credits();
	}
    
    function paypalpaymentAction(){
        if(!$this->_request->getParam('package') > 0)
            throw new Exception('Missing param package');
        
        $logger = new PPLoggingManager('SetExpressCheckout');
        $package = $this->objDictionary->packages->id($this->_request->getParam('package'));

        $currencyCode = 'USD';
        $itemAmount = $package->amount;
        $itemName = $package->description;
        $itemQuantity = 1;

        $shippingTotal = new BasicAmountType($currencyCode, 0);
        $handlingTotal = new BasicAmountType($currencyCode, 0);
        $insuranceTotal = new BasicAmountType($currencyCode, 0);

        $itemAmount = new BasicAmountType($currencyCode, $itemAmount);
        $itemTotalValue = $itemAmount->value * $itemQuantity;
        $taxTotalValue = 0 * $itemQuantity;
        $orderTotalValue = $shippingTotal->value + $handlingTotal->value + 
                            $insuranceTotal->value + $itemTotalValue + $taxTotalValue; 

        $itemDetails = new PaymentDetailsItemType();
        $itemDetails->Name = $itemName;
        $itemDetails->Amount = $itemAmount;
        $itemDetails->Quantity = $itemQuantity;
        $itemDetails->ItemCategory = 'Physical';
        $itemDetails->Tax = new BasicAmountType($currencyCode, 0);

        //$address = new AddressType();
        //$address->CityName = $itemQuantity;
        //$address->Name = $_REQUEST['name'];
        //$address->Street1 = $_REQUEST['street'];
        //$address->StateOrProvince = $_REQUEST['state'];
        //$address->PostalCode = $_REQUEST['postalCode'];
        //$address->Country = $_REQUEST['countryCode'];
        //$address->Phone = $_REQUEST['phone'];

        $PaymentDetails = new PaymentDetailsType();
        $PaymentDetails->PaymentDetailsItem[0] = $itemDetails;
        //$PaymentDetails->ShipToAddress = $address;
        $PaymentDetails->ItemTotal = new BasicAmountType($currencyCode, $itemTotalValue);
        $PaymentDetails->OrderTotal = new BasicAmountType($currencyCode, $orderTotalValue);
        $PaymentDetails->TaxTotal = new BasicAmountType($currencyCode, $taxTotalValue);
        $PaymentDetails->PaymentAction = 'Order';

        $PaymentDetails->HandlingTotal = $handlingTotal;
        $PaymentDetails->InsuranceTotal = $insuranceTotal;
        $PaymentDetails->ShippingTotal = $shippingTotal;

        $setECReqDetails = new SetExpressCheckoutRequestDetailsType();
        $setECReqDetails->PaymentDetails[0] = $PaymentDetails;
        $setECReqDetails->CancelURL = $this->objMainConfig->payments->paypal->cancelurl;
        $setECReqDetails->ReturnURL = $this->objMainConfig->payments->paypal->successurl;

        // Shipping details
        $setECReqDetails->NoShipping = 1;
        $setECReqDetails->AddressOverride = 0;
        $setECReqDetails->ReqConfirmShipping = 0;

        // Billing agreement
        $billingAgreementDetails = new BillingAgreementDetailsType('None');
        $billingAgreementDetails->BillingAgreementDescription = '';
        $setECReqDetails->BillingAgreementDetails = array($billingAgreementDetails);

        // Display options
        $setECReqDetails->cppheaderimage = $this->objMainConfig->payments->paypal->display->cppheaderimage;
        $setECReqDetails->cppheaderbordercolor = $this->objMainConfig->payments->paypal->display->cppheaderbordercolor;
        $setECReqDetails->cppheaderbackcolor = $this->objMainConfig->payments->paypal->display->cppheaderbackcolor;
        $setECReqDetails->cpppayflowcolor = $this->objMainConfig->payments->paypal->display->cpppayflowcolor;
        $setECReqDetails->cppcartbordercolor = $this->objMainConfig->payments->paypal->display->cppcartbordercolor;
        $setECReqDetails->cpplogoimage = $this->objMainConfig->payments->paypal->display->cpplogoimage;
        $setECReqDetails->PageStyle = $this->objMainConfig->payments->paypal->display->pagestyle;
        $setECReqDetails->BrandName = $this->objMainConfig->payments->paypal->display->brandname;

        // Advanced options
        $setECReqDetails->AllowNote = 0;

        $setECReqType = new SetExpressCheckoutRequestType();
        $setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;
        $setECReq = new SetExpressCheckoutReq();
        $setECReq->SetExpressCheckoutRequest = $setECReqType;

        $paypalService = new PayPalAPIInterfaceServiceService();
        $setECResponse = $paypalService->SetExpressCheckout($setECReq);

        if($setECResponse->Ack == 'Success'){
            $errors = (!empty($setECResponse->errors)) ? serialize($setECResponse->errors) : null;
            $this->objCredits->addTransactionPayPal($this->varUserData['id'], $package->id, $setECResponse->Token, $setECResponse->CorrelationID, $errors, $setECResponse->Version, $setECResponse->Build);
            $this->_redirect('https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token='.$setECResponse->Token);
        }
    }
    
    function paypalnotifyAction(){

    }
    
    function paypalsuccessAction(){
        $logger = new PPLoggingManager('GetExpressCheckout');
        if($transaction = $this->objCredits->getTransactionPayPal($this->varUserData['id'], $this->varParams['token'])){
            $getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($transaction['token']);

            $getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
            $getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

            $paypalService = new PayPalAPIInterfaceServiceService();
            $getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
            
            if($getECResponse->Ack == 'Success' && $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerStatus == 'verified'){
                $this->objDB->beginTransaction();
                
                $varIdCreditsTransactions = $this->objCredits->addTransaction($transaction['id_users'], $transaction['credits'], 1, 1, 2);
                $this->objCredits->updTransactionPayPal(
                    $transaction['id'], 
                    $transaction['id_users'], 
                    $varIdCreditsTransactions, 
                    $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Payer, 
                    $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID, 
                    $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerName->FirstName, 
                    $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerName->LastName, 
                    serialize($getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Address)
                );
                
                $this->objDB->commit();
            }
        }
    }
    
    function paypalcancelAction(){

    }
  
}
?>