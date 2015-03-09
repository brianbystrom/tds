<?

	require_once './theme/include/config.inc';

/*////////////////////////////////////////////

library index

	DB Retrieval

		get_surveys($conn,$to,$from,$id)
		get_rank($conn,$id)
		get_team($conn,$id)
		get_team_base($conn,$id)
		get_primary_metrics($conn)
		get_daily_scorecards($conn,$start,$end,$team)
		get_level_1($conn,$start,$end)
		get_level_3($conn,$start,$end,$l1)
		get_name($conn,$cid)
		get_style($conn,$surveys,$score,$metric,$type)

	Stat Calculations

		calc_wtr($surveys)
		calc_nrs($surveys)
		calc_fcr($surveys)
		calc_scorecard_wtr($scorecards)
		calc_scorecard_nrs($scorecards)
		calc_scorecard_fcr($scorecards)

	UI Creation

		create_table($conn,$pstart,$pend,$cstart,$cend,$l1,$team,$cid,$view,$scount)
		create_table_row($conn,$pstart,$pend,$cstart,$cend,$dname,$driver,$cid,$team,$scount)

	Data Manipulation

		val_sort($array,$key,$dir)

*/////////////////////////////////////////////

//////////////////////////////////////////////
// Pulls all tasks from the DB
//////////////////////////////////////////////

	function get_tasks($conn) {

			try {
	        
		        $PDO = $conn->prepare('SELECT * FROM tasks WHERE assigned_to LIKE "%-1-%" OR assigned_to LIKE "%-1-,%"');
		        //$PDO->bindParam(':cid', $id, PDO::PARAM_STR);
		        $PDO->execute();
		        
		        //$PDO->setFetchMode(PDO::FETCH_OBJ);

		

		        $tasks = $PDO->fetchAll();

		    } catch(PDOException $e) {
		        echo 'ERROR: '.$e->getMessage();    
		    }

			return $tasks;
	}

//////////////////////////////////////////////
// Pulls name of person by ID
//////////////////////////////////////////////

	function get_name($conn,$id) {

			try {
	        
		        $PDO = $conn->prepare('SELECT * FROM login WHERE id = :id');;
		        $PDO->bindParam(':id', $id, PDO::PARAM_INT);
		        $PDO->execute();
		        
		        $PDO->setFetchMode(PDO::FETCH_OBJ);

		

		        $person = $PDO->fetch();

		        $name = $person->name;

		    } catch(PDOException $e) {
		        echo 'ERROR: '.$e->getMessage();    
		    }

			return $name;
	}


//////////////////////////////////////////////
// Sorts array by provided key
//////////////////////////////////////////////

	function val_sort($array,$key,$dir) {

	//Loop through and get the values of our specified key
	foreach($array as $k=>$v) {
		$b[] = strtolower($v[$key]);
	}
	

    if($dir == true) { arsort($b); }
    else { asort($b); }
	

	
	foreach($b as $k=>$v) {
		$c[] = $array[$k];
	}
    

	return $c;
}