<?php

class Application_Form_SendEmailRequest extends Zend_Form
{

    public function init()
    {
		$this->setName("SendEmailRequest");
				
		$YourEmailAddress = new Zend_Form_Element_Text("YourEmailAddress");
		$YourEmailAddress->setLabel("Your Email")
					 ->setRequired(true)
					 ->setAttrib("maxlength", "100")
					 ->setAttrib("size", "70")
					 ->addFilter("StripTags")
					 ->addFilter("StringTrim")
					 ->addValidator("NotEmpty")
					 ->addValidator('EmailAddress'); 
					 
		$YourEmailSubject = new Zend_Form_Element_Text("YourEmailSubject");
		$YourEmailSubject->setLabel("Your Email Subject")
					 ->setRequired(true)
					 ->setAttrib("maxlength", "100")
					 ->setAttrib("size", "70")
					 ->addFilter("StripTags")
					 ->addFilter("StringTrim")
					 ->addValidator("NotEmpty");
					 
		$YourEmailBody = new Zend_Form_Element_Textarea('YourEmailBody');
        $YourEmailBody->setLabel('Your Email Body')
					->setRequired(true)
                    ->setAttrib('rows','10')
                    ->setAttrib('cols','70')                 
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->addValidator('NotEmpty');
					
		$captcha = new Zend_Form_Element_Captcha(  
        'captcha', array('label' => 'Protect key',  'captcha' => array(
							'captcha' => 'Image',  
							'wordLen' => 5, 'timeout' => 300,
							'width' => 182, 'height' => 40,
							'fontSize' =>19, 'lineNoiseLevel'=>2,
							'font' => APPLICATION_PATH . '/../public/captcha/fonts/arial.ttf',  
							'imgDir' => APPLICATION_PATH . '/../public/captcha/images/',
							'imgUrl' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/captcha/images/')));  
								
		$submit = new Zend_Form_Element_Submit("submit");
		$submit->setAttrib("id","submitbutton");
		
		$hash = new Zend_Form_Element_Hash('hash', 'no_csrf_foo', array('salt' => '~!@#$%^&*D@ngha123456')); 
		$hash->setDecorators(array( 
                     array('ViewHelper', array('helper' => 'formHidden')) 
                 ));
				 
		$this->addElements(array($YourEmailAddress, $YourEmailSubject, $YourEmailBody, $captcha, $submit, $hash));
    }
}
?>

