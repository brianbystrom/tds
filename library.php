<?

	require_once 'config.inc';

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
// Retrieves surveys by agents for any team.
//////////////////////////////////////////////

	function get_surveys($conn,$to,$from,$id) {

		$team = get_team_base($conn,$id);

		//print_r($team);

		$metrics = get_primary_metrics($conn);
		$sql = '';
		$x = 1;

		if(count($team) == 1) {
			$sql = "rep_id = '".$team[0]."'";
		} else {

			foreach($team AS $member) {
				if(count($team) != $x) {
					$sql .= "rep_id = '".$member."' OR ";
				} else {
					$sql .= "rep_id = '".$member."'";
				}
				$x++;
			}
		}

		try {
        
	        $PDO = $conn->prepare('SELECT rep_id,level_1,metric_1,metric_2,metric_3 FROM survey_data WHERE ('.$sql.')');
	        //$PDO->bindParam(':cid', $id, PDO::PARAM_STR);
	        $PDO->execute();
	        
	        //$PDO->setFetchMode(PDO::FETCH_OBJ);

	        $x = 0;

	        $surveys = $PDO->fetchAll();

	    } catch(PDOException $e) {
	        echo 'ERROR: '.$e->getMessage();    
	    }

	    return $surveys;

	}

//////////////////////////////////////////////
// Returns rank by a given id.
//////////////////////////////////////////////

	function get_rank($conn,$id) {

		// Pull rank by id from db.

		try {
        
	        $PDO = $conn->prepare('SELECT rank FROM roster WHERE cid = :cid');
	        $PDO->bindParam(':cid', $id, PDO::PARAM_STR);
	        $PDO->execute();
	        
	        $PDO->setFetchMode(PDO::FETCH_OBJ);

	        $x = 0;

	        if($row = $PDO->fetch()) {
	        	$rank = $row->rank;
	        } else {
	        	$rank = 'none';
	        }

	    } catch(PDOException $e) {
	        echo 'ERROR: '.$e->getMessage();    
	    }

	    return $rank;
	}

//////////////////////////////////////////////
// Get members of team by id.
//////////////////////////////////////////////

	function get_team($conn,$id) {

		$rank = get_rank($conn,$id);
		$count = 0;

		try {
        
	        $PDO = $conn->prepare('SELECT cid FROM roster WHERE manager = :cid');
	        $PDO->bindParam(':cid', $id, PDO::PARAM_STR);
	        $PDO->execute();
	        
	        $PDO->setFetchMode(PDO::FETCH_OBJ);

	        while($row = $PDO->fetch()) {
	        	$team[] = $row->cid;
	        	$count++;
	        }

	    } catch(PDOException $e) {
	        echo 'ERROR: '.$e->getMessage();    
	    }

	    if($count == 0) { $team = 'empty'; }

	    return $team;

	}

//////////////////////////////////////////////
// Retrieves full team down to agent level.
//////////////////////////////////////////////

	function get_team_base($conn,$id) {

		// Pull rank to determine iterations.

		$rank = get_rank($conn,$id);
		$fteam = array();

		$rank--;

		// If it's rank 1 leader, grab team list and move on,
		// if it's an agent (-1) create array of only that id,
		// otherwise iterate through decrementing rank to gather list of agents.

		if($rank == 0) {
			$team = get_team($conn,$id);
		} elseif ($rank < 0) {
			$team[] = $id;
		} else {

			$team = get_team($conn,$id);

			while($rank > 0) {

				foreach ($team AS $member) {

					//echo $member."<br>";
					$mteam = get_team($conn,$member);

					if($mteam != "empty") {

						foreach ($mteam AS $member2) {

							$fteam[] = $member2;

						}
					}
				}

				//echo "<br><br>";

				$team = $fteam;

				$rank--;
			}

			
		}

		return $team;

	}

