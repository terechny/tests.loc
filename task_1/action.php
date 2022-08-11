<?php
		 
if( isset( $_POST['text_1'] ) AND isset( $_POST['text_2'] ) ){
		
	$textProcess = new textProcess($_POST['text_1'], $_POST['text_2']);
    $textProcess->compare();
	
	return;
}

class textProcess{
	
	public $oldText;
	public $newText;
	
	public $oldTextArray;
	public $newTextArray;

    public $resultArray = [];
    public $newStr = []; 

    public $totalArray = [];	
	
	function __construct($old, $new){
		
		$this->oldText = $old;
		$this->newText = $new;
	}
	
	public function compare(){
								
		$this->oldTextArray = explode(".", trim($this->oldText, "."));
		$this->newTextArray = explode(".", trim($this->newText, "."));	
		
		$this->stringCompairS();
		
		$this->returnView();
										
	}
	
	public function returnView(){
		
		$html = "";
		
		foreach( $this->totalArray as $key => $str ){
			
			if( $str['status'] == 'created' ){
				
				$html .= "<span class='" .$str['status']. "' data-id='". $key ."' >" . $str['second_version'] . "</span> ";
			}else{
				
				if( $str['status'] == 'changed' ){
					
					$html .= "<span class='" .$str['status']. " before-changed' data-id='". $key ."' id='first-version-". $key ."' >" . $str['first_version'] . "</span> ";
					
				}else{
					
					$html .= "<span class='" .$str['status']. "' data-id='". $key ."' >" . $str['first_version'] . "</span> ";
				} 
											
			}
			
			if($str['status'] == 'changed'){
				
				$html .= "<span class='" .$str['status']. " changed_version' id='second-version-". $key ."' data-id='". $key ."' >" . $str['second_version'] . "</span> ";
				
			}else{
				
				$html .= "<span class='" .$str['status']. " second-version' id='second-version-". $key ."' >" . $str['second_version'] . "</span> ";
			}
            			
			
		}
						
		print $html;
		
	}
	
	public function stringCompairS(){
				
		foreach($this->oldTextArray as $key => $value){
						
			foreach( $this->newTextArray as $key_2 => $value_2 ){
								
				$this->stringCompairSM($value, $value_2, $key, $key_2);
				
			}
		}
		
		$this->stringCompairSMD();
		
		$this->totalArrayBild();
						
	}
		
	public function stringCompairSM($str_1, $str_2, $firstKey, $secondKey){
		
		$sim = similar_text($str_1, $str_2, $perc);
		  
		$this->resultArray[$firstKey]['first_version'] = $str_1;	 
		$this->resultArray[$firstKey]['first_key'] = $firstKey;
		  
        // Процент соспадения строки, при котором предложение можно считать изменённым или уже новым.		  
		if( $perc > 50 ){
					
			$this->resultArray[$firstKey]['second_key'] = $secondKey;		
			$this->resultArray[$firstKey]['second_version'] = $str_2;
			$this->resultArray[$firstKey]['match_percentage'] = $perc;
			
						
			if($perc < 100 ){
				
				$this->resultArray[$firstKey]['status'] = 'changed';				
			}
			
			if($perc == 100 ){
				
				$this->resultArray[$firstKey]['status'] = 'not_changed';			
			}
				
		}
	}
	
	public function totalArrayBild(){
		
		foreach( $this->resultArray as $key => $value ){
											
			if( !isset( $value['second_key'] ) ){
								
				$value['second_version'] = 'none';
				$value['status'] = 'deleted';
				
			}
				
			$value['second_version'] .= ".";
			$value['first_version'] .= ".";
			
						
			$this->totalArray[] = $value;
					
			if( isset( $this->newTextArray[ $key ] ) ){
				
				$this->totalArray[] = [
				
				    'first_version' => 'none',
					'first_key' => 0,
					'second_key' => $key,
					'second_version' => $this->newTextArray[ $key ] . '. ',
					'match_percentage' => 0,
					'status' => 'created'
				
				];
				
				unset($this->newTextArray[ $key ]);
					
			}
					
		}
		
		if( count( $this->newTextArray ) > 0 ){
			
			foreach($this->newTextArray as $key => $value ){
				
				$this->totalArray[] = [
				
				    'first_version' => 'none',
					'first_key' => 0,
					'second_key' => $key,
					'second_version' => $value . '. ',
					'match_percentage' => 0,
					'status' => 'created'
				
				];				
			}		
		}				
	}
	
	public function stringCompairSMD(){
		
		foreach( $this->newTextArray as $key => $value ){
			
			foreach( $this->resultArray as $row ){
				  			
				if( isset( $row['second_key'] ) AND $row['second_key'] == $key ){
					  
					 unset( $this->newTextArray[$key] );
				}
			}
		}
		
	}
}

