
<?php
	$parents = '';
	$children = '';
	$spouse = '';
	$spouse_close = '';
	$parents_close = '';
	$currentID = '';

	function getPerson($pid,$c = false, $debug = false) {
		include('includes/db.php');
		include('includes/includes/db.php');
		$sql = 'SELECT prefix, first_name, middle_name, last_name, suffix, gender, deceased FROM persons WHERE personid='.$pid.';';
		$result = pg_query($conn, $sql);
		$data = pg_fetch_assoc($result);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('<br />Error! (getPerson)<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Data: '.$data.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		
		
		$name = addStrTogether($data['prefix'],$data['first_name']);
		$name = addStrTogether($name,$data['middle_name']);
		$name = addStrTogether($name,$data['last_name']);
		$name = addStrTogether($name,$data['suffix']);
		
		if ($data['deceased'] == 't') {
			$gender = 'deceased';
		} else if ($data['gender'] == 'female') {
			$gender = 'woman';
		} else if ($data['gender'] == 'male') {
			$gender = 'man';
		} else {
			$gender = 'other';
		}
		pg_close($conn);
		
		$link = '"name": "<a href=\"/Profile/?p='.encrypt($pid).'\">'.$name.'</a>",'.
			'"class": "'.$gender.'"';
		if ($c) {
			$link .= ',
			"textClass": "emphasis"';
		}
		return $link;
	}

	
	function Child($other) {
		$child = getPerson($other);
		
		$return = '{'.$child.'}';
		
		return $return;
	}

	function AddtoChildren($children,$new) {
		if (strlen($children) > 1) {
			$return = $children.', '.$new;
		} else {
			$return = $new;
		}
		return $return;
	}

	function getSpouse($other, $debug = false) {
		include('includes/db.php');
		include('includes/includes/db.php');
		$sql1 = 'SELECT personid2 FROM relationships WHERE personid1='.$other.' AND relationship=3;';
		$result1 = pg_query($conn, $sql1);
		$data1 = pg_fetch_assoc($result1);
		if ($debug) {
			$error1 = pg_last_error($conn);
			if ($error1) {
				echo('<br />Error! (getSpouse)<br />');
				echo('SQL: '.$sql1.'<br />');
				echo('Result: '.$result1.'<br />');
				echo('Data: '.$data1.'<br />');
				echo('Error: '.$error1.'<br />');
			}
		}
		
		$other = $data1['personid2'];
		if ($other) {
			$sql = 'SELECT prefix, first_name, middle_name, last_name, suffix, gender, deceased FROM persons WHERE personid='.$other.';';
			$result = pg_query($conn, $sql);
			$data = pg_fetch_assoc($result);
			if ($debug) {
				$error = pg_last_error($conn);
				if ($error) {
					echo('<br />Error! (getSpouse2)<br />');
					echo('SQL: '.$sql.'<br />');
					echo('Result: '.$result.'<br />');
					echo('Data: '.$data.'<br />');
					echo('Error: '.$error.'<br />');
				}
			}
			pg_close($conn);

			$name = addStrTogether($data['prefix'],$data['first_name']);
			$name = addStrTogether($name,$data['middle_name']);
			$name = addStrTogether($name,$data['last_name']);
			$name = addStrTogether($name,$data['suffix']);

			if ($data['deceased'] == 't') {
				$gender = 'deceased';
			} else if ($data['gender'] == 'female') {
				$gender = 'woman';
			} else if ($data['gender'] == 'male') {
				$gender = 'man';
			} else {
				$gender = 'other';
			}

			$link = '"marriages": [{
					"spouse": {
						"name": "<a href=\"/Profile/?p='.encrypt($other).'\">'.$name.'</a>",'.
						'"class": "'.$gender.'"}';
		} else {
			$link = '"marriages": [{
					"spouse": {
						"name": "Unknown",'.
						'"class": "unknown"}';
		}
		
		return $link;
	}

	function getParentsOtherChildren($other,$debug = false) {
		global $currentID;
		include('includes/db.php');
		$sql = "SELECT personid2 FROM relationships WHERE personid1='$other' AND relationship = '2';";
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('<br />Error! (getRelations)<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
		
		while ($row = pg_fetch_assoc($result)) {
			$other = $row['personid2'];
			$relationship = $row['relationship'];
			if ($other == $currentID) {
			} else if ($other == $previous) {
			} else {
				$children = ','.AddtoChildren($children, Child($other));
			}
		}
		return $children;
	}

	function Parent($other) {
		global $parents, $parents_close;
		
		$parent = getPerson($other);
		$spouse = getSpouse($other,true);
		
		$parents = $parent.',
  				'.$spouse.',
				"children": [{';
		$siblings = getParentsOtherChildren($other);
		if (strlen($siblings) > 0) {
			$siblings;
		}
		$parents_close = '}'.$siblings.']}]';
	}
	
	function Spouse($other) {
		global $spouse,$spouse_close;
		$s = getPerson($other);
		
		$spouse = ',
  			"marriages": [{
				"spouse": {
					'.$s.'
				}';
		$spouse_close = '}]';
	}

	function logic($relationship,$other) {
		global $children;
		switch ($relationship) {
			case 1:
				Parent($other);
				break;
			case 2:
				$children = AddtoChildren($children, Child($other));
				break;
			case 3:
				Spouse($other);
				break;
		}
	}

	function getRelations($pid,$debug) {
		global $parents, $children, $spouse, $spouse_close, $parents_close;
		$parents = '';
		$children = '';
		$spouse = '';
		$spouse_close = '';
		$parents_close = '';
		$p = getPerson($pid,true,true);
		
		include('includes/db.php');
		$sql = 'SELECT personid2, relationship FROM relationships WHERE personid1='.$pid.';';
		$result = pg_query($conn, $sql);
		if ($debug) {
			$error = pg_last_error($conn);
			if ($error) {
				echo('<br />Error! (getRelations)<br />');
				echo('SQL: '.$sql.'<br />');
				echo('Result: '.$result.'<br />');
				echo('Error: '.$error.'<br />');
			}
		}
		pg_close($conn);
		
		while ($row = pg_fetch_assoc($result)) {
			$other = $row['personid2'];
			$relationship = $row['relationship'];
			if ($other == $previous) {
			} else {
				logic($relationship,$other);
			}
		}
		
		$noCtext = $parents.$p.$spouse;
		
		if (strlen($children) > 0) {
			$Ctext = ',
        			"children": ['.$children.']';
		}
		$fullText = $noCtext.$Ctext.$spouse_close.$parents_close;
		
		return $fullText;
	}

	function buildTree($pid) {
		global $currentID;
		$currentID = $pid;
		//echo("PID: ".$pid."<br/>");
		$treeData = getRelations($pid,true);
		
		$tree = '
		<script type="text/javascript" src="../../js/tree/d3.v4.min.js"></script>
		<script type="text/javascript" src="../../js/tree/dTree.min.js"></script>
		<script type="text/javascript" src="../../js/tree/lodash.min.js"></script>
		<script type="text/javascript">treeData = [{'.$treeData.'}]
		
		dTree.init(treeData, {
			target: "#graph",
			debug: true,
			height: 500,
			width: 290,
			callbacks: {
				nodeClick: function(name, extra) {
					console.log(name);
				}
			}
		});
		</script>';
		
		echo('<div id="graph"></div>');
		echo($tree);
		
	}
?>
        			
		