//////////////////////////////////////////////
// Returns array of primary metrics.
//////////////////////////////////////////////

	function get_primary_metrics($conn) {

		$x = 0;

		try {
        
	        $PDO = $conn->prepare('SELECT metric_name,mode FROM metrics WHERE type = "primary"');
	        $PDO->execute();
	        
	        $PDO->setFetchMode(PDO::FETCH_OBJ);

	        while($row = $PDO->fetch()) {
	        	$metrics[$x][0] = $row->metric_name;
	        	$metrics[$x][1] = $row->mode;
	        	$x++;
	        }

	    } catch(PDOException $e) {
	        echo 'ERROR: '.$e->getMessage();    
	    }

	    return $metrics;

	}

//////////////////////////////////////////////
// Returns roster based on rank specified
//////////////////////////////////////////////

	function get_roster_by_rank($conn,$min_rank,$max_rank) {

		$x = 0;

		try {
        
	        $PDO = $conn->prepare('SELECT cid,name FROM roster WHERE rank BETWEEN :min_rank AND :max_rank ORDER BY name ASC');
	        $PDO->bindParam(':min_rank',$min_rank,PDO::PARAM_INT);
	        $PDO->bindParam(':max_rank',$max_rank,PDO::PARAM_INT);
	        $PDO->execute();
	        
	        $roster = $PDO->fetchAll();

	    } catch(PDOException $e) {
	        echo 'ERROR: '.$e->getMessage();    
	    }

	    return $roster;

	}

//////////////////////////////////////////////
// Returns array of scorecards for a team at agent level
//////////////////////////////////////////////

	function get_daily_scorecards($conn,$start,$end,$team) {

		$x = 1;
		$sql = '';


		if(count($team) == 1) {
			$sql .= "cid = ".$team[0];
		} else {

			foreach($team AS $member) {
				if(count($team) != $x) {
					$sql .= "cid = '".$member."' OR ";
				} else {
					$sql .= "cid = '".$member."'";
				}
				$x++;
			}
		}


		try {
        
	        $PDO = $conn->prepare('SELECT date,m1_survey,m1_score,m2_survey,m2_score,m3_survey,m3_score FROM daily_scorecard WHERE ('.$sql.') AND date BETWEEN :start AND :end');
	        $PDO->bindParam(':start',$start,PDO::PARAM_STR);
	        $PDO->bindParam(':end',$end,PDO::PARAM_STR);
	        $PDO->execute();
	        
	        $scorecards = $PDO->fetchAll();

	    } catch(PDOException $e) {
	        echo 'ERROR: '.$e->getMessage();    
	    }


	    return $scorecards;

	}

//////////////////////////////////////////////
// Returns distinct level 1 drivers from time period provided.
//////////////////////////////////////////////

	function get_level_1($conn,$start,$end) {
    
    try {
        
        $PDO = $conn->prepare('SELECT DISTINCT level_1 FROM survey_data WHERE (completion_date BETWEEN :start AND :end) ORDER BY level_1 ASC');
        $PDO->bindParam(':start', $start, PDO::PARAM_STR);
        $PDO->bindParam(':end', $end, PDO::PARAM_STR);
        $PDO->execute();
        
        //$PDO->setFetchMode(PDO::FETCH_OBJ);

        $array = $PDO->fetchAll();

    } catch(PDOException $e) {
        echo 'ERROR: '.$e->getMessage();    
    }
    
    return $array;
}

//////////////////////////////////////////////
// Returns distinct level 3 drivers from time period  and level 1 driver provided.
//////////////////////////////////////////////

	function get_level_3($conn,$start,$end,$l1) {
    
    try {
        
        $PDO = $conn->prepare('SELECT DISTINCT level_3 FROM survey_data WHERE (completion_date BETWEEN :start AND :end) AND level_1 = :l1 ORDER BY level_3 ASC');
        $PDO->bindParam(':start', $start, PDO::PARAM_STR);
        $PDO->bindParam(':end', $end, PDO::PARAM_STR);
        $PDO->bindParam(':l1', $l1, PDO::PARAM_STR);
        $PDO->execute();
        
        //$PDO->setFetchMode(PDO::FETCH_OBJ);

        $array = $PDO->fetchAll();

    } catch(PDOException $e) {
        echo 'ERROR: '.$e->getMessage();    
    }
    
    return $array;
}

