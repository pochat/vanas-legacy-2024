<html>
<head>
	<script src="../ckeditor.js"></script>
	<link rel="stylesheet" href="css/samples.css">
</head>
<body id="main">
 
<main>
<br><br>
  

		<div class="grid-container">
			<div class="grid-width-100">
     
      <form>
				<textarea id="editorID" name="editor">
					
				</textarea>
        <input type="submit">
        <?php 
  echo isset($_GET['editor']) ? $_GET['editor'] : 'nellll'
  ?>
        </form>
			</div>
		</div>

</main>

<script>
   CKEDITOR.replace( 'editorID' ,{ 
    // Rutas para file manager
    filebrowserBrowseUrl : '../responsive_filemanager/filemanager/dialog.php?type=2&editor=ckeditor&fldr=', 
    filebrowserUploadUrl : '../responsive_filemanager/filemanager/dialog.php?type=2&editor=ckeditor&fldr=', 
    filebrowserImageBrowseUrl : '../responsive_filemanager/filemanager/dialog.php?type=1&editor=ckeditor&fldr=' 
  });
</script>

</body>
</html>