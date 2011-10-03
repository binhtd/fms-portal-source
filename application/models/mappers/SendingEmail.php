<?php

class Application_Model_Mapper_SendingEmail{	
	public function sendingEmail($fromEmailAddress, $toEmailAddress, $ccEmailAddress, $bccEmailAddress, $emailSubject, $emailBody){
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);			
		$mail = new Zend_Mail('UTF-8');
				
		$mail->setFrom($fromEmailAddress);		
		$mail->addTo($toEmailAddress);		
		if ($ccEmailAddress != null){
			$mail->addCc($ccEmailAddress);
		}
		if ($bccEmailAddress!=null){
			$mail->addBcc($bccEmailAddress);		
		}
		
		$mail->setSubject($emailSubject);
		$mail->setBodyHtml($emailBody);
		
		$queueAdapterOptions =  array('driverOptions' => array(
								'host'      => $config->resources->db->params->host,
								'username'  => $config->resources->db->params->username,
								'password'  => $config->resources->db->params->password,
								'dbname'    => $config->resources->db->params->dbname,
								'type'      => $config->resources->db->adapter),
								'name' => "SendingEmail");
		$queue = new Zend_Queue('Db', $queueAdapterOptions);
		$queue->send(serialize($mail));
	}
}