//////////////////////////////////////////////
// Get name by cid.
//////////////////////////////////////////////

	function get_name($conn,$cid) {
    
    try {
        
        $PDO = $conn->prepare('SELECT name FROM roster WHERE cid = :cid');
        $PDO->bindParam(':cid', $cid, PDO::PARAM_STR);
        $PDO->execute();
        
        $PDO->setFetchMode(PDO::FETCH_OBJ);

        $row = $PDO->fetch();

        $name = $row->name;

    } catch(PDOException $e) {
        echo 'ERROR: '.$e->getMessage();    
    }
    
    return $name;
}

//////////////////////////////////////////////
// Retruns CSS styling for stats and stat trending
//////////////////////////////////////////////

	function get_style($conn,$surveys,$score,$metric,$type) {
    
    try {
        
        $PDO = $conn->prepare('SELECT target FROM metrics WHERE metric_name = :metric');
        $PDO->bindParam(':metric', $metric, PDO::PARAM_STR);
        $PDO->execute();
        
        $PDO->setFetchMode(PDO::FETCH_OBJ);

        $row = $PDO->fetch();

        $target = $row->target;

    } catch(PDOException $e) {
        echo 'ERROR: '.$e->getMessage();    
    }

    $style = '';
    
    if ($type == "stat") {
        if ($score < ($target - 10) && $surveys > '0') { $style = "analyze-red"; }
        elseif ($score > ($target + 10) && $surveys > '0') { $style = "analyze-green"; }
        else { $style = ""; }
    }
    else {
       // if ($wtr > 0) { $style = "mt-delta-trend-up"; }
        //elseif ($wtr < 0) { $style = "mt-delta-trend-down"; }
       // else { $style = ""; }
    }
    
    return $style;
    
}


//////////////////////////////////////////////
// Returns WTR score & surveys.
//////////////////////////////////////////////

	function calc_wtr($surveys) {
	    
	    $wtr_sum = 0;
	    $count = 0;
	    
	    foreach($surveys AS $values) {
	        if($values['metric_1'] < 999) {
	            $wtr_sum += $values['metric_1']; 
	            $count++; 
	        }
	    }
	        
	    if($count == 0) { $true_wtr = 0; }
	    else { $true_wtr = $wtr_sum / $count; }
	    
	    $true_wtr = number_format($true_wtr, 10, '.', '');


	    $array[] = $count;
	    $array[] = $true_wtr;
	    
	    return $array;
	}

//////////////////////////////////////////////
// Returns NRS score & surveys.
//////////////////////////////////////////////

	function calc_nrs($surveys) {
	    
	    $nrs_sum = 0;
	    $count = 0;
	    
	    foreach($surveys AS $values) {
	        if($values['metric_2'] < 999) {
	            $nrs_sum += $values['metric_2']; 
	            $count++; 
	        }
	    }
	    
	    if($count == 0) { $true_nrs = 0; }
	    else { $true_nrs = $nrs_sum / $count; }
	    
	    $true_nrs = number_format($true_nrs, 10, '.', '');
	    
	    $array[] = $count;
	    $array[] = $true_nrs;
	    
	    return $array;
	}

//////////////////////////////////////////////
// Returns FCR score & surveys.
//////////////////////////////////////////////

	function calc_fcr($surveys) {
	    
	    $fcr_sum = 0;
	    $count = 0;
	    
	    foreach($surveys AS $values) {
	        if($values['metric_3'] < 999) {
	            $fcr_sum += $values['metric_3']; 
	            $count++; 
	        }
	    }
	    
	    if($count == 0) { $true_fcr = 0; }
	    else { $true_fcr = $fcr_sum / $count; }
	    
	    $true_fcr = number_format($true_fcr, 10, '.', '');    
	    
	    $array[] = $count;
	    $array[] = $true_fcr;
	    
	    return $array;
	}

