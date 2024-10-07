<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('includes/config.php');

if(strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
} else { 

if(isset($_POST['add'])) {
    $ebookname = $_POST['ebookname'];
    $category = $_POST['category'];
    $author = $_POST['author'];
    $ebookurl = $_POST['ebookurl'];
    $isbn = $_POST['isbn'];
    $ebookimg = $_FILES["ebookpic"]["name"];

    // Get the image extension
    $extension = substr($ebookimg, strlen($ebookimg) - 4, strlen($ebookimg));

    // Allowed extensions
    $allowed_extensions = array(".jpg", "jpeg", ".png", ".gif");

    // Rename the image file
    $imgnewname = md5($ebookimg.time()) . $extension;

    // Check for allowed image formats
    if(!in_array($extension, $allowed_extensions)) {
        echo "<script>alert('Invalid format. Only jpg / jpeg / png / gif format allowed');</script>";
    } else {
        // Move image into the directory
        if(move_uploaded_file($_FILES["ebookpic"]["tmp_name"], "ebookimg/" . $imgnewname)) {
            // Insert into the database
            $sql = "INSERT INTO tbl_ebooks (eBookName, CatId, AuthorId, eBookURL, ISBNNumber, eBookImage) 
                    VALUES (:ebookname, :category, :author, :ebookurl, :isbn, :imgnewname)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':ebookname', $ebookname, PDO::PARAM_STR);
            $query->bindParam(':category', $category, PDO::PARAM_STR);
            $query->bindParam(':author', $author, PDO::PARAM_STR);
            $query->bindParam(':ebookurl', $ebookurl, PDO::PARAM_STR);
            $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
            $query->bindParam(':imgnewname', $imgnewname, PDO::PARAM_STR);

            try {
                $query->execute();
                $lastInsertId = $dbh->lastInsertId();
                if($lastInsertId) {
                    echo "<script>alert('eBook listed successfully');</script>";
                    echo "<script>window.location.href='manage-ebooks.php'</script>";
                } else {
                    echo "<script>alert('Something went wrong. Please try again');</script>";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "<script>alert('File upload failed');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Add eBook</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
    <!------MENU SECTION START-->
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
         <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Add eBook</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">eBook Info</div>
                        <div class="panel-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                
                                <div class="col-md-6">   
                                    <div class="form-group">
                                        <label>eBook Name<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="ebookname" autocomplete="off" required />
                                    </div>
                                </div>

                                <div class="col-md-6">  
                                    <div class="form-group">
                                        <label>Category<span style="color:red;">*</span></label>
                                        <select class="form-control" name="category" required="required">
                                            <option value="">Select Category</option>
                                            <?php 
                                            $status=1;
                                            $sql = "SELECT * from tblcategory where Status=:status";
                                            $query = $dbh -> prepare($sql);
                                            $query -> bindParam(':status', $status, PDO::PARAM_STR);
                                            $query->execute();
                                            $results=$query->fetchAll(PDO::FETCH_OBJ);
                                            if($query->rowCount() > 0) {
                                                foreach($results as $result) { ?>  
                                                    <option value="<?php echo htmlentities($result->id);?>">
                                                        <?php echo htmlentities($result->CategoryName);?>
                                                    </option>
                                            <?php }} ?> 
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">  
                                    <div class="form-group">
                                        <label>Author<span style="color:red;">*</span></label>
                                        <select class="form-control" name="author" required="required">
                                            <option value="">Select Author</option>
                                            <?php 
                                            $sql = "SELECT * from tblauthors";
                                            $query = $dbh -> prepare($sql);
                                            $query->execute();
                                            $results=$query->fetchAll(PDO::FETCH_OBJ);
                                            if($query->rowCount() > 0) {
                                                foreach($results as $result) { ?>  
                                                    <option value="<?php echo htmlentities($result->id);?>">
                                                        <?php echo htmlentities($result->AuthorName);?>
                                                    </option>
                                            <?php }} ?> 
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">   
                                    <div class="form-group">
                                        <label>eBook URL<span style="color:red;">*</span></label>
                                        <input class="form-control" type="url" name="ebookurl" autocomplete="off" required />
                                    </div>
                                </div>

                                <div class="col-md-6">   
                                    <div class="form-group">
                                        <label>ISBN Number<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="isbn" autocomplete="off" required />
                                        <p class="help-block">An ISBN is an International Standard Book Number. ISBN must be unique.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">   
                                    <div class="form-group">
                                        <label>eBook Picture<span style="color:red;">*</span></label>
                                        <input class="form-control" type="file" name="ebookpic" autocomplete="off" required />
                                    </div>
                                </div>

                                <button type="submit" name="add" class="btn btn-info">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>
    <!-- CONTENT-WRAPPER SECTION END-->
    <?php include('includes/footer.php');?>
    <!-- FOOTER SECTION END-->
    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?> 

