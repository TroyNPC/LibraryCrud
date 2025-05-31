<?php
				$start_add = new BookCrud();
		    	if(isset($_POST['addbookBUTTON'])){
		    		$start_add->add_book();
		    		header("Refresh:0");
   					exit; 
		    	}
		    	else if(isset($_POST['editBook'])){
		    		$start_add->edit_book();
		    		header("Refresh:0");		
    				exit; 

		    	}
		    	else if(isset($_POST['deleteBook'])){
		    		$start_add->delete_book();
		    		header("Refresh:0");
   					exit; 
		    	}
		    	else if(isset($_POST['logout'])){
	            session_destroy(); 
	            header("Location: Login.php"); 
	            exit();		    	
        		}
?>