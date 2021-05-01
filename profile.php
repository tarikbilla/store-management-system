<?php require_once('header.php');?>
<?php 
$error="";
//get value from database
if (isset($_SESSION['username'])) {
  foreach ($db->query("SELECT * FROM members WHERE username='".$_SESSION['username']."'") as $row) {
      $uid = $row['memberID'];
      $rules = $row['rules'];
      $username = $row['username'];
      $password = $row['password'];
      $email = $row['email'];
      $phone_no = $row['phone_no']; 
      $profile_pic_path = $row['profile_pic_path']; 
      $first_name = $row['first_name']; 
      $last_name = $row['last_name']; 
      $gender = $row['gender']; 
      $address = $row['address']; 
  }
}



//if form has been submitted process it
if(isset($_POST["submit"]) && isset($_SESSION['username'])){

  try {

      //insert into database with a prepared statement
      $stmt = $db->prepare('UPDATE members SET rules=:ruless, first_name=:first_name, last_name=:last_name, gender=:gender, address=:address, email=:email, phone_no=:phone_no WHERE username=:username');

      $stmt->execute(array(
      'username' => $_SESSION['username'],
      'ruless' => $_POST["userroles"],
      'first_name' => $_POST["fname"],
      'last_name' => $_POST["lname"],
      'gender' => $_POST["gender"],
      'address' => $_POST['address'],
      'email' => $_POST['email'],
      'phone_no' => $_POST['phone_no']
      ));


      //for upload picture
      if($_FILES["fileField"]["size"] < 500000) {
        $newname ='img/profile_pic/'.$username.'.jpg';
        move_uploaded_file( $_FILES['fileField']['tmp_name'], $newname);
        //insert into database with a prepared statement
        $db->query("UPDATE members SET profile_pic_path='$newname' WHERE username='".$_SESSION['username']."'");

      }
      
      //update log data
      setLogData($db, "-", "Update Profile <a href='update-profile.php?id=".$uid."'>". $_SESSION['username']."</a>");

      //redirect to index page
      header('Location: ?action=success');
      exit;




    //else catch the exception and show the error.
    } catch(PDOException $e) {
        $error[] = $e->getMessage();
    }

}
 ?>
        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800">Profile</h1>

          <div class="row">
            
            <div class="col-lg-6 card">
              <div class="card-body">
                <div class="text-center">
                  <img src="<?php if ($profile_pic_path) {echo $profile_pic_path;}else{echo 'img/user_default.png';} ?>" class="rounded-circle" height="150" width="150"><br>
                  <div class="h4 mt-3"><?php if (isset($_SESSION['username'])) {echo $first_name." ".$last_name.'   ('.$rules.')';} ?></div>
                </div>
                    
                    <table class="table table-hover ">
                      <tr>
                        <td class="">UserName</td>
                        <td>: <?php if (isset($_SESSION['username'])) {echo $username;} ?></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td>: <?php if (isset($_SESSION['username'])) {echo $email;} ?></td>
                      </tr>
                      <tr>
                        <td>Phone Number</td>
                        <td>: <?php if (isset($_SESSION['username'])) {echo $phone_no;} ?></td>
                      </tr>
                      <tr>
                        <td>Address</td>
                        <td>: <?php if (isset($_SESSION['username'])) {echo $address;} ?></td>
                      </tr>
                    </table>
              </div>
            </div>

            <div class="col-lg-6 card">
              <div class="card-body">
                <?php 
                  //if action is succes show sucess
                  if(isset($_GET['action']) && $_GET['action'] == 'success'){
                    echo "<p class='h3 text-success'>Update Successful.</p>";
                  } 
                ?>
                <div class="h4 mb-1 text-primary">Update Profile</div>
                <form class="was-validated" role="form" enctype="multipart/form-data" method="post" action="">
                  <div class="form-group row">
                    <div class="col-sm-6 mb-1 mb-sm-0">
                    <label for="firstName">First Name</label>
                        <input type="text" name="fname" class="form-control form-control-user" id="" placeholder="First Name" value="<?php if (isset($_SESSION['username'])) {echo $first_name;} ?>" required>
                    </div>
                    <div class="col-sm-6">
                    <label for="lastName">Last Name</label>
                        <input type="text" name="lname" class="form-control form-control-user" id="" placeholder="Last Name"  value="<?php if (isset($_SESSION['username'])) {echo $last_name;} ?>" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-6">
                    <label for="lastName">Username</label>
                        <input type="text" name="username" class="form-control form-control-user" id="" placeholder="Username"  value="<?php if (isset($_SESSION['username'])) {echo $username;} ?>" title="Username never change" readonly>
                    </div>
                    <div class="col-sm-6 mb-1 mb-sm-0">
                    <label for="firstName">Email</label>
                        <input type="email" name="email" class="form-control form-control-user" id="" placeholder="Email Address"  value="<?php if (isset($_SESSION['username'])) {echo $email;} ?>" required>
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col-sm-6 mb-1 mb-sm-0">
                    <label for="phone_no">Phone No</label>
                        <input type="text" name="phone_no" class="form-control form-control-user" id="" placeholder="Phone Number"  value="<?php if (isset($_SESSION['username'])) {echo $phone_no;} ?>" required>
                    </div>
                    <div class="col-sm-6 mb-1 mb-sm-0">
                    <label for="gender">Gender</label>
                        <select name="gender" class="custom-select" required>
                        <option value="">Select</option>
                        <option value="male" <?php if (isset($_SESSION['username'])) {if ($gender=="male") {echo 'selected';}} ?>>Male</option>
                        <option value="Female" <?php if (isset($_SESSION['username'])) {if ($gender=="Female") {echo 'selected';}} ?>>Female</option>
                        <option value="other" <?php if (isset($_SESSION['username'])) {if ($gender=="other") {echo 'selected';}} ?>>Other</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col-sm-12 mb-1 mb-sm-0">
                    <label for="address">Address</label>
                      <input type="text" name="address" class="form-control form-control-user" id="" placeholder="Address"  value="<?php if (isset($_SESSION['username'])) {echo $address;} ?>" required>
                    </div>
                  </div>
                  

                  <div class="form-group row">
                    <div class="col-sm-6">
                    <label for="userRulse">User Rules</label>
                      <select name="userroles" class="custom-select" required>
                        <option value="" >Select Rules</option>
                        <option value="engineer" <?php if ($rules=='engineer') {echo "selected";} ?>>Engineer</option>
                        <option value="admin" <?php if ($rules=='admin') {echo "selected";} ?>>Admin</option>
                        <option value="other"  <?php if ($rules=='other') {echo "selected";} ?>>Other</option>
                      </select>
                    </div>
                    <div class="col-sm-6">
                    <label for="profile">Profile Picture</label>
                      <input name="fileField" type="file" size="42">
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <input type="submit" name="submit" value="Update" class="btn btn-primary btn-user btn-block">
                    </div>
                  </div>
                </form>
              </div>
            </div>

          </div>
        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

<?php require_once('footer.php');?>