//////////////////////////////////////////////
// Returns WTR calculated via scorecards
//////////////////////////////////////////////

	function calc_scorecard_wtr($scorecards) {
	    
	    $wtr_sum = 0;
	    $count = 0;
	    
	    foreach($scorecards AS $values) {
	        if($values['m1_survey'] > 0) {
	            $wtr_sum += $values['m1_score'] * $values['m1_survey']; 
	            $count += $values['m1_survey']; 
	        }
	    }

	        
	    if($count == 0) { $true_wtr = 0; }
	    else { $true_wtr = $wtr_sum / $count; }
	    
	    $true_wtr = number_format($true_wtr, 1, '.', '');


	    $array[] = $count;
	    $array[] = $true_wtr;
	    $array[] = $wtr_sum;
	    
	    return $array;
	}

//////////////////////////////////////////////
// Returns NRS calculated via scorecards
//////////////////////////////////////////////

	function calc_scorecard_nrs($scorecards) {
	   
	    $nrs_sum = 0;
	    $count = 0;
	    
	    foreach($scorecards AS $values) {
	        if($values['m2_survey'] > 0) {
	            $nrs_sum += $values['m2_score'] * $values['m2_survey']; 
	            $count += $values['m2_survey']; 
	        }
	    }
	        
	    if($count == 0) { $true_nrs = 0; }
	    else { $true_nrs = $nrs_sum / $count; }
	    
	    $true_nrs = number_format($true_nrs, 1, '.', '');

	    $array[] = $count;
	    $array[] = $true_nrs;
	    
	    return $array;
	}

//////////////////////////////////////////////
// Returns FCR calculated via scorecards
//////////////////////////////////////////////

	function calc_scorecard_fcr($scorecards) {
	    
	    $fcr_sum = 0;
	    $count = 0;
	    
	    foreach($scorecards AS $values) {
	        if($values['m3_survey'] > 0) {
	            $fcr_sum += $values['m3_score'] * $values['m3_survey']; 
	            $count += $values['m3_survey']; 
	        }
	    }
	    
	    if($count == 0) { $true_fcr = 0; }
	    else { $true_fcr = $fcr_sum / $count; }
	    
	    $true_fcr = number_format($true_fcr, 1, '.', '');    
	    
	    $array[] = $count;
	    $array[] = $true_fcr;
	    
	    return $array;
	}

