<?php
	function getResults() {
		require_once 'exchange/util/FieldsFactory.php';
		require_once 'exchange/util/db.php';
		require_once 'exchange/util/report_query.php';
		
		$showButtons = isset($_GET['showButtons']) ? $_GET['showButtons'] != 'no' : true;
	
		$startIdx = isset($_GET['startIdx']) ? intval($_GET['startIdx']) : 0;
		$reportId = isset($_GET['reportId']) ? intval($_GET['reportId']) : NULL;
		$reportName = isset($_GET["reportName"]) ? $_GET["reportName"] : "";
		$query = prepareReportQuery($reportId, $startIdx);
		
		if ($query == NULL) {
			return null;
		}
		
		// Add limit statement to $query
		$TARG_ROWS = isset($_GET['maxResults']) ? min(intval($_GET['maxResults']) + 1, 501) : 21;
		$query->push("LIMIT ?,", $startIdx, 'i');
		$query->push("?", $TARG_ROWS, 'i');
		
		// echo $query;
		$result = runReportQuery($query);
		
		if ($result->numRows == 0) {
			$result->close();
			
			echo "<div class='centred'>";
			echo "	<p class='error'>No results found</p>";
			if ($showButtons) {
				echo "<a href='/exchange/reports/search/?reportId=" . urlencode($reportId) . "&reportName=" . urlencode($reportName) . "' class='btn btn-default active' role='button'>New Search</a>";
			}
			echo "</div>";
			
			return null;
		}
		
		// Display table
		echo "<table class='table table-striped table-bordered table-hover' id='table'>";
		echo "<thead><tr><th>Index</th>";
		
		// Output format:
		//   [ id, id, ... ]
		$fields = FieldsFactory::get();
		$outputs = $query->getOutputs();		
		foreach ($outputs as $id) {	
			if(!isset($fields[$id])) {
				return error("Corrupt report output definition!");
			} else if (!$fields[$id]->validateInput(null)) {
				return error("Corrupt report output definition!");
			}
				
			$field = $fields[$id];
			echo "<th>" . formatForTable($field->getFormattedTitle()) . "</th>";
		}
		echo "</tr></thead>";
		
		// Display all but last row
		echo "<tbody>";
		for ($idx = 1; $idx < $TARG_ROWS && $result->fetch(); $idx++) {
			echo "<tr>";
			echo "<td>" . ($startIdx + $idx) . "</td>";
			
			// Write the fields that were used
			foreach ($result->getArray() as $id => $val) {
				$field = $fields[$id];
				if (!$field->validateInput($val)) {
					return error("Corrupt report output definition!");
				}
			
				echo "<td>" . $field->formatResult($val) . "</td>";
			}
			
			echo "</tr>";
		}
		
		// End table
		echo "</tbody></table>";
		echo "</table>";
		
		// Option buttons		
		echo "<form name='options_form' method='get'>
			  <input type='hidden' value='0' id='startIdx' name='startIdx'>
			  <input type='hidden' value='" . htmlspecialchars($reportId) . "' name='reportId'>
			  <input type='hidden' value='" . htmlspecialchars($reportName) . "' name='reportName'>
			  <input type='hidden' value='" . ($TARG_ROWS - 1) . "' name='maxResults'>";
			 
		// If is min must include input
		if (!isNotMin()) {
			echo "<input type='hidden' value='true' name='min'>";
		}
		
		if (!$showButtons) {
			echo "<input type='hidden' value='no' name='showButtons'>";
		}
		
		// Keep track of all other fields if needed
		foreach ($fields as $id => $field) {
			if (isset($_GET[$id]) && isset($_GET['WHERE_' . $id])) {				
				echo "<input type='hidden' value='" . htmlspecialchars($_GET[$id]) . "' name='$id'>";	
				echo "<input type='hidden' value='" . htmlspecialchars($_GET["WHERE_" . $id]) . "' name='WHERE_$id'>";
			}
			
			if (isset($_GET['TOP_' . $id])) {
				echo "<input type='hidden' value='" . htmlspecialchars($_GET["TOP_" . $id]) . "' name='TOP_$id'>";
			}
		}
		
		echo "
			<div class='row'>
				<div class='col-sm-6 result-col'>
					<div class='dataTables_info'>";
		
		// New button
		if ($showButtons) {
			echo "		<a href='/exchange/reports/search/?reportId=" . urlencode($reportId) . "&reportName=" . urlencode($reportName) . "' class='btn btn-default active' role='button'>New</a>
						<button type='submit' class='btn btn-warning active' onClick='doTarget(\"/exchange/reports/search/results/export-csv/\", 0)'>Export As CSV</button>";
		}
		// End new button
		
		echo "		</div>
				</div>
				<div class='col-sm-6 result-col'>
					<div class='dataTables_info'></div>";
		
		// Previous and Next buttons
			echo "	<div class='dataTables_paginate pagin_simple_numbers'>
						<ul class='pagination'>
							<li class='paginate_button previous' id='previous'>";
								// Previous
								if ($startIdx > 0) {
									$newIdx = $startIdx - ($TARG_ROWS - 1);
									if ($newIdx < 0) $newIdx = 0;
									
									echo "<a href='javascript:void(0)' class='btn btn-default active' onClick='doTarget(\"/exchange/reports/search/results/#table\", \"$newIdx\")'>Previous</a>";
								} else {
									echo "<a href='javascript:void(0)' class='btn btn-default' disabled='disabled'>Previous</a>";
								}
			echo "			</li>
							<li class='paginate_button next' id='next'>";
								 // Next
								if ($result->numRows == $TARG_ROWS) {
									$newIdx = $startIdx + ($TARG_ROWS - 1);
									echo "<a href='javascript:void(0)' class='btn btn-default active' onClick='doTarget(\"/exchange/reports/search/results/#table\", \"$newIdx\")'>Next</a>";
								} else {
									echo "<a href='javascript:void(0)' class='btn btn-default' disabled='disabled'>Next</a>";
								}
			echo "			</li>
						</ul>
					</div>";		
		// end previous and next buttons
		
		echo "	</div>
			</div>
			";
		
		$result->close();
	}
	
	/**
	 * @param {string} $str String to format for the table
	 * @return {string} The formatted $str
	*/
	function formatForTable($str) {
		return str_replace("-", "&#8209;", $str);
	}
?>