<?php
require_once APPLICATION_PATH. '/../library/Export/PHPExcel.php';

class Application_Model_Mapper_ExportPageTranslationTracking extends Application_Model_Mapper_PageTranslationTracking{
	public function ExportToExcel(){
		$objPHPExcel = new PHPExcel();
		$startColumn = 0;
		$startRow = 1;
		$objPHPExcel->getProperties()->setCreator("BinhTD")
									 ->setLastModifiedBy("BinhTD")
									 ->setTitle("Office 2007 XLSX")
									 ->setSubject("Office 2007 XLSX")
									 ->setDescription("Generate when using bento portal")
									 ->setKeywords("bento portal")
									 ->setCategory("localization");		
		$this->setExcelHeaderColumn($objPHPExcel, $startColumn, $startRow);	
		$objPHPExcel->getActiveSheet()->setTitle('Page Translation Tracking');
		$this->setExcelExportDataInRange($objPHPExcel, $startColumn, $startRow);						
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=". "XLSX_Export_Page_Translation_Tracking" . date("Y-m-d : H:i:s", time()) . ".xlsx");
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	private function setExcelHeaderColumn($objPHPExcel, $startColumn, $startRow){
		$languageMapper = new Application_Model_Mapper_Language();
		$languages = $languageMapper->getTargetLanguageActive();
		
		$sharedStyle1 = new PHPExcel_Style();
		$sharedStyle1->applyFromArray(
		array('fill' 	=> array(
									'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
									'color'		=> array('argb' => 'FFCCFFCC')
								),
			  'borders' => array(
									'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
									'right'		=> array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
								)
			 ));
			 
		$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" .$startRow. ":FZ" .$startRow);
		$objPHPExcel->getActiveSheet()
					->setCellValueByColumnAndRow( $startColumn++, $startRow, "Page/URL");
		
		$objPHPExcel->getActiveSheet()			
					->setCellValueByColumnAndRow( $startColumn++, $startRow, "Language");
							
		$objPHPExcel->getActiveSheet()			
					->setCellValueByColumnAndRow( $startColumn++, $startRow, "Number of Visits");		
	}
	
	private function setExcelExportDataInRange($objPHPExcel, $startColumn, $startRow){
		$rows = $this->getExportTranslationTracking();
		$languages = Zend_Registry::get('languages'); 
		$column = $startColumn;
		
		foreach($rows as $row){						
			$startRow++;
			$objPHPExcel->getActiveSheet()
					->setCellValueByColumnAndRow( $startColumn++, $startRow, $row["PageUrl"]);
					
			$objPHPExcel->getActiveSheet()
					->setCellValueByColumnAndRow( $startColumn++, $startRow, $languages[$row["TranslationLanguage"]]);					
			$objPHPExcel->getActiveSheet()
					->setCellValueByColumnAndRow( $startColumn++, $startRow, $row["CountTranslation"]);	
			$startColumn = $column;			
		}													
	}

	private function getExportTranslationTracking(){		
		$select = parent::getDbTable()->getAdapter()->select();	
		$rows = parent::getDbTable()->getAdapter()
									->fetchAll($select->from("pagetranslationtracking", 
															  array("PageUrl", "TranslationLanguage", 		"CountTranslation" =>'count("pagetranslationtracking")'))
													  ->group(array("PageUrl", "TranslationLanguage")));		
		return $rows;
	}
}