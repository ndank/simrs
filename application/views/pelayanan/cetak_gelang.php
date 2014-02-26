<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <title><?= $title ?></title>
      	<style type="text/css">
      		.table_gelang{
      			border: 1px solid ;
      			padding-left: 10px;
      			padding-right: 10px;
      		}
      	</style>
       
        <script type="text/javascript">
            function printit() {
               window.print();
               setTimeout(function(){ window.close();},300);
            }
        </script>
    </head> 
    <body onload="printit()">
    	<table class="table_gelang">
    		<tr>
    			<td><h3><b><?= $detail->no_rm ?></b></h3></td>
    			<td> <b>|</b> </td>
    			<td><h3><b><?= $detail->nama ?></b></h3></td>
    		</tr>
    	</table>
    </body>
</html>