<?php
	// Index setup
	set_include_path( $_SERVER['DOCUMENT_ROOT'] );
	include_once "exchange/util/common.php";
	
	$page = "Results";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Freight Auditing Services">
    <meta name="author" content="John Espenhahn">

    <title>TDE - Results</title>

    <!-- Bootstrap Core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="/css/plugins/dataTables.bootstrap.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/css/sb-admin-2.css" rel="stylesheet">
	
	<!-- Exchange CSS -->
	<link href="/css/exchange.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	
	<!-- Date Picker -->
	<link href="/css/datepicker3.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>

<?php
	require_once "exchange/util/safearea.php";
?>

<div id="wrapper">	
	<?php
		require_once 'exchange/view/navbar.php';
	?>
	<div id="page-wrapper">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Results</h1>
			</div>
			<!-- /.col-lg-12 -->
		</div>	
		<!-- /.row -->

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						Search Results <?php echo (isset($_GET['reportName']) ? "For <b>". htmlspecialchars($_GET['reportName']) . "</b>" : ""); ?>
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body">
						<?php
							// Always include results
							require_once "exchange/util/results.php";
							getResults();
						?>
					</div>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->	
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<!-- /.row -->
	</div>
	<!-- /#page-wrapper -->	
</div>
<!-- /#wrapper -->

<!-- jQuery Version 1.11.1 -->
<script src="/js/jquery-1.11.1.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="/js/plugins/metisMenu/metisMenu.min.js"></script>

<!-- DataTables JavaScript -->
<script src="/js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="/js/plugins/dataTables/dataTables.bootstrap.js"></script>

<!-- Custom Theme JavaScript -->
<script src="/js/sb-admin-2.js"></script>

<script>
	function doTarget(file, idx) {
		document.options_form.action = file;
		document.getElementById("startIdx").value = idx;
		
		document.options_form.submit();
	}
</script>


	
</body>
</html>


