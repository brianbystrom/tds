<?
	include('library.php');
	include('./theme/include/config.inc');

	$name = $_POST['name'];
	$due = $_POST['due-date'];
	$desc = $_POST['description'];
	$assigned = $_POST['assigned'];
	$urgency = $_POST['urgency'];

	$astring = '';

	foreach($assigned as $value) {
		$astring .= $value.",";
	}

	$astring = rtrim($astring, ',');

	try {
	        
        $PDO = $conn->prepare("INSERT INTO tasks (name, description, created_by, assigned_to, urgency) VALUES (:name, :desc, 1, :astring, :urgency)");
        $PDO->bindParam(':name', $name, PDO::PARAM_STR);
        $PDO->bindParam(':desc', $desc, PDO::PARAM_STR);
        $PDO->bindParam(':astring', $astring, PDO::PARAM_STR);
        $PDO->bindParam(':urgency', $urgency, PDO::PARAM_STR);
        $PDO->execute();
       

    } catch(PDOException $e) {
        echo 'ERROR: '.$e->getMessage();    
    }

	