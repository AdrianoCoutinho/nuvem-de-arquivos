<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>Painel de Arquivos</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/component.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<style>
	*{margin:0;}
	#nova-pasta{font-weight: 700; color: #000; padding: 0; text-decoration: none;}
	.btn-abrir{width:89px;}
	.btn a{text-decoration: none; color:#000000;}
	.glyphicon-download{display: inline-block; padding: 6px 17px 0 12px; line-height: 1.42857143; vertical-align: middle;}
	.listfolder{display: inline-block; padding: 6px 12px; line-height: 1.42857143; vertical-align: middle;}
	.glyphicon-folder-close{margin: 5px auto;}
	.glyphicon-duplicate{margin: 5px auto;}
	.namearq{margin: 5px auto;}
</style>
</head>
<body>
<div class="container">
<?php
session_start();
	
	if( $_SERVER['REQUEST_METHOD']=='POST' )
	{
		$request = md5( implode( $_POST ) );
		
		if( isset( $_SESSION['last_request'] ) && $_SESSION['last_request']== $request )
		{
			echo 'refresh';
		}
		else
		{
			$_SESSION['last_request']  = $request;
			echo 'post';
		}
	}	

function formatBytes($size, $precision = 0){
    $unit = ["B", "KB", "MB", "GB", "TB"];

    for($i = 0; $size >= 1024 && $i < count($unit)-1; $i++){
        $size /= 1024;
    }

    return round($size, $precision).' '.$unit[$i];
}
$hd = disk_total_space("C:");
	
function flash_encode($in) 
{ 
  $out = ''; 
  for ($i=0;$i<strlen($in);$i++) 
  { 
    $hex = dechex(ord($in[$i])); 
    if ($hex=='') 
       $out = $out.urlencode($in[$i]); 
    else 
       $out = $out .'%'.((strlen($hex)==1) ? ('0'.strtoupper($hex)):(strtoupper($hex))); 
  } 
  $out = str_replace('+','%20',$out); 
  $out = str_replace('_','%5F',$out); 
  $out = str_replace('.','%2E',$out); 
  $out = str_replace('-','%2D',$out); 
  return $out; 
}	

	

	
$baseDir = 'uploads/';
$abreDir = ($_GET['dir'] != '' ? $_GET['dir'] : $baseDir);	
$openDir = dir($abreDir);
$strrdir = strrpos(substr($abreDir,0,-1),'/');
$backDir = substr($abreDir,0,$strrdir+1);

echo '<table class="table table-condensed table-bordered text-center">';
echo '<tr><td><a id="nova-pasta" href="#" data-toggle="modal" data-target="#modaldir">Nova pasta</a></td><td><form id="upload" name="upload" enctype="multipart/form-data" method="post" action="upload.php" autocomplete="off" style=" display: inline; ">
<input type="hidden" name="MAX_FILE_SIZE" value="1099511627776">     
<input onchange="this.form.submit()" type="file" name="arquivo[]" id="file" class="inputfile" data-multiple-caption="{count} arquivos selecionados" multiple /><label for="file"><span>Upload de arquivo</span></label>
</form></td> <td><form id="upload" name="upload" enctype="multipart/form-data" method="post" action="upload.php" autocomplete="off" style=" display: inline; ">
<input type="hidden" name="MAX_FILE_SIZE" value="1099511627776">     
<input onchange="this.form.submit()" type="file" name="arquivo[]" id="file" class="inputfile" data-multiple-caption="{count} arquivos selecionados" multiple /><label for="file"><span>Upload de pasta</span></label>
</form></td><td style="
    font-weight: 700;
">Espaço Livre '.formatBytes($hd, 2).'</td></tr>';
echo '<tr><td><br></td><td><br></td><td><br></td><td><br></td></tr>';	
echo '<td>Tipo</td><td>Nome</td><td>Tamanho</td><td>Ação</td>';
	
while($arq = $openDir -> read()):

	if($arq != '.' && $arq != '..'):
	if(is_dir($abreDir.$arq))
	{
		//<a id="nova-pasta" href="#" data-toggle="modal" data-target="#modaldir">Nova pasta</a>
		echo '<tr>';
		echo '<td><i class="glyphicon glyphicon-folder-close"></i></td>';
		echo '<td><p class="namearq">'.substr($arq, 0, 30).'</p></td>';
		echo '<td></td>';
		echo '<td class="col-sm-2"><span class="glyphicon glyphicon-folder-open listfolder"></span>
		<div class="btn-group">
			
			<input type="button" class="btn btn-default btn-abrir" value="Abrir" onclick="location.href = \'index.php?dir='.$abreDir.flash_encode($arq).'/\';">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			<span class="caret">
			</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="#"><span class="glyphicon glyphicon-paste"></span> Mover</a></li>
				<li><a data-toggle="modal" data-target="#'.pathinfo($arq, PATHINFO_FILENAME).'" href=""><span class="glyphicon glyphicon-erase"></span> Renomear</a></li>
				<li><a href="rmdir.php?dir='.$abreDir.flash_encode($arq).'/"><span class="glyphicon glyphicon-trash"></span> Excluir</a</li>
				
			</ul>
		</div>
		
          
		  </td>';
		echo '</tr>';
		echo '
		
<div class="modal fade " id="'.pathinfo($arq, PATHINFO_FILENAME).'" role="dialog">
    <div class="modal-dialog modal-dialog-centered  modal-sm">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title text-center">Renomear pasta</h4>
        </div>
        <div class="modal-body text-center">
			<form action="renomearpast.php?dir='.$abreDir.$arq.'" method="post">
				<div class="input-group">
			  		<span class="input-group-addon"><i class="glyphicon glyphicon-folder-open"></i></span>
			  		<input id="nomepasta" type="text" class="form-control" name="newpasta" placeholder="Novo nome da pasta">
				</div>
				<br>
				<input type="submit" class="btn btn-block" value="Confirmar">
			</form>
        </div>
       
      </div>
      
    </div>
</div>';	
	}
	else
	{
		echo '<tr>';
		echo '<td><span class="glyphicon glyphicon-duplicate"></span></td>';
		echo '<td><p class="namearq">'.substr($arq, 0, 30).'</p></td>';
		$bytes = filesize($abreDir.$arq);
		echo '<td><p class="namearq">'.formatBytes($bytes, 2).'</p></td>';
		echo '<td><span class="glyphicon glyphicon-download"></span><div class="btn-group">
			<input type="button" class="btn btn-default" value="Download" onclick="location.href = \''.$abreDir.flash_encode($arq).'\';">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			<span class="caret">
			</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a href="#"><span class="glyphicon glyphicon-paste"></span> Mover</a></li>
				<li><a data-toggle="modal" data-target="#'.md5($arq).'" href=""><span class="glyphicon glyphicon-erase"></span> Renomear</a></li>
				<li><a href="unlink.php?dir='.$abreDir.flash_encode($arq).'"><span class="glyphicon glyphicon-trash"></span> Excluir</a</li>
				
			</ul>
		</div></td>';
		echo '</tr>';
		echo '
		
<div class="modal fade " id="'.md5($arq).'" role="dialog">
    <div class="modal-dialog modal-dialog-centered  modal-sm">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title text-center">Renomear</h4>
        </div>
        <div class="modal-body text-center">
			<form action="renamearq.php?dir='.$abreDir.$arq.'" method="post">
				<div class="input-group">
			  		<span class="input-group-addon"><i class="glyphicon glyphicon-folder-open"></i></span>
			  		<input id="nomepasta" type="text" class="form-control" name="newarq" placeholder="Novo nome do arquivo">
				</div>
				<br>
				<input type="submit" class="btn btn-block" value="Confirmar">
			</form>
        </div>
       
      </div>
      
    </div>
</div>';
	}
	endif;

endwhile;

echo '</table>';
if($abreDir != $baseDir)
{
	echo '<a href="index.php?dir='.$backDir.'"><button type="button" class="btn btn-default btn-sm">
          <span class="glyphicon glyphicon-chevron-left"></span> Voltar
        </button></a>';
}	
	
$openDir->close();
	
?>
<div class="modal fade " id="modaldir" role="dialog">
    <div class="modal-dialog modal-dialog-centered  modal-sm">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title text-center">Nova pasta</h4>
        </div>
        <div class="modal-body text-center">
			<form action="mkdir.php?dir=<?php echo $abreDir;?>" method="post">
				<div class="input-group">
			  		<span class="input-group-addon"><i class="glyphicon glyphicon-folder-open"></i></span>
			  		<input id="nomepasta" type="text" class="form-control" name="pasta" placeholder="Nome da pasta">
				</div>
				<br>
				<input type="submit" class="btn btn-block" value="Criar">
			</form>
        </div>
       
      </div>
      
    </div>
</div>

<script src="js/custom-file-input.js"></script>
	</body>
</html>

