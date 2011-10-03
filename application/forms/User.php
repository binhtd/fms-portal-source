<?php

class Application_Form_User extends Zend_Form
{
	const UPLOAD_DIRECTORY_LABEL = "Best Practice is to use The same Name as User Login Name";
    public function init()
    {
        $this->setName('User');
		$UserID = new Zend_Form_Element_Hidden("UserID");
		$UserID->addFilter('Int');
		
		$UserName = new Zend_Form_Element_Text("UserName");
		$UserName->setLabel("User Name")
				 ->setRequired(true)
				 ->setAttrib("size", "40")
				 ->setAttrib("style", "width:370px")
				 ->setAttrib("maxlength", "50")
				 ->addFilter("StripTags")	
				 ->addFilter("StringTrim")
				 ->addValidator("NotEmpty");
				 
		$UserEmail = new Zend_Form_Element_Text("UserEmail");
		$UserEmail->setLabel("User Email")
				  ->setRequired(true)
				  ->setAttrib("size", "40")
				  ->setAttrib("style", "width:370px")
				  ->setAttrib("maxlength", "50")
				  ->addFilter("StripTags")
				  ->addFilter("StringTrim")
				  ->addValidator("NotEmpty")
				  ->addValidator('EmailAddress'); 
				  
		$UserLoginName = new Zend_Form_Element_Text("UserLoginName");
		$UserLoginName->setLabel("User Login Name")
					  ->setRequired(true)
					  ->setAttrib("style", "width:204px")
					  ->setAttrib("size", "30")
					  ->setAttrib("maxlength", "30")
					  ->addFilter("StripTags")
					  ->addFilter("StringTrim")
					  ->addValidator("NotEmpty");		
					  
		$UserPassword = new Zend_Form_Element_Password('UserPassword');		
		$UserPassword->setLabel("Password")
				 ->setRequired(true)
				 ->setAttrib("style", "width:204px")
				 ->setAttrib("size", "30")
				 ->setAttrib("maxlength", "30")
				 ->addFilter("StripTags")
				 ->addFilter("StringTrim")
				 ->addValidator('StringLength', false, array(8,30));
				 
		$UserConfirmPassword = new Zend_Form_Element_Password('UserConfirmPassword');		
		$UserConfirmPassword->setLabel("Confirm Password")
				 ->setRequired(true)
				 ->setAttrib("size", "30")
				 ->setAttrib("style", "width:204px")
				 ->setAttrib("maxlength", "30")
				 ->addFilter("StripTags")
				 ->addFilter("StringTrim")
				 ->addValidator('StringLength', false, array(8,30))
				 ->addValidator('Identical', false, array('token' => 'UserPassword'));
				 
				 
        $UserRootUploadDirectory = new Zend_Form_Element_Text("UserRootUploadDirectory");
		$UserRootUploadDirectory->setLabel("Bento Folder Name")
								->setRequired(true)
								->setAttrib("style", "width:370px")
								->setAttrib("size", "40")
								->setAttrib("maxlength", "100")
								->addFilter("StripTags")
								->addFilter("StringTrim")
								->addValidator("NotEmpty")
								->setValue(Application_Form_User::UPLOAD_DIRECTORY_LABEL);
								
		$Role = new Zend_Form_Element_Radio("UserIsClient"); 						
		$Role->setLabel("Roles")
			 ->addMultiOptions(array("Y" => "User Is Client",
								"N" => "User Is Jonckers PM"))
			->setSeparator("")
			->setValue("Y");
		
		$JtepmEmail = new Zend_Form_Element_Text("JtepmEmail");
		$JtepmEmail->setLabel("JTE Project Manager Email")
					  ->setRequired(true)
					  ->setAttrib("size", "40")
					  ->setAttrib("style", "width:370px")
					  ->setAttrib("maxlength", "50")
					  ->addFilter("StripTags")
					  ->addFilter("StringTrim")
					  ->addValidator("NotEmpty")
					  ->addValidator('EmailAddress'); 
			
		$UserIsActive = new Zend_Form_Element_CheckBox("UserIsActive");	
		$UserIsActive->setLabel("User Is Active")
					 ->setUncheckedValue("N")
					 ->setCheckedValue("Y")
					 ->setValue("Y");
					 
		$submit = new Zend_Form_Element_Submit("submit");
		$submit->setAttrib("id","submitbutton");
		
		$hash = new Zend_Form_Element_Hash('hash', 'no_csrf_foo', array('salt' => '~!@#$%^&*D@ngha123456')); 
		$hash->setDecorators(array( 
                     array('ViewHelper', array('helper' => 'formHidden')) 
                 ));
				 
		$this->addElements(array($UserName, $UserEmail, $UserLoginName, $UserPassword, $UserConfirmPassword, $UserRootUploadDirectory, 
				$Role, $JtepmEmail, $UserIsActive, $submit, $UserID, $hash));
    }


}

