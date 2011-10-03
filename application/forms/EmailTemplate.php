<?php

class Application_Form_EmailTemplate extends Zend_Form
{
    public function init()
    {
        $this->setName('EmailTemplate');
		$EmailTemplateID = new Zend_Form_Element_Hidden("EmailTemplateID");
		$EmailTemplateID->addFilter('Int');

		$EmailTemplateSubject = new Zend_Form_Element_Text("EmailTemplateSubject");
		$EmailTemplateSubject->setLabel("EmailTemplate Subject")
					 ->setRequired(true)
					 ->setAttrib("maxlength", "500")
					 ->setAttrib("size", "40")
					 ->addFilter("StripTags")
					 ->addFilter("StringTrim")
					 ->addValidator("NotEmpty");
					 
		$EmailTemplateContent = new Zend_Form_Element_Text("content");
		$EmailTemplateContent->setLabel("Email Template Content")
				 ->setRequired(true)
				 ->addFilter("StringTrim")
				 ->addValidator("NotEmpty");
				 
		$EmailTemplateStatus = new Zend_Form_Element_Select('EmailTemplateStatus');		
		$EmailTemplateStatus->setLabel('Email Template Status')
			          ->setRequired(true) 
					  ->setMultiOptions(array(
						 Application_Model_DbTable_HOs::HO_UPLOADED=> Application_Model_DbTable_HOs::HO_UPLOADED,
						 Application_Model_DbTable_HOs::HO_RECEIVED=> Application_Model_DbTable_HOs::HO_RECEIVED,
						 Application_Model_DbTable_HOs::HB_COMPLETED=> Application_Model_DbTable_HOs::HB_COMPLETED,
						 Application_Model_DbTable_HOs::HO_CLOSED=> Application_Model_DbTable_HOs::HO_CLOSED))
					  ->addFilter('StripTags')
					  ->addFilter('StringTrim');		 
				 			
		$EmailTemplateIsActive = new Zend_Form_Element_CheckBox("EmailTemplateIsActive");	
		$EmailTemplateIsActive->setLabel("Email template Is active")
					 ->setUncheckedValue("N")
					 ->setCheckedValue("Y");
					 
		$submit = new Zend_Form_Element_Submit("submit");
		$submit->setAttrib("id","submitbutton");
		
		$hash = new Zend_Form_Element_Hash('hash', 'no_csrf_foo', array('salt' => '~!@#$%^&*D@ngha123456')); 
		$hash->setDecorators(array( 
                     array('ViewHelper', array('helper' => 'formHidden')) 
                 ));
				 
		$this->addElements(array($EmailTemplateSubject, $EmailTemplateContent, $EmailTemplateStatus, $EmailTemplateIsActive, $submit, $EmailTemplateID, $hash));
    }
}