//////////////////////////////////////////////
// Returns calculated table based on filter
//////////////////////////////////////////////

	function create_table($conn,$pstart,$pend,$cstart,$cend,$l1,$team,$cid,$view,$scount) {

		if($view == '1') { 
			$header = 'Team Member';
			$list = get_team($conn,$cid);
		} else { 
			$header = 'Level 1 Driver'; 
			$list = $l1;
		}


		$table = '';

		$pstart_display = date('m/d/Y', strtotime($pstart));
		$pend_display = date('m/d/Y', strtotime($pend));
		$cstart_display = date('m/d/Y', strtotime($cstart));
		$cend_display = date('m/d/Y', strtotime($cend));

		$name = get_name($conn,$cid);


		$table .= "<table class='table table-striped table-bordered2 table-hover' id='sample_1'>
								<thead>
									<tr>
										<th class='bg-blue-steel'></th>
										<th colspan='1' id='name' class='bg-blue-steel text-center'>
											 ".$name." (".$cid.")
										</th>
										<th colspan='5' id='pdate' class='bg-blue-steel text-center'>
											 ".$pstart_display." - ".$pend_display."
										</th>
										<th colspan='5' id='cdate' class='bg-blue-steel text-center'>
											 ".$cstart_display." - ".$cend_display."
										</th>
										<th colspan='5' class='bg-blue-steel text-center'>
											 Change
										</th>
										<th class='bg-blue-ebonyclay text-center'>
											 <i class='fa fa-cogs'></i>
										</th>
									</tr>
									<tr>
										<th class='text-center'>
											".$header."
										</th>
										<th class='text-center analyze-border-left'>
											Surveys
										</th>
										<th class='text-center'>
											% Vol
										</th>
										<th class='text-center'>
											WTR
										</th>
										<th class='text-center'>
											NRS
										</th>
										<th class='text-center'>
											FCR
										</th>
										<th class='text-center analyze-border-left'>
											Surveys
										</th>
										<th class='text-center'>
											% Vol
										</th>
										<th class='text-center'>
											WTR
										</th>
										<th class='text-center'>
											NRS
										</th>
										<th class='text-center'>
											FCR
										</th>
										<th class='text-center analyze-border-left'>
											Surveys
										</th>
										<th class='text-center'>
											% Vol
										</th>
										<th class='text-center'>
											WTR
										</th>
										<th class='text-center'>
											NRS
										</th>
										<th class='text-center analyze-border-right'>
											FCR
										</th>
										<th class='text-center'>
											
										</th>
									</tr>
								</thead>
								<tbody>";

								if($list != 'empty') {

									foreach ($list AS $value) {

										if($view == '0') {
											$line = create_table_row($conn,$pstart,$pend,$cstart,$cend,$value['level_1'],'level_1',$cid,$team,$scount);
											$item = $value['level_1'];
										} else { 
											$line = create_table_row($conn,$pstart,$pend,$cstart,$cend,$value,'team',$cid,$team,$scount);
											$item = get_name($conn,$value);
											$item .= ' ('.$value.')';	
										}

									$style2 = get_style($conn,$line[0],$line[2],'WTR','stat');
									$style3 = get_style($conn,$line[0],$line[3],'NRS','stat');
									$style4 = get_style($conn,$line[0],$line[4],'FCR','stat');
									$style7 = get_style($conn,$line[5],$line[7],'WTR','stat');
									$style8 = get_style($conn,$line[5],$line[8],'NRS','stat');
									$style9 = get_style($conn,$line[5],$line[9],'FCR','stat');

									$table .= "<tr>
										<td>
											".$item."
										</td>
										<td class='text-center analyze-border-left'>
											".number_format($line[0], 0, '.', '')."
										</td>
										<td class='text-center'>
											".number_format($line[1], 2, '.', '')."
										</td>
										<td class='text-center ".$style2."'>
											".number_format($line[2], 2, '.', '')."
										</td>
										<td class='text-center ".$style3."'>
											".number_format($line[3], 2, '.', '')."
										</td>
										<td class='text-center ".$style4."'>
											".number_format($line[4], 2, '.', '')."
										</td>
										<td class='text-center analyze-border-left'>
											".number_format($line[5], 0, '.', '')."
										</td>
										<td class='text-center'>
											".number_format($line[6], 2, '.', '')."
										</td>
										<td class='text-center ".$style7."'>
											".number_format($line[7], 2, '.', '')."
										</td>
										<td class='text-center ".$style8."'>
											".number_format($line[8], 2, '.', '')."
										</td>
										<td class='text-center ".$style9."'>
											".number_format($line[9], 2, '.', '')."
										</td>
										<td class='text-center analyze-border-left'>
											".number_format($line[10], 0, '.', '')."
										</td>
										<td class='text-center'>
											".number_format($line[11], 2, '.', '')."
										</td>
										<td class='text-center'>
											".number_format($line[12], 2, '.', '')."
										</td>
										<td class='text-center'>
											".number_format($line[13], 2, '.', '')."
										</td>
										<td class='text-center analyze-border-right'>
											".number_format($line[14], 2, '.', '')."
										</td>
										<td class='text-center'>
											<i class='glyphicon glyphicon-comment' onclick='return viewVerbatims(\"".$item."\");'></i>
											<i class='glyphicon glyphicon-list-alt'></i>
										</td>
									</tr>";	

									}
								}

								$table .= "</tbody>
							</table>";

		return $table;


	}

