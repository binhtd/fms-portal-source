<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        $this->addElement('text', 'username', array(
			'filters'     => array('StringTrim','StringToLower'),
			'validators'  => array(
				array('StringLength', false, array(0, 30)),
				),
				'required' => true,
				'label'	   => 'User Name:',
		));
		
		$this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(8, 30)),
            ),
            'required'   => true,
            'label'      => 'Password:',
        ));

        $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Login',
        ));  

		$hash = new Zend_Form_Element_Hash('hash', 'no_csrf_foo', array('salt' => '~!@#$%^&*D@ngha123456')); 
		$hash->setDecorators(array( 
                     array('ViewHelper', array('helper' => 'formHidden')) 
                 ));
				 
		$this->addElement($hash);			
    }


}

