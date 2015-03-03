<?

function get_tasks($conn) {

		try {
        
	        $PDO = $conn->prepare('SELECT * FROM tasks');
	        //$PDO->bindParam(':cid', $id, PDO::PARAM_STR);
	        $PDO->execute();
	        
	        //$PDO->setFetchMode(PDO::FETCH_OBJ);

	

	        $tasks = $PDO->fetchAll();

	    } catch(PDOException $e) {
	        echo 'ERROR: '.$e->getMessage();    
	    }

		return $tasks;
}
