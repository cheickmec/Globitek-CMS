<?php
  require_once('../private/initialize.php');
  
  // Set default values for all variables the page needs.
	$first_name	=	"";
	$last_name	=	"";
	$email			=	"";
	$username	=	"";
  // if this is a POST request, process the form
  // Hint: private/functions.php can help
	if(is_post_request()){
	 	$first_name	=	$_POST['first_name'];
		$last_name	=	$_POST['last_name'];
		$email			=	$_POST['email'];
		$username	=	$_POST['username'];
		$created_at = 	date("Y-m-d H:i:s");
		
      // Confirm that POST values are present before accessing them.
	   // Perform Validations
      // Hint: Write these in private/validation_functions.php
	  $errors = array();
	  
	  if(is_blank($first_name)){
		  array_push($errors, "First name cannot be blank.");
	  } elseif(!has_length($first_name,['min' => 2, 'max' => 255])){
		  array_push($errors, "First name must be between 2 and 255 characters.");
	  }
	  
	  if(is_blank($last_name)){
			array_push($errors, "Last name cannot be blank.");
	  } elseif(!has_length($last_name,['min' => 2, 'max' => 255])){
			array_push($errors, "Last name must be between 2 and 255 characters.");
	  }
	  
	  if(!has_valid_email_format($email)) {
		array_push($errors, "Invalid email format.");
	  }
	  
	  if(is_blank($username)){
			array_push($errors, "Username cannot be blank.");		  
	  } elseif(!has_length($username,['min' => 8, 'max' => 20])){
			array_push($errors, "Username must be between 8 and 20 characters.");
	  }
     
	 
		
	  // if there were no errors, submit data to database
	  if(sizeof($errors) != 0){
		  if(!preg_match('/\A[A-Za-z\s\-,\.\']+\Z/', $first_name) || !preg_match('/\A[A-Za-z\s\-,\.\']+\Z/', $last_name)){
			  array_push($errors, h("First Name and Last Name may only contains alphabetical characters, periods, spaces, commas, hyphens and apostrophes (single-quotes) "));
		  }
		  if(preg_match('/[^a-z_0-9]/i', $username)){
			  array_push($errors, h("Username may only contains alphanumeric characters and underscores"));
		  }
		  echo display_errors($errors);
	  }else{
		  
		  // Check if username is already being used
		  $sql = "SELECT username FROM users WHERE username = '$username'";
		  $exist = db_query($db, $sql);
		  $num_rows = mysqli_num_rows($exist);
		  
		  if($num_rows >= 1){
			 echo display_errors(array("This username is already being used")); 
		  }
		  else{
			// Write SQL INSERT statement
			$sql = "INSERT INTO users (first_name, last_name, email, username, created_at) VALUES ('$first_name', '$last_name', '$email', '$username', '$created_at')";
			$result = db_query($db, $sql);
			if($result) {
				db_close($db);
				redirect_to("registration_success.php");
			}	else {
					echo db_error($db);
					db_close($db);
					exit;
				}  
			}
		}
	}
?>

<?php $page_title = 'Register'; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<div id="main-content">
  <h1>Register</h1>
  <p>Register to become a Globitek Partner.</p>

  <?php
    // TODO: display any form errors here
    // Hint: private/functions.php can help
  ?>

  <!--HTML form-->
	<form action= "<?php echo h($_SERVER["PHP_SELF"]); ?>" method="POST">
	<table border="0">
	<tr>
			<td>First Name:	</td>	<td><input type= "text" name="first_name" value="<?php echo h($first_name); ?>"/>	</td>
	</tr>
	<tr>
			<td>Last Name:	</td>	<td><input type= "text" name="last_name" value="<?php echo h($last_name); ?>" />	</td>
	</tr>
			<td>E-mail:			</td>	<td><input type= "text" name="email" value="<?php echo h($email); ?>"/>			</td>
	</tr>
			<td>Username:	</td>	<td><input type= "text" name="username" value="<?php echo h($username); ?>" />	</td>
	</tr>
	<tr>
		<td><input type = "submit" name="submit" value = "Submit"> </td>
	</tr>
	</table>
	
	</form>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