//////////////////////////////////////////////
// Returns individual row for the kpi table
//////////////////////////////////////////////

	function create_table_row($conn,$pstart,$pend,$cstart,$cend,$dname,$driver,$cid,$team,$scount) {

		$metrics = get_primary_metrics($conn);
		$sql = '';
		$x = 1;

		if($driver == 'team') { $team = get_team_base($conn,$dname); }

		if(count($team) == 1) {
			$sql = "rep_id = '".$team[0]."'";
		} else {

			foreach($team AS $member) {
				if(count($team) != $x) {
					$sql .= "rep_id = '".$member."' OR ";
				} else {
					$sql .= "rep_id = '".$member."'";
				}
				$x++;
			}
		}

		if($driver == 'team') {

			try {
	        
		        $PDO = $conn->prepare('SELECT metric_1,metric_2,metric_3 FROM survey_data WHERE ('.$sql.') AND completion_date BETWEEN :start AND :end');
		        $PDO->bindParam(':start', $pstart, PDO::PARAM_STR);
		        $PDO->bindParam(':end', $pend, PDO::PARAM_STR);
		        $PDO->execute();
		        
		        $psurveys = $PDO->fetchAll();

		        $x = 0;

		    } catch(PDOException $e) {
		        echo 'ERROR: '.$e->getMessage();    
		    }

		    try {
        
	        $PDO2 = $conn->prepare('SELECT metric_1,metric_2,metric_3 FROM survey_data WHERE ('.$sql.') AND completion_date BETWEEN :start AND :end');
	        $PDO2->bindParam(':start', $cstart, PDO::PARAM_STR);
	        $PDO2->bindParam(':end', $cend, PDO::PARAM_STR);
	        $PDO2->execute();
	        
	        $csurveys = $PDO2->fetchAll();

	        $x = 0;

		    } catch(PDOException $e) {
		        echo 'ERROR: '.$e->getMessage();    
		    }

		} else {

			try {
	        
		        $PDO = $conn->prepare('SELECT metric_1,metric_2,metric_3 FROM survey_data WHERE ('.$sql.') AND '.$driver.' = "'.$dname.'" AND completion_date BETWEEN :start AND :end');
		        $PDO->bindParam(':start', $pstart, PDO::PARAM_STR);
		        $PDO->bindParam(':end', $pend, PDO::PARAM_STR);
		        $PDO->execute();
		        
		        $psurveys = $PDO->fetchAll();

		        $x = 0;

		    } catch(PDOException $e) {
		        echo 'ERROR: '.$e->getMessage();    
		    }

		    try {
        
	        $PDO2 = $conn->prepare('SELECT metric_1,metric_2,metric_3 FROM survey_data WHERE ('.$sql.') AND '.$driver.' = "'.$dname.'" AND completion_date BETWEEN :start AND :end');
	        $PDO2->bindParam(':start', $cstart, PDO::PARAM_STR);
	        $PDO2->bindParam(':end', $cend, PDO::PARAM_STR);
	        $PDO2->execute();
	        
	        $csurveys = $PDO2->fetchAll();

	        $x = 0;

		    } catch(PDOException $e) {
		        echo 'ERROR: '.$e->getMessage();    
		    }
		}

	    $pwtr = calc_wtr($psurveys);
		$pnrs = calc_nrs($psurveys);
		$pfcr = calc_fcr($psurveys);
		$psurveys = count($psurveys);
		$pvol = $psurveys / $scount;

	    $cwtr = calc_wtr($csurveys);
		$cnrs = calc_nrs($csurveys);
		$cfcr = calc_fcr($csurveys);
		$csurveys = count($csurveys);
		$cvol = $csurveys / $scount;

		$dsurveys = $psurveys - $csurveys;
		$dwtr = $pwtr[1] - $cwtr[1];
		$dnrs = $pnrs[1] - $cnrs[1];
		$dfcr = $pfcr[1] - $cfcr[1];
		$dvol = $pvol - $cvol;

		$array = array();

		$array[] = $psurveys;
		$array[] = $pvol * 100;
		$array[] = $pwtr[1];
		$array[] = $pnrs[1];
		$array[] = $pfcr[1];
		$array[] = $csurveys;
		$array[] = $cvol * 100;
		$array[] = $cwtr[1];
		$array[] = $cnrs[1];
		$array[] = $cfcr[1];
		$array[] = $dsurveys;
		$array[] = $dvol * 100;
		$array[] = $dwtr;
		$array[] = $dnrs;
		$array[] = $dfcr;
		

		return $array;

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