<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
  <title>Starter Template - Materialize</title>

  <!-- CSS  -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
  <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
  <nav class="waves-effect waves-light-blue lighten-1" role="navigation">
    <div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo">Introducing...</a>
      <ul class="right hide-on-med-and-down">
        <li><a href="https://en.wikipedia.org/wiki/Google_Cloud_Print">Learn More</a></li>
      </ul>

      <ul id="nav-mobile" class="side-nav">
        <li><a href="https://en.wikipedia.org/wiki/Google_Cloud_Print">Learn More</a></li>
      </ul>
      <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
    </div>
  </nav>
  
    <div class="nav-wrapper container">
      <br>
      <h3 class="header center black-text">SMART PRINT</h3>
      <div class="row center">
        <h5 class="header center black-text">A CLOUD PRINT SYSTEM FOR ORGANIZATIONS</h5>
      </div> 
    </div>
<form method="post" enctype="multipart/form-data" action="locbased.php">
 	 <div class="container">
  <div class="input-field col s6">
      <select name="nearuserprinter">
        <option value="" disabled selected>Choose Your Location </option>
        <option value="0">C Block</option>
        <option value="1">Library</option>
        <option value="2">ECE Block</option>
        <option value="3">K Block</option>
      </select>
    <label>Your Location</label>
  </div>
<div class="container">
  <div class="row">
        
          <div class="file-field input-field">
            <div class="btn">
              <span>Choose you file</span>
              <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
              <input type="file" name="userfile" id="userfile">
            </div>
            <div class="file-path-wrapper">
              <input class="file-path validate" type="text">
            </div>
          </div>
        
      <div class="col s5 offset-s1 btn-large waves-effect waves-light-blue lighten-1">
        <input name="upload" type="submit" id="upload" value="Print"></input>
      </div>
    </div>
  </div>
  </form>
  
       
      
	</div>

  
    <footer class="page-footer teal lighten-2">
    <div class="container">
      <div class="row">
        <div class="col l6 s12">
          <h5 class="white-text">About the Project</h5>
          <p class="grey-text text-lighten-4">Smart Print enables students to print documents from anywhere and any device. Students no longer need to carry storage devices and stand in queues.</p>


        </div>
        
       
      </div>
    </div>
    <div class="footer-copyright">
      <div class="container">
      Made by <a class="orange-text text-lighten-3" href="http://materializecss.com">Materialize</a>
      </div>
    </div>
  </footer> 
  


<!--  Scripts-->
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="js/materialize.js"></script>
  <script src="js/init.js"></script>
  <script>  $(document).ready(function() {
    $('select').material_select();
  });
       </script>
  </body>
</html>
